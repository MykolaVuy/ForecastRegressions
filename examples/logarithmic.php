<?php

require '../vendor/autoload.php';

use MykolaVuy\Forecast\ForecastService;

$dataSets = require 'data_sets.php';
$service = new ForecastService();

foreach ($dataSets as $name => $data) {
    echo "\n=== Logarithmic Regression: $name ===\n";
    $result = $service->forecast($data, 'logarithmic');
    print_r($result);

    echo "--- Logarithmic Regression (Interpolation Only) ---\n";
    $result = $service->forecast($data, 'logarithmic', true);
    print_r($result);
}
