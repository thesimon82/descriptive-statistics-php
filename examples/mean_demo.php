<?php
// examples/mean_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [8, 12, 10, 15];        // Sample dataset
$stats = new DescriptiveStats($data);

echo 'Data: ' . json_encode($data) . PHP_EOL;
echo 'Mean : ' . $stats->mean() . PHP_EOL;