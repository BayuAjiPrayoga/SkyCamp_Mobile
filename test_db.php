<?php
try {
    $dsn = "pgsql:host=127.0.0.1;port=5432;dbname=luhurcamp";
    $username = "postgres";
    // Read password from .env to match exactly what artisan uses
    $env = file_get_contents('.env');
    preg_match('/DB_PASSWORD=(.*)/', $env, $matches);
    $password = trim($matches[1] ?? '');

    echo "Attempting connection with:\nDSN: $dsn\nUser: $username\nPass: " . ($password ? '****' : '(empty)') . "\n";

    $pdo = new PDO($dsn, $username, $password);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
