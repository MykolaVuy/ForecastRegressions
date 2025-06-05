<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\Regressions\ExponentialRegression;

final class ExponentialRegressionTest extends TestCase
{
    private ExponentialRegression $regression;

    protected function setUp(): void
    {
        $this->regression = new ExponentialRegression();
    }

    public function testForecastWithPositiveExponentialData(): void
    {
        $data = [
            0 => 2.0,
            1 => null,
            2 => 4.0,
            3 => null,
            4 => 8.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[1]);
        $this->assertNotNull($result[3]);

        $this->assertEqualsWithDelta(2.83, $result[1], 0.5);
        $this->assertEqualsWithDelta(5.66, $result[3], 0.5);
    }

    public function testInterpolationOnlySkipsExtrapolation(): void
    {
        $data = [
            1 => 1.0,
            2 => null,
            3 => 2.7,
            4 => null,
            5 => null,
            6 => null,
            7 => 20.0,
            8 => null,
        ];

        $result = $this->regression->forecast($data, true);

        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);
        $this->assertNotNull($result[5]);
        $this->assertNotNull($result[6]);

        $this->assertNull($result[8]); // поза межами відомих — не інтерполюється
    }

    public function testNegativeAndZeroValuesAreIgnored(): void
    {
        $data = [
            0 => 5.0,
            1 => 0.0, // логарифм неможливий
            2 => -3.0, // логарифм неможливий
            3 => null,
            4 => 40.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertNotNull($result[3]);
        $this->assertGreaterThan(5.0, $result[3]); // очікується зростання
    }

    public function testAllInvalidDataReturnsZero(): void
    {
        $data = [
            0 => -5.0,
            1 => null,
            2 => 0.0,
            3 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[1]);
        $this->assertEquals(0.0, $result[3]);
    }

    public function testSingleValidPointReturnsZero(): void
    {
        $data = [
            0 => 10.0,
            1 => null,
            2 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[1]);
        $this->assertEquals(0.0, $result[2]);
    }

    public function testNoMissingValuesReturnsOriginal(): void
    {
        $data = [
            0 => 1.0,
            1 => 2.0,
            2 => 4.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertSame($data, $result);
    }

    public function testUnsortedKeysHandledCorrectly(): void
    {
        $data = [
            3 => 3.0,
            1 => null,
            2 => 2.0,
            0 => 1.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[1]);
    }
}
