<?php
// examples/harmonic_mean_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [2, 6];                      // Sample dataset
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo 'Harmonic mean: ' . $stats->harmonicMean() . PHP_EOL; // Output: 3