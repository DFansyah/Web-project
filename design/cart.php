<?php
ob_start();
session_start();
require 'db.php';

// Inisialisasi Keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- 1. LOGIC HAPUS ITEM ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['index'])) {
    $index = $_GET['index'];
    
    // Hapus item berdasarkan index array
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1); // array_splice otomatis menata ulang index
    }
    
    header("Location: cart.php");
    exit;
}

// --- 2. HITUNG TOTAL & SIAPKAN PESAN WA ---
$total_price = 0;
$wa_message = "Halo Admin, saya ingin memesan:\n\n";

foreach ($_SESSION['cart'] as $item) {
    $subtotal = $item['price'] * $item['qty'];
    $total_price += $subtotal;
    
    // Format Pesan: Nama Barang (Size) x Qty - Rp Harga
    $wa_message .= "- " . $item['name'] . " (" . $item['size'] . ") x " . $item['qty'] . " = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
}

$wa_message .= "\n*Total: Rp " . number_format($total_price, 0, ',', '.') . "*";
$wa_message .= "\n\nMohon info pembayaran. Terima kasih!";

// Nomor WA Admin (GANTI DENGAN NOMOR ANDA, Format: 628...)
$admin_wa = "6281234567890"; 
$checkout_link = "https://wa.me/$admin_wa?text=" . urlencode($wa_message);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">KERANJANG<b>KAMU</b></div>
        
        <nav class="desktop-nav">
            <a href="index.php">Beranda</a>
            <a href="cart.php" style="color:var(--accent);">Keranjang</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn-login-nav" style="background:var(--danger);">Keluar</a>
            <?php else: ?>
                <a href="login.php" class="btn-login-nav">Masuk</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="main-content" style="padding-bottom: 150px;">
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div style="text-align:center; margin-top:100px; color:#aaa;">
                <i class="fas fa-shopping-cart" style="font-size:4rem; margin-bottom:20px; color:#ddd;"></i>
                <h3>Keranjang Kosong</h3>
                <p style="margin-bottom:20px;">Kamu belum memilih produk apapun.</p>
                <a href="index.php" class="btn-primary" style="width:auto; padding:10px 30px;">Mulai Belanja</a>
            </div>
        
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="product-card" style="flex-direction:row; padding:10px; align-items:center; gap:15px;">
                        
                        <div style="width:80px; height:80px; flex-shrink:0; background:#f4f4f4; border-radius:8px; overflow:hidden;">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        
                        <div style="flex:1;">
                            <h4 style="font-size:0.95rem; margin-bottom:3px;"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p style="font-size:0.85rem; color:#888; margin-bottom:5px;">
                                Size: <b><?php echo $item['size']; ?></b> | Qty: <b><?php echo $item['qty']; ?></b>
                            </p>
                            <div style="color:var(--accent); font-weight:700;">
                                Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>
                            </div>
                        </div>

                        <a href="cart.php?action=delete&index=<?php echo $index; ?>" onclick="return confirm('Hapus produk ini?');" style="color:#e74c3c; padding:10px;">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="fixed-bottom-action" style="flex-direction:column; gap:10px; border-top:1px solid #eee;">
                <div style="display:flex; justify-content:space-between; width:100%; font-size:1.1rem; font-weight:700;">
                    <span>Total Pembayaran</span>
                    <span style="color:var(--accent);">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                </div>
                
                <a href="<?php echo $checkout_link; ?>" target="_blank" class="btn-primary" style="background:#2ecc71; display:flex; justify-content:center; align-items:center; gap:10px;">
                    <i class="fab fa-whatsapp" style="font-size:1.2rem;"></i> Checkout via WhatsApp
                </a>
            </div>
        <?php endif; ?>

    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php"><i class="fas fa-home"></i></a>
        <a href="cart.php" class="active"><i class="fas fa-shopping-bag"></i></a>
        <a href="admin.php"><i class="fas fa-user"></i></a>
    </nav>

</body>
</html>