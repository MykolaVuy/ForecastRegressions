<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\ForecastService;
use MykolaVuy\Forecast\Regressions\Contracts\RegressionInterface;

final class ForecastServiceTest extends TestCase
{
    private ForecastService $service;

    protected function setUp(): void
    {
        $this->service = new ForecastService();
    }

    public function testLinearForecastReturnsValues(): void
    {
        $data = [0 => 1.0, 1 => 3.0, 2 => null, 3 => 7.0];

        $result = $this->service->forecast($data, 'linear');

        $this->assertIsArray($result);
        $this->assertNotNull($result[2]);
        $this->assertEqualsWithDelta(5.0, $result[2], 1.0);
    }

    public function testPowerForecastHandlesNulls(): void
    {
        $data = [1 => 2.0, 2 => null, 3 => 4.0, 4 => 8.0];

        $result = $this->service->forecast($data, 'power');

        $this->assertNotNull($result[2]);
        $this->assertGreaterThan(0, $result[2]);
    }

    public function testLogarithmicForecastWorks(): void
    {
        $data = [1 => 2.0, 2 => null, 3 => 3.0];

        $result = $this->service->forecast($data, 'logarithmic');

        $this->assertArrayHasKey(2, $result);
        $this->assertIsFloat($result[2] ?? 0.0);
    }

    public function testExponentialForecastIgnoresInvalid(): void
    {
        $data = [1 => 2.0, 2 => null, 3 => -3.0];

        $result = $this->service->forecast($data, 'exponential');

        $this->assertIsArray($result);
        $this->assertNull($result[2]); // -3.0 робить тренд неможливим
    }

    public function testInterpolationSkipsWhenNotEnoughPoints()
    {
        $service = new ForecastService();

        $data = [
            1 => 2.0,
            2 => null,
            3 => 4.0,
        ];

        $result = $service->forecast($data, 'linear', interpolateOnly: true);

        $this->assertNull($result[2]); // fillForecast нічого не робить
    }

    public function testStaticPredictWorks(): void
    {
        $data = [1 => 2.0, 2 => null, 3 => 4.0, 4 => 5.0];
        $result = ForecastService::predict($data, 'linear');

        $this->assertIsFloat($result[2]);
    }

    public function testThrowsOnInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->forecast([1 => 2.0, 2 => null], 'unknown');
    }

    public function testCustomRegressorCanBeRegistered(): void
    {
        $mock = $this->createMock(RegressionInterface::class);
        $mock->method('forecast')->willReturn([0 => 10.0]);

        $this->service->register('custom', $mock);

        $result = $this->service->forecast([0 => null], 'custom');

        $this->assertEquals(10.0, $result[0]);
    }

    public function testEmptyInputReturnsEmptyArray(): void
    {
        $result = $this->service->forecast([], 'linear');
        $this->assertEmpty($result);
    }

    public function testAllNullsInputReturnsAllNulls(): void
    {
        $data = [0 => null, 1 => null];
        $result = $this->service->forecast($data, 'linear');

        $this->assertEquals([0 => null, 1 => null], $result);
    }

    public function testSingleValidPointReturnsOnlyNulls(): void
    {
        $data = [0 => 2.0, 1 => null, 2 => null];
        $result = $this->service->forecast($data, 'linear');

        $this->assertNull($result[1]);
        $this->assertNull($result[2]);
    }

    public function testCaseInsensitiveMethodName(): void
    {
        $data = [1 => 1.0, 2 => null, 3 => 3.0, 4 => 4.0];
        $result = $this->service->forecast($data, 'LiNeAr');

        $this->assertIsFloat($result[2]);
    }
}
