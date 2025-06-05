<?php

require '../vendor/autoload.php';

use MykolaVuy\Forecast\ForecastService;

$dataSets = require 'data_sets.php';
$service = new ForecastService();

foreach ($dataSets as $name => $data) {
    echo "\n=== Power Regression: $name ===\n";
    $result = $service->forecast($data, 'power');
    print_r($result);

    echo "--- Power Regression (Interpolation Only) ---\n";
    $result = $service->forecast($data, 'power', true);
    print_r($result);
}
