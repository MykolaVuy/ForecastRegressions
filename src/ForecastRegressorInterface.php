<?php

namespace MykolaVuy\Forecast;

interface ForecastRegressorInterface
{
    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countLinearRegression(array $data, bool $interpolateOnly = false): array;

    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countPowerRegression(array $data, bool $interpolateOnly = false): array;

    /**
     * @param array<int, float|null> $data
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function countLogarithmicRegression(array $data, bool $interpolateOnly = false): array;
}
