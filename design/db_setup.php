<?php
require "db.php";

echo "Memulai setup database...<br>";

try {
  $sql_products = "
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL,
        image TEXT
    );";
  $pdo->exec($sql_products);
  echo "Tabel 'products' siap.<br>";

  $sql_users = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    );";
  $pdo->exec($sql_users);
  echo "Tabel 'users' siap.<br>";

  $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
  if ($stmt->fetchColumn() == 0) {
    $pass = password_hash("admin123", PASSWORD_DEFAULT);
    $pdo
      ->prepare("INSERT INTO users (username, password) VALUES (?, ?)")
      ->execute(["admin", $pass]);
    echo "Admin default dibuat.<br>";
  }

  $sql_customers = "
    CREATE TABLE IF NOT EXISTS customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );";
  $pdo->exec($sql_customers);
  echo "Tabel 'customers' siap.<br>";

  echo "<hr>Setup Selesai.";
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>
