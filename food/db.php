<?php
$db_dir = __DIR__ . '/database';
$db_file = $db_dir . '/foodstore.sqlite'; // Ganti nama DB biar fresh

if (!is_dir($db_dir)) @mkdir($db_dir, 0777, true);

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Tabel User
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT UNIQUE,
        password TEXT
    )");

    // Tabel Menu Makanan
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price REAL,
        description TEXT,
        image TEXT
    )");

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>