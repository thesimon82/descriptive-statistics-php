<?php
// examples/range_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [4, 7, 1, 9, 5];
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo 'Range: ' . $stats->range() . PHP_EOL;    // Output: 8