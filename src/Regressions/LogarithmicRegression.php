<?php

namespace MykolaVuy\Forecast\Regressions;

final class LogarithmicRegression extends AbstractRegressor
{
    public function forecast(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $n = count($x);
            $sumLogX = $sumLogX2 = $sumY = $sumYLogX = 0.0;
            $validCount = 0;

            for ($i = 0; $i < $n; $i++) {
                if ($x[$i] <= 0) {
                    continue;
                }

                $lx = log($x[$i]);

                $sumLogX += $lx;
                $sumLogX2 += $lx ** 2;
                $sumY += $y[$i];
                $sumYLogX += $y[$i] * $lx;
                $validCount++;
            }

            if ($validCount < 2) {
                return fn(int $key) => 0.0;
            }

            $denominator = ($validCount * $sumLogX2 - $sumLogX ** 2);
            if ($denominator === 0.0) {
                return fn(int $key) => 0.0;
            }

            $b = ($validCount * $sumYLogX - $sumLogX * $sumY) / $denominator;
            $a = ($sumY - $b * $sumLogX) / $validCount;

            return fn(int $key) => $key > 0 ? $a + $b * log($key) : 0.0;
        }, $interpolateOnly);
    }
}
