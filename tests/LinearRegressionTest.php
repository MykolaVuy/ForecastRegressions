<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\Regressions\LinearRegression;

final class LinearRegressionTest extends TestCase
{
    private LinearRegression $regression;

    protected function setUp(): void
    {
        $this->regression = new LinearRegression();
    }

    public function testFullDataSetReturnsSameValues(): void
    {
        $data = [
            0 => 1.0,
            1 => 2.0,
            2 => 3.0,
            3 => 4.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertSame($data, $result);
    }

    public function testForecastWithMissingValues(): void
    {
        $data = [
            0 => 1.0,
            1 => 2.0,
            2 => null,
            3 => 4.0,
            4 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);

        // Перевіримо лінійність (приблизно)
        $this->assertEqualsWithDelta(3.0, $result[2], 0.1);
        $this->assertEqualsWithDelta(5.0, $result[4], 0.1);
    }

    public function testInterpolationOnlyDoesNotExtrapolate(): void
    {
        $data = [
            0 => 2.0,
            1 => null,
            2 => 6.0,
            3 => null,
            4 => null,
            5 => 12.0,
            6 => null,
        ];

        $result = $this->regression->forecast($data, true);

        // interpolation
        $this->assertNotNull($result[1]);
        $this->assertNotNull($result[3]);
        $this->assertNotNull($result[4]);

        // extrapolation — should remain null
        $this->assertNull($result[6]);
    }

    public function testAllNullValuesReturnsEmptyForecast(): void
    {
        $data = [
            0 => null,
            1 => null,
            2 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertSame($data, $result);
    }

    public function testSingleDataPoint(): void
    {
        $data = [
            0 => 42.0,
            1 => null,
            2 => null,
        ];

        $result = $this->regression->forecast($data);

        // У формулі буде denominator === 0.0 -> заповниться 0.0
        $this->assertEquals(0.0, $result[1]);
        $this->assertEquals(0.0, $result[2]);
    }

    public function testConstantValues(): void
    {
        $data = [
            0 => 5.0,
            1 => null,
            2 => 5.0,
            3 => null,
            4 => 5.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEqualsWithDelta(5.0, $result[1], 0.1);
        $this->assertEqualsWithDelta(5.0, $result[3], 0.1);
    }

    public function testNegativeAndZeroValues(): void
    {
        $data = [
            -2 => 4.0,
            -1 => null,
            0 => 0.0,
            1 => null,
            2 => -4.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEqualsWithDelta(2.0, $result[-1], 0.1);
        $this->assertEqualsWithDelta(-2.0, $result[1], 0.1);
    }
}
