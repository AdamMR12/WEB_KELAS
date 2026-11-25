<?php
// 1. Buat database kalau belum ada
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS kas_kelassi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "OK: database kas_kelassi siap\n";
} catch (PDOException $e) {
    echo "GAGAL buat database: " . $e->getMessage() . "\n";
    echo "Pastikan MySQL XAMPP menyala.\n";
    exit(1);
}

// 2. Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 3. Migrate + seed
Illuminate\Support\Facades\Artisan::call('migrate', ['--seed' => true, '--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();
echo "SELESAI\n";
