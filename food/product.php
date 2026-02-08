<?php
session_start();
require 'db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if(!$p) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $_SESSION['cart'][] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'qty' => (int)$_POST['quantity'],
        'note' => $_POST['note'], // Ganti Size jadi Note
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
        <div class="brand-logo">DETAIL<b>MENU</b></div>
        <a href="cart.php"><i class="fas fa-shopping-basket"></i></a>
    </header>

    <main class="main-content">
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($p['image']); ?>">
        </div>

        <h1 class="product-detail-title"><?php echo htmlspecialchars($p['name']); ?></h1>
        <div class="product-detail-price">Rp <?php echo number_format($p['price'],0,',','.'); ?></div>
        <p class="product-detail-desc"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
        
        <form method="POST" style="background:#f9f9f9; padding:20px; border-radius:15px;">
            <div style="margin-bottom: 15px;">
                <label style="font-weight:600; display:block; margin-bottom:5px;">Catatan (Opsional)</label>
                <input type="text" name="note" placeholder="Contoh: Pedas, Jangan pakai sayur..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="font-weight:600; display:block; margin-bottom:5px;">Jumlah Porsi</label>
                <input type="number" name="quantity" value="1" min="1" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
            </div>

            <button type="submit" name="add_to_cart" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah ke Pesanan
            </button>
        </form>
    </main>
</body>
</html>