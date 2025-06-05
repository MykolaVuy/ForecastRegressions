<?php

namespace MykolaVuy\Forecast\Regressions\Contracts;

interface RegressionInterface
{
    public function forecast(array $data, bool $interpolateOnly = false): array;
}
