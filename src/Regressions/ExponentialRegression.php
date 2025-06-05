<?php

namespace MykolaVuy\Forecast\Regressions;

final class ExponentialRegression extends AbstractRegressor
{
    public function forecast(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $n = count($x);
            $sumX = $sumX2 = $sumLogY = $sumXLogY = 0.0;

            $valid = 0;
            for ($i = 0; $i < $n; $i++) {
                if ($y[$i] <= 0) {
                    continue; // логарифм недопустимий
                }

                $xi = $x[$i];
                $lyi = log($y[$i]);

                $sumX += $xi;
                $sumX2 += $xi * $xi;
                $sumLogY += $lyi;
                $sumXLogY += $xi * $lyi;

                $valid++;
            }

            if ($valid < 2) {
                return fn(int $x) => 0.0;
            }

            $denominator = ($valid * $sumX2 - $sumX ** 2);
            if ($denominator == 0.0) {
                return fn(int $x) => 0.0;
            }

            $b = ($valid * $sumXLogY - $sumX * $sumLogY) / $denominator;
            $a = exp(($sumLogY - $b * $sumX) / $valid);

            return fn(int $x) => $a * exp($b * $x);
        }, $interpolateOnly);
    }
}
