<?php
// examples/iqr_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [6, 47, 49, 15, 42, 41, 7, 39, 43, 40];
$stats = new DescriptiveStats($data);

[$q1, $q2, $q3] = $stats->quartiles();

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo "Q1: $q1  Q2 (median): $q2  Q3: $q3" . PHP_EOL;
echo 'IQR: ' . $stats->iqr() . PHP_EOL;