<?php

require 'vendor/autoload.php';

use MykolaVuy\Forecast\ForecastService;

$data = [
    1 => 100,
    2 => null,
    3 => 150,
    4 => null,
    5 => 180,
    6 => 200,
    7 => null,
    8 => null,
    9 => null,
];

// Instantiate the ForecastService
$service = new ForecastService();

// Forecast using linear regression
$result = $service->forecast($data, 'linear');
echo "Linear Regression Forecast:\n";
print_r($result);

// Forecast using linear regression with interpolation only
$result = $service->forecast($data, 'linear', true);
echo "\nLinear Regression Forecast (Interpolation Only):\n";
print_r($result);


// Forecast using power regression
$result = $service->forecast($data, 'power');
echo "\nPower Regression Forecast:\n";
print_r($result);

// Forecast using power regression with interpolation only
$result = $service->forecast($data, 'power', true);
echo "\nPower Regression Forecast (Interpolation Only):\n";
print_r($result);

// Forecast using logarithmic regression
$result = $service->forecast($data, 'logarithmic');
echo "\nLogarithmic Regression Forecast:\n";
print_r($result);

// Forecast using logarithmic regression with interpolation only
$result = $service->forecast($data, 'logarithmic', true);
echo "\nLogarithmic Regression Forecast (Interpolation Only):\n";
print_r($result);
