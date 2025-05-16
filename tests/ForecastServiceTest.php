<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\ForecastService;

final class ForecastServiceTest extends TestCase
{
    private ForecastService $service;

    protected function setUp(): void
    {
        $this->service = new ForecastService();
    }

    public function testLinearForecastDelegatesToRegressor(): void
    {
        $data = [
            1 => 1.0,
            2 => null,
            3 => 3.0,
            4 => 4.0,
        ];

        $result = $this->service->forecast($data, 'linear');
        $this->assertIsArray($result);
        $this->assertArrayHasKey(2, $result);
        $this->assertNotNull($result[2]);
    }

    public function testPowerForecastDelegatesToRegressor(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 6.0,
            4 => 8.0,
        ];

        $result = $this->service->forecast($data, 'power');
        $this->assertIsArray($result);
        $this->assertArrayHasKey(2, $result);
        $this->assertNotNull($result[2]);
    }

    public function testLogarithmicForecastDelegatesToRegressor(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 4.0,
            4 => 5.0,
        ];

        $result = $this->service->forecast($data, 'logarithmic');
        $this->assertIsArray($result);
        $this->assertArrayHasKey(2, $result);
        $this->assertNotNull($result[2]);
    }

    public function testInterpolateOnlyParameter(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 6.0,
            4 => null,
            5 => 10.0,
        ];

        $result = $this->service->forecast($data, 'linear', true);

        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);
    }

    public function testInvalidMethodThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown regression method: invalid');

        $data = [
            1 => 1.0,
            2 => 2.0,
        ];

        $this->service->forecast($data, 'invalid');
    }
}
