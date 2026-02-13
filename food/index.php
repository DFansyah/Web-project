<?php
session_start();
require 'db.php';

// Logika Filter Kategori
$cat_filter = $_GET['category'] ?? 'Semua';

if ($cat_filter == 'Semua') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->execute([$cat_filter]);
}
$products = $stmt->fetchAll();

// List Kategori
$categories = ['Semua', 'Makanan', 'Minuman'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APHPMART - Jajan Sehat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">APHP<b>MART</b></div>
        <nav class="desktop-nav">
            <a href="cart.php">Keranjang</a>
            <?php if(isset($_SESSION['admin_logged_in'])): ?>
                <a href="admin.php">Admin</a>
                <a href="logout.php" style="color:var(--danger);">Keluar</a>
            <?php else: ?>
                <a href="login.php">Admin</a>
            <?php endif; ?>
        </nav>
        <?php if(isset($_SESSION['admin_logged_in'])): ?>
            <a href="logout.php" class="mobile-auth-btn btn-m-out" onclick="return confirm('Keluar Admin?');"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        <?php endif; ?>
    </header>

    <main class="main-content">
        <div style="margin-bottom: 20px;">
            <h2 style="font-size:1.8rem; font-weight:800; color:var(--text);">Mau Jajan Sehat? <br><span style="color:var(--primary);">Jajan di APHPMART!</span></h2>
            <p style="color:#777;">Mau pesan apa?</p>
        </div>

        <div class="category-scroll">
            <?php foreach($categories as $c): ?>
                <a href="index.php?category=<?php echo urlencode($c); ?>" 
                   class="cat-pill <?php echo ($cat_filter == $c) ? 'active' : ''; ?>">
                   <?php echo $c; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card" style="opacity: <?php echo ($p['stock'] == 0) ? '0.7' : '1'; ?>;">
                <a href="product.php?id=<?php echo $p['id']; ?>">
                    <div style="position:relative;">
                        <img src="<?php echo htmlspecialchars($p['image'] ?? ''); ?>">
                        
                        <?php if($p['stock'] == 0): ?>
                            <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:1.2rem;">HABIS</div>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="info">
                    <span style="font-size:0.7rem; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:3px; display:block;">
                        <?php echo htmlspecialchars($p['category'] ?? 'Umum'); ?>
                    </span>
                    
                    <a href="product.php?id=<?php echo $p['id']; ?>">
                        <h4><?php echo htmlspecialchars($p['name'] ?? 'Tanpa Nama'); ?></h4>
                    </a>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="price">Rp <?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></span>
                        
                        <?php if($p['stock'] > 0): ?>
                            <a href="product.php?id=<?php echo $p['id']; ?>" style="background:var(--accent); color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-plus"></i>
                            </a>
                        <?php else: ?>
                            <span style="font-size:0.8rem; color:var(--danger); font-weight:600;">Habis</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($products) == 0): ?>
            <div style="text-align:center; padding:50px; color:#999;">
                <i class="fas fa-search" style="font-size:2rem; margin-bottom:10px;"></i><br>
                Tidak ada produk di kategori ini.
            </div>
        <?php endif; ?>
    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php" class="active"><i class="fas fa-home"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-basket"></i></a>
        <a href="<?php echo isset($_SESSION['admin_logged_in']) ? 'admin.php' : 'login.php'; ?>"><i class="fas fa-user-cog"></i></a>
    </nav>
</body>
</html>