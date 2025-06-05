<?php

namespace MykolaVuy\Forecast\Regressions;

final class LinearRegression extends AbstractRegressor
{
    public function forecast(array $data, bool $interpolateOnly = false): array
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
            if ($denominator === 0.0) {
                return fn(int $key) => 0.0;
            }

            $a = ($sumX * $sumY - $n * $sumXY) / $denominator;
            $b = ($sumX * $sumXY - $sumXX * $sumY) / $denominator;

            return fn(int $key) => $a * $key + $b;
        }, $interpolateOnly);
    }
}
