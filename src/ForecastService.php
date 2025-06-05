<?php

namespace MykolaVuy\Forecast;

use InvalidArgumentException;
use MykolaVuy\Forecast\Regressions\ExponentialRegression;
use MykolaVuy\Forecast\Regressions\LinearRegression;
use MykolaVuy\Forecast\Regressions\LogarithmicRegression;
use MykolaVuy\Forecast\Regressions\PowerRegression;
use MykolaVuy\Forecast\Regressions\Contracts\RegressionInterface;

final class ForecastService
{
    /**
     * @var array<string, RegressionInterface>
     */
    private array $regressors;

    public function __construct()
    {
        $this->regressors = self::defaultRegistry();
    }

    /**
     * Common method for forecasting data using specified regression methods.
     *
     * @param array<int, float|null> $data Data for forecasting, where keys are indices and values are the data points.
     * @param string $method Regression method to use: 'linear', 'power', 'logarithmic'.
     * @param bool $interpolateOnly If true, only fills values within the known x range, otherwise extrapolates.
     * @return array<int, float|null>
     */
    public function forecast(array $data, string $method = 'linear', bool $interpolateOnly = false): array
    {
        $method = strtolower($method);

        if (!isset($this->regressors[$method])) {
            throw new InvalidArgumentException("Unknown regression method: {$method}");
        }

        return $this->regressors[$method]->forecast($data, $interpolateOnly);
    }

    /**
     * Static method for quick access to forecasting
     */
    public static function predict(array $data, string $method = 'linear', bool $interpolateOnly = false): array
    {
        return (new self())->forecast($data, $method, $interpolateOnly);
    }

    /**
     * Register a new regression method
     */
    public function register(string $method, RegressionInterface $regressor): void
    {
        $this->regressors[strtolower($method)] = $regressor;
    }

    /**
     * @return array<string, RegressionInterface>
     */
    private static function defaultRegistry(): array
    {
        return [
            'linear' => new LinearRegression(),
            'power' => new PowerRegression(),
            'logarithmic' => new LogarithmicRegression(),
            'exponential' => new ExponentialRegression(),
        ];
    }
}
