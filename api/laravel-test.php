
<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

echo "PHP BERJALAN\n";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "COMPOSER BERHASIL\n";

    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "LARAVEL BOOTSTRAP BERHASIL\n";

    $request = Illuminate\Http\Request::capture();

    $app->handleRequest($request);

    echo "\nLARAVEL REQUEST SELESAI\n";

} catch (Throwable $e) {
    error_log(
        'Laravel diagnostic error: ' .
        get_class($e) . ': ' . $e->getMessage()
    );

    echo "\nLARAVEL ERROR\n";
    echo "ERROR TYPE: " . get_class($e) . "\n";
    echo "Periksa Runtime Logs Vercel.\n";
}
