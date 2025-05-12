<?php
// tests/DescriptiveStatsTest.php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Renor\Statistics\DescriptiveStats;

final class DescriptiveStatsTest extends TestCase
{
    /* ---------- Mean ---------- */
    public function testMean(): void
    {
        $stats = new DescriptiveStats([8, 12, 10, 15]);
        $this->assertSame(11.25, $stats->mean());
    }

    public function testMeanWithSingleValue(): void
    {
        $stats = new DescriptiveStats([42]);
        $this->assertSame(42.0, $stats->mean());
    }

    public function testMeanThrowsOnEmptyDataset(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DescriptiveStats([]);
    }

    /* ---------- Median ---------- */
    public function testMedianOddCount(): void
    {
        $stats = new DescriptiveStats([7, 12, 9, 15, 10]);
        $this->assertSame(10.0, $stats->median());
    }

    public function testMedianEvenCount(): void
    {
        $stats = new DescriptiveStats([1, 3, 2, 4]);
        $this->assertSame(2.5, $stats->median());
    }

    /* ---------- Mode ---------- */
    public function testModeUnimodal(): void
    {
        $stats = new DescriptiveStats([4, 7, 4, 9, 7, 7, 3]);
        $this->assertSame([7.0], $stats->mode());
    }

    public function testModeMultimodal(): void
    {
        $stats = new DescriptiveStats([2, 3, 2, 3, 4]);
        $this->assertSame([2.0, 3.0], $stats->mode());
    }

    public function testModeNoMode(): void
    {
        $stats = new DescriptiveStats([10, 11, 12]);
        $this->assertSame([], $stats->mode());
    }

    /* ---------- Geometric Mean ---------- */
    public function testGeometricMean(): void
    {
        $stats = new DescriptiveStats([4, 1]);
        $this->assertSame(2.0, $stats->geometricMean());
    }

    public function testGeometricMeanThrowsOnNonPositive(): void
    {
        $this->expectException(\DomainException::class);

        $stats = new DescriptiveStats([5, 0, 2]); // contiene uno zero
        $stats->geometricMean();                  // qui scatta l’eccezione
    }

    /* ---------- Harmonic Mean ---------- */
    public function testHarmonicMean(): void
    {
        $stats = new DescriptiveStats([2, 6]);   // 2 valori -> H = 3
        $this->assertSame(3.0, $stats->harmonicMean());
    }

    public function testHarmonicMeanThrowsOnNonPositive(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([5, 0, 10]); // contiene zero
        $stats->harmonicMean();
    }

    /* ---------- Trimmed Mean ---------- */
    public function testTrimmedMean25Percent(): void
    {
        $stats = new DescriptiveStats([1, 2, 3, 100]);
        $this->assertSame(2.5, $stats->trimmedMean(25.0));
    }

    public function testTrimmedMeanZeroPercentEqualsArithmetic(): void
    {
        $data = [4, 6, 10];
        $stats = new DescriptiveStats($data);
        $this->assertSame($stats->mean(), $stats->trimmedMean(0.0));
    }

    public function testTrimmedMeanThrowsOnTooHighPercent(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([1, 2, 3, 4]);
        $stats->trimmedMean(60.0);   // Removes all data
    }

    /* ---------- Range ---------- */
    public function testRange(): void
    {
        $stats = new DescriptiveStats([4, 7, 1, 9, 5]); // max 9, min 1
        $this->assertSame(8.0, $stats->range());
    }

    public function testRangeSingleValueIsZero(): void
    {
        $stats = new DescriptiveStats([42]);
        $this->assertSame(0.0, $stats->range());
    }

    /* ---------- Quartiles & IQR ---------- */

    public function testQuartilesAndIqr(): void
    {
        $stats = new DescriptiveStats([6, 47, 49, 15, 42, 41, 7, 39, 43, 40]);
        [$q1, $q2, $q3] = $stats->quartiles();

        $this->assertSame(15.0, $q1);   // Q1
        $this->assertSame(40.5, $q2);   // Q2 (mediana globale)
        $this->assertSame(43.0, $q3);   // Q3 corretto secondo Tukey hinges
        $this->assertSame(28.0, $stats->iqr()); // 43 − 15
    }

    public function testIqrSingleValueIsZero(): void
    {
        $stats = new DescriptiveStats([5]);
        $this->assertSame(0.0, $stats->iqr());
    }

    /* ---------- Variance ---------- */
    public function testPopulationVariance(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]);
        $this->assertSame(4.0, $stats->variance(false));
    }

    public function testSampleVariance(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]);
        $this->assertEqualsWithDelta(4.57142857, $stats->variance(true), 1e-7);
    }

    public function testSampleVarianceThrowsIfSingleValue(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([10]);
        $stats->variance(true);
    }

    /* ---------- Standard Deviation ---------- */
    public function testPopulationStdDev(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]);
        $this->assertSame(2.0, $stats->standardDeviation(false));
    }

    public function testSampleStdDev(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]);
        $this->assertEqualsWithDelta(2.138089935, $stats->standardDeviation(true), 1e-9);
    }

    public function testSampleStdDevThrowsIfSingleValue(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([10]);
        $stats->standardDeviation(true);
    }

    /* ---------- Standard Error ---------- */
    public function testStandardError(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]);
        // st. dev. campionaria ≈ 2.138089935  →  SEM ≈ 2.138089935 / sqrt(8)
        $expected = 2.138089935 / sqrt(8);
        $this->assertEqualsWithDelta($expected, $stats->standardError(), 1e-9);
    }

    public function testStandardErrorThrowsIfLessThanTwo(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([10]);
        $stats->standardError();
    }

    /* ---------- Mean Absolute Deviation ---------- */
    public function testMeanAbsoluteDeviation(): void
    {
        $stats = new DescriptiveStats([2, 4, 4, 4, 5, 5, 7, 9]); // mean = 5
        $this->assertSame(1.5, $stats->meanAbsoluteDeviation());
    }

    public function testMadSingleValueIsZero(): void
    {
        $stats = new DescriptiveStats([42]);
        $this->assertSame(0.0, $stats->meanAbsoluteDeviation());
    }

    /* ---------- Percentile ---------- */
    public function testPercentileExactRank(): void
    {
        $stats = new DescriptiveStats([10, 20, 30, 40]);
        // 25th percentile = 17.5 secondo interpolazione lineare
        $this->assertSame(17.5, $stats->percentile(25.0));
    }

    public function testPercentileInterpolated(): void
    {
        $stats = new DescriptiveStats([15, 20, 35, 40, 50]);
        // 40th percentile should interpolate between 20 and 35 → 29
        $this->assertSame(29.0, $stats->percentile(40.0));
    }

    public function testPercentileBounds(): void
    {
        $stats = new DescriptiveStats([3, 6, 9]);
        $this->assertSame(3.0, $stats->percentile(0.0));   // min
        $this->assertSame(9.0, $stats->percentile(100.0)); // max
    }

    public function testPercentileThrowsOnInvalid(): void
    {
        $this->expectException(\DomainException::class);
        $stats = new DescriptiveStats([1, 2, 3]);
        $stats->percentile(120.0);
    }

    /* ---------- Min & Max ---------- */
    public function testMinAndMax(): void
    {
        $stats = new DescriptiveStats([4, 7, 1, 9, 5]);
        $this->assertSame(1.0, $stats->minValue());
        $this->assertSame(9.0, $stats->maxValue());
    }

    public function testMinMaxSingleValue(): void
    {
        $stats = new DescriptiveStats([42]);
        $this->assertSame(42.0, $stats->minValue());
        $this->assertSame(42.0, $stats->maxValue());
    }
}