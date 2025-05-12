<?php
// examples/trimmed_mean_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [1, 2, 3, 100];               // Outlier 100
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo 'Trimmed mean (25%): ' . $stats->trimmedMean(25.0) . PHP_EOL; // Output: 2.5