<?php
// examples/median_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [7, 12, 9, 15, 10];
$stats = new DescriptiveStats($data);

echo 'Data   : ' . json_encode($data) . PHP_EOL;
echo 'Median : ' . $stats->median() . PHP_EOL; // Output: 10