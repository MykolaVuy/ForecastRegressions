<?php

use PHPUnit\Framework\TestCase;
use MykolaVuy\Forecast\Regressions\LogarithmicRegression;

final class LogarithmicRegressionTest extends TestCase
{
    private LogarithmicRegression $regression;

    protected function setUp(): void
    {
        $this->regression = new LogarithmicRegression();
    }

    public function testForecastWithValidData(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 3.5,
            4 => null,
            5 => 5.5,
        ];

        $result = $this->regression->forecast($data);

        $this->assertIsArray($result);
        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);

        $this->assertGreaterThan(2.0, $result[2]);
        $this->assertLessThan(5.5, $result[4]);
    }

    public function testForecastWithInvalidZeroAndNegativeKeys(): void
    {
        $data = [
            0 => 3.0,
            -1 => 2.0,
            1 => null,
            2 => 4.0,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[1]); // 0-й та -1-й пропускаються
    }

    public function testInterpolationOnlyMode(): void
    {
        $data = [
            1 => 2.0,
            2 => null,
            3 => 4.0,
            4 => null,
            5 => null,
            6 => null,
            7 => 9.0,
            8 => null,
        ];

        $result = $this->regression->forecast($data, true);

        $this->assertNotNull($result[2]);
        $this->assertNotNull($result[4]);
        $this->assertNotNull($result[5]);
        $this->assertNotNull($result[6]);

        $this->assertNull($result[8]); // поза інтервалом — не інтерполюється
    }

    public function testAllInvalidKeysReturnZero(): void
    {
        $data = [
            0 => null,
            -1 => null,
            -5 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[0]);
        $this->assertEquals(0.0, $result[-1]);
        $this->assertEquals(0.0, $result[-5]);
    }

    public function testInsufficientValidPointsReturnZero(): void
    {
        $data = [
            1 => 3.0,
            2 => null,
            3 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertEquals(0.0, $result[2]);
        $this->assertEquals(0.0, $result[3]);
    }

    public function testNoMissingValuesReturnsOriginal(): void
    {
        $data = [
            1 => 2.0,
            2 => 2.5,
            3 => 3.1,
        ];

        $result = $this->regression->forecast($data);

        $this->assertSame($data, $result);
    }

    public function testUnsortedKeysHandledCorrectly(): void
    {
        $data = [
            1 => 2.0,
            3 => 4.0,
            2 => null,
        ];

        $result = $this->regression->forecast($data);

        $this->assertArrayHasKey(2, $result);
        $this->assertNull($result[2]);
    }
}
