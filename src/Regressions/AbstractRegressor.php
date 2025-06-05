<?php

namespace MykolaVuy\Forecast\Regressions;

use MykolaVuy\Forecast\Regressions\Contracts\RegressionInterface;

abstract class AbstractRegressor implements RegressionInterface
{
    /**
     * Common forecast logic for all regression types.
     *
     * @param array $data Array in format [x => y] where y can be null.
     * @param callable $regressionCallback Callback that returns a predictor function.
     * @param bool $interpolateOnly Whether to only fill values within known x range.
     * @return array Forecasted data with filled values.
     */
    protected function fillForecast(array $data, callable $regressionCallback, bool $interpolateOnly = false): array
    {
        $known = array_filter($data, static fn($v) => $v !== null, ARRAY_FILTER_USE_BOTH);

        if (count($known) < 3) {
            return $data; // Not enough data for regression
        }

        $x = array_keys($known);
        $y = array_values($known);

        $minX = min($x);
        $maxX = max($x);

        $predict = $regressionCallback($x, $y);

        foreach ($data as $key => $value) {
            if ($value !== null) {
                continue;
            }

            if ($interpolateOnly && ($key < $minX || $key > $maxX)) {
                continue;
            }

            $data[$key] = round($predict((int)$key), 2);
        }

        return $data;
    }
}