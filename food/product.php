<?php
session_start();
require 'db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if(!$p) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if ($p['stock'] <= 0) {
        // Cegah jika stok habis tapi dipaksa post
        header("Location: product.php?id=$id"); exit;
    }

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $qty = (int)$_POST['quantity'];
    // Validasi agar tidak pesan melebihi stok
    if ($qty > $p['stock']) $qty = $p['stock'];

    $_SESSION['cart'][] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'qty' => $qty,
        'note' => $_POST['note'],
        'image' => $p['image']
    ];
    header("Location: cart.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body style="background:white;">

    <header class="top-header">
        <a href="index.php"><i class="fas fa-arrow-left"></i></a>
        <div class="brand-logo">DETAIL<b>PRODUK</b></div>
        <a href="cart.php"><i class="fas fa-shopping-basket"></i></a>
    </header>

    <main class="main-content">
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($p['image']); ?>">
        </div>

        <h1 class="product-detail-title"><?php echo htmlspecialchars($p['name']); ?></h1>
        <div class="product-detail-price">Rp <?php echo number_format($p['price'],0,',','.'); ?></div>
        
        <p class="product-detail-desc"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
        
        <div style="margin-bottom: 20px; padding: 10px; background: #f0f2f5; border-radius: 8px; color: var(--text);">
            <i class="fas fa-boxes" style="color: var(--primary);"></i> Stok Produk: 
            <?php if($p['stock'] > 0): ?>
                <b><?php echo $p['stock']; ?> tersedia</b>
            <?php else: ?>
                <b style="color: var(--danger);">HABIS</b>
            <?php endif; ?>
        </div>

        <?php if($p['stock'] > 0): ?>
            <form method="POST" style="background:#f9f9f9; padding:20px; border-radius:15px;">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Catatan (Opsional)</label>
                    <input type="text" name="note" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Jumlah</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $p['stock']; ?>" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
                </div>

                <button type="submit" name="add_to_cart" class="btn-primary" style="margin-bottom: 10px; background: var(--accent);">
                    <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                </button>
                
                <button type="submit" name="add_to_cart" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah ke Pesanan
                </button>
            </form>
        <?php else: ?>
            <div style="background: #ffecec; color: var(--danger); padding: 20px; border-radius: 15px; text-align: center; font-weight: 700;">
                <i class="fas fa-times-circle" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                Maaf, produk ini sedang habis.
            </div>
        <?php endif; ?>

    </main>
</body>
</html>