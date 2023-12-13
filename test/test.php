<?php
require __DIR__ . '/../vendor/autoload.php';

$faker = Random\Random::getFaker();

echo Random\Random::int(1, 1000) . "\n";
