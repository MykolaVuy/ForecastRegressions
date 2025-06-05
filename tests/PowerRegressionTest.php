<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\Regressions\PowerRegression;

final class PowerRegressionTest extends TestCase
{
    private PowerRegression $regression;

    protected function setUp(): void
    {
        $this->regression = new PowerRegression();
    }

    public function testForecastWithTypicalData(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 5.0,
            4 => null,
            5 => 18.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);

        $this->assertGreaterThan(2.0, $result[2]);
        $this->assertLessThan(18.0, $result[4]);
    }

    public function testForecastWithInvalidXorY(): void
    {
        $data = [
            -1 => 1.0,
            0 => 1.0,
            1 => 2.0,
            2 => null,
            3 => -3.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[2]); // бо замало валідних
    }

    public function testForecastWithInterpolationOnly(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 4.5,
            4 => null,
            5 => null,
            6 => null,
            7 => 16.0,
            8 => null,
        ];

        $result = $this->regression->forecast($data, true);

        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);
        $this->assertNotNull($result[5]);
        $this->assertNotNull($result[6]);

        $this->assertNull($result[8]); // поза відомими межами
    }

    public function testForecastWithAllInvalidPointsReturnsZero(): void
    {
        $data = [
            -1 => null,
            0 => null,
            1 => -1.0,
            2 => 0.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertNull($result[-1]);
        $this->assertNull($result[0]);
    }

    public function testForecastWithOneValidPointReturnsZero(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[2]);
        $this->assertEquals(0.0, $result[3]);
    }

    public function testForecastWithPerfectPowerCurve(): void
    {
        // y = x^2
        $data = [
            1 => 1.0,
            2 => 4.0,
            3 => null,
            4 => 16.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEqualsWithDelta(9.0, $result[3], 0.1); // 3^2 = 9
    }

    public function testForecastWithNoMissingValuesReturnsOriginal(): void
    {
        $data = [
            1 => 2.0,
            2 => 4.0,
            3 => 9.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertSame($data, $result);
    }

    public function testUnsortedInputHandledCorrectly(): void
    {
        $data = [
            3 => 9.0,
            1 => null,
            2 => 4.0,
            4 => 16.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertNotNull($result[1]);
        $this->assertGreaterThan(0.0, $result[1]);
    }
}
