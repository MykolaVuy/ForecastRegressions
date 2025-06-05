<?php

namespace MykolaVuy\Forecast\Regressions;

final class PowerRegression extends AbstractRegressor
{
    public function forecast(array $data, bool $interpolateOnly = false): array
    {
        return $this->fillForecast($data, function (array $x, array $y): callable {
            $n = count($x);
            $sumLogX = $sumLogY = $sumLogX2 = $sumLogXLogY = 0.0;
            $validCount = 0;

            for ($i = 0; $i < $n; $i++) {
                if ($x[$i] <= 0 || $y[$i] <= 0) {
                    continue;
                }

                $lx = log($x[$i]);
                $ly = log($y[$i]);

                $sumLogX += $lx;
                $sumLogY += $ly;
                $sumLogX2 += $lx ** 2;
                $sumLogXLogY += $lx * $ly;
                $validCount++;
            }

            if ($validCount < 2) {
                return fn(int $key) => 0.0;
            }

            $denominator = ($validCount * $sumLogX2 - $sumLogX ** 2);
            if ($denominator === 0.0) {
                return fn(int $key) => 0.0;
            }

            $b = ($validCount * $sumLogXLogY - $sumLogX * $sumLogY) / $denominator;
            $a = exp(($sumLogY - $b * $sumLogX) / $validCount);

            return fn(int $key) => $key > 0 ? $a * ($key ** $b) : 0.0;
        }, $interpolateOnly);
    }
}
