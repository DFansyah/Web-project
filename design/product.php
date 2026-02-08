<?php
session_start();
require 'db.php';

// Ambil ID Produk
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

// Jika produk tidak ditemukan, kembali ke home
if(!$p) { header("Location: index.php"); exit; }

// --- LOGIC ADD TO CART (BELI BIASA) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $qty = (int)$_POST['quantity'];
    $size = $_POST['size'];
    
    // Masukkan ke Session Keranjang
    $_SESSION['cart'][] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'qty' => $qty,
        'size' => $size,
        'image' => $p['image']
    ];
    
    header("Location: cart.php"); // Redirect ke keranjang
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['name']); ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <style> body { background: #ffffff !important; } </style>
</head>
<body>

    <header class="top-header">
        <a href="index.php" style="font-size:1.2rem; color:var(--primary);"><i class="fas fa-arrow-left"></i></a>
        <div class="brand-logo" style="font-size:1rem;">DETAIL<b>PRODUK</b></div>
        <a href="cart.php" style="font-size:1.2rem; color:var(--primary); position:relative;">
            <i class="fas fa-shopping-bag"></i>
            <?php if(!empty($_SESSION['cart'])): ?>
                <span style="position:absolute; top:-5px; right:-5px; width:10px; height:10px; background:red; border-radius:50%;"></span>
            <?php endif; ?>
        </a>
    </header>

    <main class="main-content product-detail-wrapper">
        
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
        </div>

        <div class="product-info-column">
            <h1 class="product-detail-title"><?php echo htmlspecialchars($p['name']); ?></h1>
            <div class="product-detail-price">Rp <?php echo number_format($p['price'],0,',','.'); ?></div>
            
            <p class="product-detail-desc">
                <?php echo nl2br(htmlspecialchars($p['description'])); ?>
            </p>

            <form method="POST" class="form-options-container">
                
                <div style="margin-bottom: 15px;">
                    <label class="form-label">Pilih Ukuran</label>
                    <select name="size" class="custom-select">
                        <option>S</option>
                        <option>M</option>
                        <option selected>L</option>
                        <option>XL</option>
                        <option>XXL</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="quantity" value="1" min="1" class="custom-input-qty">
                </div>

                <button type="submit" name="add_to_cart" class="btn-primary" style="background:var(--primary); display:flex; justify-content:center; align-items:center; gap:8px;">
                    <i class="fas fa-cart-plus"></i> Masukkan Keranjang
                </button>
            </form>

            <div class="fixed-bottom-action">
                <a href="designer.php?product_id=<?php echo $p['id']; ?>" class="btn-primary" style="background:var(--blue-action); display:flex; justify-content:center; align-items:center; gap:8px; font-size:1.05rem;">
                    <i class="fas fa-paint-brush"></i> <b>Desain Custom Sendiri</b>
                </a>
            </div>
        </div>

    </main>

    </body>
</html>