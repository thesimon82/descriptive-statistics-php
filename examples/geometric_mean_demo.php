<?php
// examples/geometric_mean_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data = [4, 1];                      // Sample dataset
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo 'Geometric mean: ' . $stats->geometricMean() . PHP_EOL;