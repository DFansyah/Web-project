<?php
$db_dir = __DIR__ . '/database';
$db_file = $db_dir . '/foodstore.sqlite';

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

    // Tabel Produk (Tambah Kolom Category)
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price REAL,
        stock INTEGER DEFAULT 0,
        category TEXT, -- Kolom Baru
        description TEXT,
        image TEXT
    )");

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>