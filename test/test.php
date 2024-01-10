<?php

use DBarbieri\Random\Random;

require __DIR__ . '/../vendor/autoload.php';

$faker = Random::getFaker();

echo Random::int(1, 1000) . "\n";
