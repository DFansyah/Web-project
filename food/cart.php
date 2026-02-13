<?php
ob_start();
session_start();
require 'db.php';

// Inisialisasi Keranjang
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Logic Hapus Item
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    array_splice($_SESSION['cart'], $_GET['index'], 1);
    header("Location: cart.php"); exit;
}

// Hitung Total Belanja
$total = 0;
$item_list = "";
foreach ($_SESSION['cart'] as $item) {
    $sub = $item['price'] * $item['qty'];
    $total += $sub;
    $note = $item['note'] ? " (" . $item['note'] . ")" : "";
    $item_list .= "- " . $item['name'] . $note . " x" . $item['qty'] . "\n";
}

// Nomor WA Admin
$admin_wa = "628567555560"; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Style Tambahan untuk Form Nama */
        .checkout-box {
            position: fixed; bottom: 80px; left: 0; width: 100%; 
            padding: 20px; background: white; 
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
            border-radius: 20px 20px 0 0;
            z-index: 1000;
        }
        .name-input {
            width: 100%; padding: 12px; border: 1px solid #ddd;
            border-radius: 10px; margin-bottom: 10px; font-family: 'Poppins';
            outline: none; transition: 0.3s;
        }
        .name-input:focus { border-color: var(--primary); }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-logo">PESANAN<b>SAYA</b></div>
        <nav class="desktop-nav">
            <a href="index.php">Beranda</a>
        </nav>
    </header>

    <main class="main-content" style="padding-bottom: 220px;">
        <?php if (empty($_SESSION['cart'])): ?>
            <div style="text-align:center; padding-top:50px; color:#aaa;">
                <i class="fas fa-shopping-basket" style="font-size:4rem; margin-bottom:15px;"></i>
                <p>Keranjang masih kosong.</p>
                <a href="index.php" style="color:var(--primary); font-weight:700;">Lihat Produk</a>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($_SESSION['cart'] as $i => $item): ?>
                    <div class="product-card" style="flex-direction:row; padding:10px; align-items:center; gap:15px;">
                        <img src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" style="width:70px; height:70px; border-radius:10px; object-fit:cover;">
                        <div style="flex:1;">
                            <h4 style="font-size:0.95rem; margin-bottom:5px;"><?php echo htmlspecialchars($item['name'] ?? ''); ?></h4>
                            <p style="font-size:0.8rem; color:#666;">
                                <?php echo !empty($item['note']) ? "Note: ".$item['note']." | " : ""; ?>Qty: <?php echo $item['qty']; ?>
                            </p>
                            <div style="color:var(--primary); font-weight:700; margin-top:5px;">
                                Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <a href="cart.php?action=delete&index=<?php echo $i; ?>" style="color:var(--danger); padding:10px;">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="checkout-box">
                <div style="margin-bottom: 10px; font-weight: 700; color: var(--text);">Informasi Pemesan:</div>
                <input type="text" id="buyer_name" class="name-input" placeholder="Masukkan Nama Lengkap Anda..." required>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span style="font-size:0.9rem; color:#666;">Total Bayar:</span>
                    <span style="font-weight:800; color:var(--primary); font-size:1.1rem;">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>

                <button onclick="sendWhatsApp()" class="btn-primary" style="display:block; width:100%; border:none;">
                    <i class="fab fa-whatsapp"></i> Checkout Pesanan
                </button>
            </div>
        <?php endif; ?>
    </main>

    <nav class="bottom-nav-capsule">
        <a href="index.php"><i class="fas fa-home"></i></a>
        <a href="cart.php" class="active"><i class="fas fa-shopping-basket"></i></a>
        <a href="<?php echo isset($_SESSION['admin_logged_in']) ? 'admin.php' : 'login.php'; ?>">
            <i class="fas fa-user-cog"></i>
        </a>
    </nav>

    <script>
    function sendWhatsApp() {
        const name = document.getElementById('buyer_name').value;
        
        if (name.trim() === "") {
            alert("Silakan masukkan nama Anda terlebih dahulu!");
            document.getElementById('buyer_name').focus();
            return;
        }

        const adminWA = "<?php echo $admin_wa; ?>";
        const total = "<?php echo number_format($total, 0, ',', '.'); ?>";
        const items = `<?php echo $item_list; ?>`;
        
        // Format Pesan
        let message = "Halo min, saya mau pesan\n\n";
        message += "Nama : " + name + "\n";
        message += "--------------------------------\n";
        message += "Detail Pesanan :\n";
        message += items;
        message += "\nTotal Bayar: Rp " + total;
        message += "\n\nMohon diproses ya min. Terima kasih!";

        // Encode URI dan Buka WhatsApp
        const waLink = "https://wa.me/" + adminWA + "?text=" + encodeURIComponent(message);
        window.open(waLink, '_blank');
    }
    </script>

</body>
</html>