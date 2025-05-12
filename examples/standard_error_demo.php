<?php
// examples/standard_error_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [2, 4, 4, 4, 5, 5, 7, 9];
$stats = new DescriptiveStats($data);

echo 'Sample size        : ' . count($data)                . PHP_EOL;
echo 'Sample st. dev.    : ' . $stats->standardDeviation(true) . PHP_EOL;
echo 'Standard error (SEM): ' . $stats->standardError()       . PHP_EOL;