<?php
// examples/percentile_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [15, 20, 35, 40, 50];
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo '25th percentile: ' . $stats->percentile(25) . PHP_EOL; // 20
echo '40th percentile: ' . $stats->percentile(40) . PHP_EOL; // 29