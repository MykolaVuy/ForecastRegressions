<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\ForecastRegressor;

final class ForecastRegressorTest extends TestCase
{
    private ForecastRegressor $regressor;

    protected function setUp(): void
    {
        $this->regressor = new ForecastRegressor();
    }

    public function testLinearRegressionWithMissingValues(): void
    {
        $data = [
            0 => 2.0,
            1 => 4.0,
            2 => null,
            3 => 8.0,
            4 => null,
            5 => 12.0,
        ];

        $result = $this->regressor->countLinearRegression($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);

        $this->assertEqualsWithDelta(6.0, $result[2], 0.1);
        $this->assertEqualsWithDelta(10.0, $result[4], 0.1);
    }

    public function testPowerRegressionWithMissingValues(): void
    {
        $data = [
            1 => 2.0,
            2 => 4.5,
            3 => null,
            4 => 9.0,
            5 => null,
            6 => 18.0,
        ];

        $result = $this->regressor->countPowerRegression($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[3]);
        $this->assertNotNull($result[5]);

        $this->assertEqualsWithDelta(6.0, $result[3], 2.0);
        $this->assertEqualsWithDelta(13.0, $result[5], 2.0);
    }

    public function testLogarithmicRegressionWithMissingValues(): void
    {
        $data = [
            1 => 2.0,
            2 => 3.0,
            3 => null,
            4 => 5.0,
            5 => null,
            6 => 6.0,
        ];

        $result = $this->regressor->countLogarithmicRegression($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[3]);
        $this->assertNotNull($result[5]);

        $this->assertEqualsWithDelta(4.0, $result[3], 1.0);
        $this->assertEqualsWithDelta(5.0, $result[5], 1.0);
    }

    public function testInterpolationOnlyDoesNotExtrapolate(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 6.0,
            4 => null,
            5 => null,
            6 => null,
            7 => 14.0,
        ];

        $result = $this->regressor->countLinearRegression($data, true);

        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);
        $this->assertNotNull($result[5]);
        $this->assertNotNull($result[6]);

        $dataWithExtrapolation = [
            3 => 5.0,
            4 => null,
            5 => 9.0,
            6 => null,
            7 => 13.0,
            8 => null,
            9 => null,
        ];

        $result2 = $this->regressor->countLinearRegression($dataWithExtrapolation, true);

        $this->assertNotNull($result2[4]);
        $this->assertNotNull($result2[6]);
        $this->assertNull($result2[8]);
        $this->assertNull($result2[9]);
    }
}
