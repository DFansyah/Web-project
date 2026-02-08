<?php
session_start();
require 'db.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodies - Pesan Makan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">FOOD<b>IES</b></div>
        
        <nav class="desktop-nav">
            <a href="index.php">Menu</a>
            <a href="cart.php">Pesanan</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Keluar</a>
            <?php else: ?>
                <a href="login.php">Masuk</a>
            <?php endif; ?>
            <a href="admin.php">Admin</a>
        </nav>

        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="mobile-auth-btn btn-m-out" onclick="return confirm('Keluar akun?');">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        <?php else: ?>
            <a href="login.php" class="mobile-auth-btn btn-m-in">Masuk</a>
        <?php endif; ?>
    </header>

    <main class="main-content">
        <div style="margin-bottom: 25px;">
            <h2 style="font-size:1.8rem; font-weight:800; color:var(--text);">Lapar? <span style="color:var(--primary);">Pesan Aja!</span></h2>
            <p style="color:#777;">Temukan makanan favoritmu hari ini.</p>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $p['id']; ?>">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>">
                </a>
                <div class="info">
                    <a href="product.php?id=<?php echo $p['id']; ?>">
                        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                    </a>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="price">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                        <a href="product.php?id=<?php echo $p['id']; ?>" style="background:var(--accent); color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($products) == 0): ?>
            <div style="text-align:center; padding:50px; color:#999;">Menu belum tersedia.</div>
        <?php endif; ?>
    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php" class="active"><i class="fas fa-utensils"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-basket"></i></a>
        <a href="admin.php"><i class="fas fa-user"></i></a>
    </nav>
</body>
</html>