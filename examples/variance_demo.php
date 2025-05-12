<?php
// examples/variance_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [2, 4, 4, 4, 5, 5, 7, 9];
$stats = new DescriptiveStats($data);

echo 'Population variance : ' . $stats->variance(false) . PHP_EOL; // 4
echo 'Sample variance     : ' . $stats->variance(true)  . PHP_EOL; // 4.5714…