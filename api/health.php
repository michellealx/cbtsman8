<?php

header('Content-Type: text/plain; charset=UTF-8');

$result = setcookie('cbt_test', 'ok', [
    'expires' => time() + 300,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

echo $result
    ? 'COOKIE TEST BERHASIL'
    : 'COOKIE TEST GAGAL';
