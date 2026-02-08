<?php
/* =========================================
   DB.PHP
   Koneksi Database SQLite & Auto-Create Tables
   ========================================= */

// 1. Tentukan Lokasi Database
$db_dir = __DIR__ . '/database';
$db_file = $db_dir . '/store.sqlite';

// 2. Buat Folder Database Jika Belum Ada
if (!is_dir($db_dir)) {
    if (!@mkdir($db_dir, 0777, true)) {
        die("Gagal membuat folder database. Pastikan permission folder benar.");
    }
}

try {
    // 3. Koneksi ke SQLite
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 4. TABEL CUSTOMERS (Pengguna)
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT UNIQUE,
        password TEXT
    )");

    // 5. TABEL PRODUCTS (Katalog Barang)
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price REAL,
        description TEXT,
        image TEXT
    )");

    // 6. TABEL CUSTOM DESIGNS (Pesanan Desain)
    $pdo->exec("CREATE TABLE IF NOT EXISTS custom_designs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER,
        user_name TEXT,
        contact TEXT,
        image_path TEXT,
        status TEXT DEFAULT 'pending', -- pending, approved, rejected
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    // Tampilkan Error Jika Koneksi Gagal
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>