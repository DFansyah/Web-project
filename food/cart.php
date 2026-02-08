<?php
ob_start();
session_start();
require 'db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    array_splice($_SESSION['cart'], $_GET['index'], 1);
    header("Location: cart.php"); exit;
}

$total = 0;
$wa_msg = "Halo Foodies, saya mau pesan:\n\n";
foreach ($_SESSION['cart'] as $item) {
    $sub = $item['price'] * $item['qty'];
    $total += $sub;
    $note = $item['note'] ? " (" . $item['note'] . ")" : "";
    $wa_msg .= "- " . $item['name'] . $note . " x" . $item['qty'] . "\n";
}
$wa_msg .= "\nTotal: Rp " . number_format($total, 0, ',', '.');
$checkout_link = "https://wa.me/6281234567890?text=" . urlencode($wa_msg);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">PESANAN<b>KAMU</b></div>
        <nav class="desktop-nav"><a href="index.php">Menu</a></nav>
    </header>

    <main class="main-content">
        <?php if (empty($_SESSION['cart'])): ?>
            <div style="text-align:center; padding-top:50px; color:#aaa;">
                <i class="fas fa-shopping-basket" style="font-size:4rem; margin-bottom:15px;"></i>
                <p>Keranjang masih kosong.</p>
                <a href="index.php" style="color:var(--primary); font-weight:700;">Lihat Menu</a>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($_SESSION['cart'] as $i => $item): ?>
                    <div class="product-card" style="flex-direction:row; padding:10px; align-items:center; gap:15px;">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width:70px; height:70px; border-radius:10px;">
                        <div style="flex:1;">
                            <h4 style="font-size:0.95rem;"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p style="font-size:0.8rem; color:#666;">
                                <?php echo $item['note'] ? "Note: ".$item['note']." | " : ""; ?>Qty: <?php echo $item['qty']; ?>
                            </p>
                            <div style="color:var(--primary); font-weight:700;">
                                Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <a href="cart.php?action=delete&index=<?php echo $i; ?>" style="color:var(--danger); padding:10px;">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="position:fixed; bottom:80px; left:0; width:100%; padding:20px; background:transparent; pointer-events:none;">
                <a href="<?php echo $checkout_link; ?>" target="_blank" class="btn-primary" style="pointer-events:auto; display:block; box-shadow:0 10px 20px rgba(192, 57, 43, 0.4);">
                    Checkout WhatsApp - Rp <?php echo number_format($total, 0, ',', '.'); ?>
                </a>
            </div>
        <?php endif; ?>
    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php"><i class="fas fa-utensils"></i></a>
        <a href="cart.php" class="active"><i class="fas fa-shopping-basket"></i></a>
        <a href="admin.php"><i class="fas fa-user"></i></a>
    </nav>
</body>
</html>