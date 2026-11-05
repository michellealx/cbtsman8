<?php

echo "PHP VERSION: " . PHP_VERSION . "\n";

echo "COMPOSER AUTOLOAD: ";
echo file_exists(__DIR__ . '/../vendor/autoload.php')
    ? "ADA\n" : "TIDAK ADA\n";

echo "LARAVEL BOOTSTRAP: ";
echo file_exists(__DIR__ . '/../bootstrap/app.php')
    ? "ADA\n" : "TIDAK ADA\n";

echo "PUBLIC INDEX: ";
echo file_exists(__DIR__ . '/../public/index.php')
    ? "ADA\n" : "TIDAK ADA\n";
