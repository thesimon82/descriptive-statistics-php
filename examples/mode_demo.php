<?php
// examples/mode_demo.php

declare(strict_types=1);
use Renor\Statistics\DescriptiveStats;

$data  = [4, 7, 4, 9, 7, 7, 3];
$stats = new DescriptiveStats($data);

print_r($stats->mode());