<?php

require '../vendor/autoload.php';

use MykolaVuy\Forecast\ForecastService;

$dataSets = require 'data_sets.php';
$service = new ForecastService();

foreach ($dataSets as $name => $data) {
    echo "\n=== Linear Regression: $name ===\n";
    $result = $service->forecast($data, 'linear');
    print_r($result);

    echo "--- Linear Regression (Interpolation Only) ---\n";
    $result = $service->forecast($data, 'linear', true);
    print_r($result);
}
