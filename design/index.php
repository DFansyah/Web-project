<?php
session_start();
require 'db.php';

// Ambil data produk terbaru
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandKamu - Toko Online</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">BRAND<b>KAMU</b></div>
        
        <nav class="desktop-nav">
            <a href="index.php" style="color:var(--accent);">Beranda</a>
            <a href="cart.php">Keranjang</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn-login-nav" style="background:var(--danger);">Keluar</a>
            <?php else: ?>
                <a href="login.php" class="btn-login-nav">Masuk / Daftar</a>
            <?php endif; ?>
            <a href="admin.php" title="Admin Panel" style="color:#ddd;"><i class="fas fa-cog"></i></a>
        </nav>

        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="mobile-auth-btn btn-m-out" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        <?php else: ?>
            <a href="login.php" class="mobile-auth-btn btn-m-in">
                Masuk
            </a>
        <?php endif; ?>
    </header>

    <main class="main-content">
        <div style="margin: 10px 0 20px 0;">
            <h2 style="font-size:1.4rem; color:var(--primary); font-weight:700;">Katalog Terbaru</h2>
            <p style="color:#888; font-size:0.9rem;">Pilih kaos favoritmu dan mulai desain!</p>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $p['id']; ?>">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                </a>
                <div class="info">
                    <a href="product.php?id=<?php echo $p['id']; ?>">
                        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                    </a>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:auto;">
                        <span class="price">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                        <a href="product.php?id=<?php echo $p['id']; ?>" style="width:28px; height:28px; background:var(--bg-color); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary);">
                            <i class="fas fa-arrow-right" style="font-size:0.8rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($products) == 0): ?>
            <div style="text-align:center; padding: 60px 20px; color:#bbb;">
                <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:15px; opacity:0.5;"></i>
                <p>Belum ada produk yang tersedia.</p>
            </div>
        <?php endif; ?>
    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php" class="active"><i class="fas fa-home"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
        <a href="admin.php"><i class="fas fa-user"></i></a>
    </nav>

</body>
</html>