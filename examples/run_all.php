<?php

require '../vendor/autoload.php';

use MykolaVuy\Forecast\ForecastService;

$dataSets = require 'data_sets.php';

$regressions = ['linear', 'power', 'logarithmic', 'exponential'];

$service = new ForecastService();

foreach ($dataSets as $setName => $data) {
    echo "\n=============================\n";
    echo "Data Set: $setName\n";
    echo "=============================\n";

    foreach ($regressions as $method) {
        echo "\n--- [$method] Instance ---\n";
        $result = $service->forecast($data, $method);
        print_r($result);

        echo "--- [$method] InterpolateOnly ---\n";
        $result = $service->forecast($data, $method, true);
        print_r($result);

        echo "--- [$method] Static ---\n";
        $static = ForecastService::predict($data, $method);
        print_r($static);
    }
}
