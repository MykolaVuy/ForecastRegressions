<?php

namespace MykolaVuy\Forecast;

use InvalidArgumentException;

final class ForecastService
{
    public function __construct(
        private readonly ForecastRegressorInterface $regressor = new ForecastRegressor()
    ) {}

    /**
     * Загальний метод прогнозу
     *
     * @param array<int, float|null> $data
     * @param string $method Method of regression: 'linear', 'power', 'logarithmic'
     * @param bool $interpolateOnly If true — forecast only between known values
     * @return array<int, float|null>
     */
    public function forecast(array $data, string $method = 'linear', bool $interpolateOnly = false): array
    {
        return match (strtolower($method)) {
            'linear'      => $this->regressor->countLinearRegression($data, $interpolateOnly),
            'power'       => $this->regressor->countPowerRegression($data, $interpolateOnly),
            'logarithmic' => $this->regressor->countLogarithmicRegression($data, $interpolateOnly),
            default       => throw new InvalidArgumentException("Unknown regression method: {$method}"),
        };
    }
}
