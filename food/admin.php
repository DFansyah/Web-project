<?php
ob_start();
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }
$upload_dir = 'assets/images/';
if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);

// --- UPDATE KATEGORI DISINI ---
$categories = ['Makanan', 'Minuman'];

// Hapus Produk
if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$_GET['del']]);
    $img = $stmt->fetchColumn();
    if($img && file_exists($img)) unlink($img);
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$_GET['del']]);
    header("Location: admin.php"); exit;
}

// Tambah / Edit Produk
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = (int)$_POST['stock'];
    $cat   = $_POST['category']; 
    $desc  = $_POST['description'];
    $img_path = $_POST['old_img'] ?? '';

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file);
        $img_path = $upload_dir . $file;
    }

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $sql = "UPDATE products SET name=?, price=?, stock=?, category=?, description=?, image=? WHERE id=?";
        $pdo->prepare($sql)->execute([$name, $price, $stock, $cat, $desc, $img_path, $_POST['id']]);
    } else {
        $sql = "INSERT INTO products (name, price, stock, category, description, image) VALUES (?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $price, $stock, $cat, $desc, $img_path]);
    }
    header("Location: admin.php"); exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - APHPMART</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

    <header class="top-header">
        <div class="brand-logo">ADMIN<b>KU</b></div>
        <nav class="desktop-nav">
            <a href="index.php" target="_blank">Lihat Produk</a>
            <a href="logout.php">Keluar</a>
        </nav>
        <a href="logout.php" class="mobile-auth-btn btn-m-out" onclick="return confirm('Keluar Admin?');"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </header>

    <main class="main-content">
        <div class="admin-form-card">
            <h3><?php echo $edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
            <form method="POST" enctype="multipart/form-data" style="margin-top:15px;">
                <input type="hidden" name="id" value="<?php echo $edit['id']??''; ?>">
                <input type="hidden" name="old_img" value="<?php echo $edit['image']??''; ?>">
                
                <div class="form-group-admin">
                    <label>Nama Produk</label>
                    <input type="text" name="name" required value="<?php echo $edit['name']??''; ?>">
                </div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group-admin">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" required value="<?php echo $edit['price']??''; ?>">
                    </div>
                    <div class="form-group-admin">
                        <label>Stok</label>
                        <input type="number" name="stock" required value="<?php echo $edit['stock']??''; ?>">
                    </div>
                </div>

                <div class="form-group-admin">
                    <label>Kategori</label>
                    <select name="category" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; background:white;">
                        <?php foreach($categories as $c): ?>
                            <option value="<?php echo $c; ?>" <?php echo ($edit && $edit['category'] == $c) ? 'selected' : ''; ?>>
                                <?php echo $c; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-admin">
                    <label>Deskripsi</label>
                    <textarea name="description"><?php echo $edit['description']??''; ?></textarea>
                </div>
                <div class="form-group-admin">
                    <label>Foto Produk</label>
                    <input type="file" name="image" accept="image/*" <?php echo $edit?'':'required'; ?>>
                </div>
                <button type="submit" class="btn-primary"><?php echo $edit?'Simpan Perubahan':'Upload Produk'; ?></button>
                <?php if($edit): ?> <a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:#777;">Batal</a> <?php endif; ?>
            </form>
        </div>

        <h3 style="margin-bottom:15px;">Daftar Produk Aktif</h3>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($p['image']); ?>" style="height:150px;">
                <div class="info">
                    <span style="font-size:0.7rem; background:#eee; padding:3px 8px; border-radius:10px; width:fit-content; margin-bottom:5px; color:#666;">
                        <?php echo htmlspecialchars($p['category']); ?>
                    </span>
                    <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                    <p class="price">Rp <?php echo number_format($p['price'],0,',','.'); ?></p>
                    <p style="font-size:0.8rem; color:#666;">Stok: <b><?php echo $p['stock']; ?></b></p>
                </div>
                <div class="admin-actions" style="padding:10px;">
                    <a href="admin.php?edit=<?php echo $p['id']; ?>" class="btn-action btn-edit">Edit</a>
                    <a href="admin.php?del=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus?');">Hapus</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>