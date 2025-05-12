<?php

declare(strict_types=1);

namespace Renor\Statistics;

/**
 * Class DescriptiveStats
 *
 * A lightweight class to perform basic descriptive statistics on numeric datasets.
 */
class DescriptiveStats
{
    /**
     * @var float[] Filtered and normalized numeric dataset.
     */
    private array $data;

    /**
     * Main constructor.
     *
     * @param array $data An array containing the numeric values to be analyzed.
     * @throws \InvalidArgumentException If the array is empty or contains no numeric values.
     */
    public function __construct(array $data)
    {
        // Filter only numeric values (int or float) and reset array keys
        $filtered = array_filter($data, 'is_numeric');
        $this->data = array_values($filtered);

        if (count($this->data) === 0) {
            throw new \InvalidArgumentException('The dataset must contain at least one numeric value.');
        }
    }

    /**
     * Calculates the arithmetic mean of the dataset.
     *
     * @return float The arithmetic mean.
     */
    public function mean(): float
    {
        return array_sum($this->data) / count($this->data);
    }

    /**
     * Calculates the median (50th percentile) of the dataset.
     *
     * @return float The median value.
     */
    public function median(): float
    {
        // Clone and sort the dataset to avoid mutating the original array
        $sorted = $this->data;
        sort($sorted, SORT_NUMERIC);

        $count = count($sorted);
        $mid = intdiv($count, 2);

        // If the count is odd, return the middle value
        if ($count % 2 === 1) {
            return (float) $sorted[$mid];
        }

        // If even, return the average of the two central values
        return ($sorted[$mid - 1] + $sorted[$mid]) / 2.0;
    }

    /**
     * Returns the mode(s) of the dataset.
     *
     * If the dataset is multimodal, an array with all modal values is returned.
     * If every value occurs only once, an empty array is returned (no mode).
     *
     * @return float[] list of modal values
     */
    public function mode(): array
    {
        // Build a frequency table: value => occurrences
        $frequencies = array_count_values($this->data);

        // Determine the highest frequency
        $maxFrequency = max($frequencies);

        // If every value appears only once, there is no mode
        if ($maxFrequency === 1) {
            return [];
        }

        // Collect all values that share the highest frequency
        $modes = [];
        foreach ($frequencies as $value => $count) {
            if ($count === $maxFrequency) {
                // Cast to float so that return type is consistent
                $modes[] = (float) $value;
            }
        }

        sort($modes, SORT_NUMERIC); // return modes in ascending order
        return $modes;
    }

    /**
     * Calculates the geometric mean of the dataset.
     *
     * @throws \DomainException If any value is zero or negative.
     * @return float The geometric mean.
     */
    public function geometricMean(): float
    {
        // The geometric mean is defined only for strictly positive numbers.
        foreach ($this->data as $value) {
            if ($value <= 0) {
                throw new \DomainException('Geometric mean requires all values to be greater than zero.');
            }
        }

        // Use logarithms for numerical stability: exp( (1/n) * sum(log(x_i)) )
        $logSum = array_sum(array_map('log', $this->data));
        return exp($logSum / count($this->data));
    }

    /**
     * Calculates the harmonic mean of the dataset.
     *
     * @throws \DomainException If any value is zero or negative.
     * @return float The harmonic mean.
     */
    public function harmonicMean(): float
    {
        // Harmonic mean is defined only for strictly positive numbers.
        foreach ($this->data as $value) {
            if ($value <= 0) {
                throw new \DomainException('Harmonic mean requires all values to be greater than zero.');
            }
        }

        $inverseSum = array_sum(array_map(
            static fn(float $v): float => 1.0 / $v,
            $this->data
        ));

        return count($this->data) / $inverseSum;
    }

    /**
     * Calculates the trimmed mean of the dataset.
     *
     * @param float $percent Percentage (0–50) of data to trim at each tail.
     * @throws \DomainException If $percent is out of range or removes all data.
     * @return float The trimmed mean.
     */
    public function trimmedMean(float $percent): float
    {
        if ($percent < 0.0 || $percent >= 50.0) {
            throw new \DomainException('Percent must be in the range 0 <= p < 50.');
        }

        $count = count($this->data);
        if ($count < 3) {
            // Too few values to trim meaningfully; fall back to arithmetic mean
            return $this->mean();
        }

        // Clone and sort to preserve original order
        $sorted = $this->data;
        sort($sorted, SORT_NUMERIC);

        // Number of elements to trim from each end
        $k = (int) floor($count * $percent / 100.0);

        // Ensure at least one value remains
        if ($k * 2 >= $count) {
            throw new \DomainException('Trim percentage removes all data.');
        }

        $trimmed = array_slice($sorted, $k, $count - 2 * $k);

        return array_sum($trimmed) / count($trimmed);
    }

    /**
     * Calculates the range (max – min) of the dataset.
     *
     * @return float The range of the data.
     */
    public function range(): float
    {
        // min() e max() sono O(n) ma il dataset è già in memoria: soluzione lineare
        return max($this->data) - min($this->data);
    }

    /**
     * Returns an array with the first, second (median) and third quartile.
     *
     * Method: "Tukey hinges".
     *  - Sort the dataset.
     *  - For Q1 and Q3, exclude the median when the sample size is odd.
     *
     * @return float[] [Q1, Q2, Q3] in ascending order.
     */
    public function quartiles(): array
    {
        $sorted = $this->data;
        sort($sorted, SORT_NUMERIC);

        $n = count($sorted);
        if ($n === 1) {
            return [$sorted[0], $sorted[0], $sorted[0]];
        }
        $mid = intdiv($n, 2);

        // Median (Q2)
        $q2 = ($n % 2 === 0)
            ? ($sorted[$mid - 1] + $sorted[$mid]) / 2.0
            : (float) $sorted[$mid];

        // Lower half (exclude median if n is odd)
        $lower = array_slice($sorted, 0, $mid);
        // Upper half (exclude median if n is odd)
        $upper = array_slice($sorted, ($n % 2 === 0) ? $mid : $mid + 1);

        // Q1 and Q3 are medians of the two halves
        $q1 = $this->medianOfArray($lower);
        $q3 = $this->medianOfArray($upper);

        return [$q1, $q2, $q3];
    }

    /**
     * Calculates the interquartile range (Q3 – Q1).
     *
     * @return float The interquartile range.
     */
    public function iqr(): float
    {
        [$q1, , $q3] = $this->quartiles();
        return $q3 - $q1;
    }

    /* ---------- Helper ---------- */
    /**
     * Median of a pre-sorted array (helper for quartiles).
     *
     * @param float[] $arr Sorted numeric array.
     * @return float Median value.
     */
    private function medianOfArray(array $arr): float
    {
        $count = count($arr);
        if ($count === 0) {
            throw new \LogicException('Cannot compute median of an empty array.');
        }

        $mid = intdiv($count, 2);

        return ($count % 2 === 0)
            ? ($arr[$mid - 1] + $arr[$mid]) / 2.0
            : (float) $arr[$mid];
    }

    /**
     * Calculates the variance of the dataset.
     *
     * @param bool $sample If true, uses (n-1) in the denominator (sample variance).
     *                     If false, uses n (population variance).
     * @return float The variance value.
     */
    public function variance(bool $sample = false): float
    {
        $n = count($this->data);

        // For a single value, population variance is 0, sample variance is undefined
        if ($n < 2 && $sample) {
            throw new \DomainException('Sample variance requires at least two observations.');
        }
        if ($n === 1) {
            return 0.0;
        }

        $mean = $this->mean();
        $sumSquares = 0.0;

        foreach ($this->data as $v) {
            $diff = $v - $mean;
            $sumSquares += $diff * $diff;
        }

        $denominator = $sample ? ($n - 1) : $n;
        return $sumSquares / $denominator;
    }

    /**
     * Calculates the standard deviation of the dataset.
     *
     * @param bool $sample If true, returns the sample standard deviation (n-1 in the denominator).
     *                     If false, returns the population standard deviation.
     * @return float The standard deviation.
     */
    public function standardDeviation(bool $sample = false): float
    {
        return sqrt($this->variance($sample));
    }

    /**
     * Calculates the standard error of the mean (SEM) of the dataset.
     *
     * SEM = sample standard deviation / sqrt(n)
     * Requires at least two observations.
     *
     * @throws \DomainException If the dataset size is less than 2.
     * @return float The standard error of the mean.
     */
    public function standardError(): float
    {
        $n = count($this->data);

        if ($n < 2) {
            throw new \DomainException('Standard error requires at least two observations.');
        }

        return $this->standardDeviation(true) / sqrt($n);
    }

    /**
     * Calculates the mean absolute deviation (MAD) of the dataset.
     *
     * @return float The mean absolute deviation.
     */
    public function meanAbsoluteDeviation(): float
    {
        $mean = $this->mean();
        $sumAbs = 0.0;

        foreach ($this->data as $v) {
            $sumAbs += abs($v - $mean);
        }

        return $sumAbs / count($this->data);
    }

    /**
     * Returns the p-th percentile of the dataset (linear interpolation).
     *
     * @param float $p Percentile in the closed range [0, 100].
     * @throws \DomainException If $p is outside 0–100.
     * @return float The requested percentile.
     */
    public function percentile(float $p): float
    {
        if ($p < 0.0 || $p > 100.0) {
            throw new \DomainException('Percentile must be between 0 and 100.');
        }

        $sorted = $this->data;
        sort($sorted, SORT_NUMERIC);
        $n = count($sorted);

        // Edge cases: 0th and 100th percentile
        if ($p === 0.0) {
            return (float) $sorted[0];
        }
        if ($p === 100.0) {
            return (float) $sorted[$n - 1];
        }

        // Linear-interpolated rank
        $rank = ($p / 100.0) * ($n - 1);
        $lowerIndex = (int) floor($rank);
        $upperIndex = (int) ceil($rank);
        $weightUpper = $rank - $lowerIndex;

        // If rank is an integer, no interpolation is needed
        if ($lowerIndex === $upperIndex) {
            return (float) $sorted[$lowerIndex];
        }

        $lowerValue = $sorted[$lowerIndex];
        $upperValue = $sorted[$upperIndex];

        return (1.0 - $weightUpper) * $lowerValue + $weightUpper * $upperValue;
    }

    /**
     * Returns the minimum value of the dataset.
     *
     * @return float The smallest observation.
     */
    public function minValue(): float
    {
        return (float) min($this->data);
    }

    /**
     * Returns the maximum value of the dataset.
     *
     * @return float The largest observation.
     */
    public function maxValue(): float
    {
        return (float) max($this->data);
    }
}