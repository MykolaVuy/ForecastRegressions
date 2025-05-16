<?php

namespace MykolaVuy\Forecast;

final class ForecastRegressor implements ForecastRegressorInterface
{
    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countLinearRegression(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $n = count($x);
            $sumX = $sumY = $sumXX = $sumXY = 0.0;

            for ($i = 0; $i < $n; $i++) {
                $xi = $x[$i];
                $yi = $y[$i];

                $sumX += $xi;
                $sumY += $yi;
                $sumXX += $xi * $xi;
                $sumXY += $xi * $yi;
            }

            $denominator = ($sumX ** 2 - $n * $sumXX);

            if (abs($denominator) < 1e-10) {
                return fn(int $key) => 0.0;
            }

            $a = ($sumX * $sumY - $n * $sumXY) / $denominator;
            $b = ($sumX * $sumXY - $sumXX * $sumY) / $denominator;

            return fn(int $key) => $a * $key + $b;
        }, $interpolateOnly);
    }

    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countPowerRegression(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $filteredX = [];
            $filteredY = [];

            for ($i = 0; $i < count($x); $i++) {
                if ($x[$i] > 0 && $y[$i] > 0) {
                    $filteredX[] = log($x[$i]);
                    $filteredY[] = log($y[$i]);
                }
            }

            $n = count($filteredX);
            if ($n < 3) return fn(int $key) => 0.0;

            $sumLogX = array_sum($filteredX);
            $sumLogY = array_sum($filteredY);
            $sumLogX2 = array_sum(array_map(fn($v) => $v ** 2, $filteredX));
            $sumLogXLogY = array_sum(array_map(fn($x, $y) => $x * $y, $filteredX, $filteredY));

            $denominator = ($n * $sumLogX2 - $sumLogX ** 2);
            if (abs($denominator) < 1e-10) {
                return fn(int $key) => 0.0;
            }

            $b = ($n * $sumLogXLogY - $sumLogX * $sumLogY) / $denominator;
            $a = exp(($sumLogY - $b * $sumLogX) / $n);

            return fn(int $key) => $a * ($key ** $b);
        }, $interpolateOnly);
    }

    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countLogarithmicRegression(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $filteredX = [];
            $filteredY = [];

            for ($i = 0; $i < count($x); $i++) {
                if ($x[$i] > 0) {
                    $filteredX[] = log($x[$i]);
                    $filteredY[] = $y[$i];
                }
            }

            $n = count($filteredX);
            if ($n < 3) return fn(int $key) => 0.0;

            $sumLogX = array_sum($filteredX);
            $sumLogX2 = array_sum(array_map(fn($v) => $v ** 2, $filteredX));
            $sumY = array_sum($filteredY);
            $sumYLogX = array_sum(array_map(fn($x, $y) => $x * $y, $filteredX, $filteredY));

            $denominator = ($n * $sumLogX2 - $sumLogX ** 2);
            if (abs($denominator) < 1e-10) {
                return fn(int $key) => 0.0;
            }

            $b = ($n * $sumYLogX - $sumLogX * $sumY) / $denominator;
            $a = ($sumY - $b * $sumLogX) / $n;

            return fn(int $key) => $a + $b * log($key);
        }, $interpolateOnly);
    }

    /**
     * @param array<int, float|null> $data
     * @param callable $regressionCallback
     * @param bool $interpolateOnly
     * @return array<int, float|null>
     */
    private function fillForecast(array $data, callable $regressionCallback, bool $interpolateOnly = false): array
    {
        $known = array_filter($data, fn($v) => $v !== null);
        if (count($known) < 3) return $data;

        $x = array_keys($known);
        $y = array_values($known);

        $minX = min($x);
        $maxX = max($x);

        $predict = $regressionCallback($x, $y);

        foreach ($data as $key => $value) {
            if ($value !== null) continue;
            if ($interpolateOnly && ($key < $minX || $key > $maxX)) continue;

            $data[$key] = round($predict((int)$key), 2);
        }

        return $data;
    }
}
