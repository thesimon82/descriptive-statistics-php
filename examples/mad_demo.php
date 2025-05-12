<?php
// examples/mad_demo.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data = [2, 4, 4, 4, 5, 5, 7, 9];
$stats = new DescriptiveStats($data);

echo 'Data (json): ' . json_encode($data) . PHP_EOL;
echo 'Mean            : ' . $stats->mean() . PHP_EOL; // 5
echo 'Mean abs. dev.  : ' . $stats->meanAbsoluteDeviation() . PHP_EOL; // 1.5