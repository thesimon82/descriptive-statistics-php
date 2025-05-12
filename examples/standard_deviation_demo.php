<?php
// examples/standard_deviation_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [2, 4, 4, 4, 5, 5, 7, 9];
$stats = new DescriptiveStats($data);

echo 'Population st. dev.: ' . $stats->standardDeviation(false) . PHP_EOL; // 2
echo 'Sample st. dev.    : ' . $stats->standardDeviation(true)  . PHP_EOL; // 2.1380…