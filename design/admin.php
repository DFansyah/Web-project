<?php
ob_start();
session_start();
require 'db.php';

// --- 1. CEK KEAMANAN (ADMIN ONLY) ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$upload_dir = 'assets/images/';
if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);

$error_msg = '';
$edit_data = ['id' => '', 'name' => '', 'price' => '', 'description' => '', 'image' => ''];
$is_edit = false;

// --- 2. LOGIC: APPROVAL DESAIN (TERIMA/TOLAK) ---
if (isset($_GET['action']) && isset($_GET['design_id'])) {
    $design_id = $_GET['design_id'];
    $action = $_GET['action'];

    if ($action == 'reject') {
        // Hapus file gambar & data dari DB
        $stmt = $pdo->prepare("SELECT image_path FROM custom_designs WHERE id = ?");
        $stmt->execute([$design_id]);
        $d = $stmt->fetch();
        if ($d && file_exists($d['image_path'])) unlink($d['image_path']);

        $stmt = $pdo->prepare("DELETE FROM custom_designs WHERE id = ?");
        $stmt->execute([$design_id]);
    } elseif ($action == 'approve') {
        // Update status jadi approved
        $stmt = $pdo->prepare("UPDATE custom_designs SET status = 'approved' WHERE id = ?");
        $stmt->execute([$design_id]);
    }
    header("Location: admin.php");
    exit;
}

// --- 3. LOGIC: MANAJEMEN PRODUK (TAMBAH/EDIT/HAPUS) ---

// A. Hapus Produk
if (isset($_GET['action']) && $_GET['action'] == 'delete_prod' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    // Hapus gambar lama agar tidak menumpuk
    if ($row && !empty($row['image']) && file_exists($row['image'])) unlink($row['image']);

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// B. Persiapan Edit Produk
if (isset($_GET['action']) && $_GET['action'] == 'edit_prod' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch();
    if ($data) {
        $edit_data = $data;
        $is_edit = true;
    }
}

// C. Simpan Produk (Baru/Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $id = $_POST['id'] ?? null;
    $image_path = $_POST['existing_image'] ?? null;

    // Handle Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
            // Hapus gambar lama jika sedang edit
            if ($id && !empty($_POST['existing_image']) && file_exists($_POST['existing_image'])) {
                unlink($_POST['existing_image']);
            }
        }
    }

    try {
        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
            $stmt->execute([$name, $price, $desc, $image_path, $id]);
        } else {
            // Insert Baru
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $desc, $image_path]);
        }
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        $error_msg = "Gagal menyimpan: " . $e->getMessage();
    }
}

// --- 4. AMBIL DATA UNTUK DITAMPILKAN ---
$stmt_prod = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt_prod->fetchAll();

$stmt_des = $pdo->query("SELECT * FROM custom_designs WHERE status = 'pending' ORDER BY created_at DESC");
$designs = $stmt_des->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - BrandKamu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">
    
    <header class="top-header">
        <div class="brand-logo">PANEL<b>ADMIN</b></div>
        
        <nav class="desktop-nav">
            <a href="index.php" target="_blank">Lihat Toko <i class="fas fa-external-link-alt"></i></a>
            <span style="color:var(--gray);">Administrator</span>
            <a href="logout.php" class="btn-login-nav" style="background:var(--danger);">Keluar</a>
        </nav>

        <a href="logout.php" class="mobile-auth-btn btn-m-out" onclick="return confirm('Keluar dari Admin Panel?');">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </header>

    <main class="main-content">
        
        <h3 style="color:var(--primary); margin-bottom:15px; border-left:4px solid var(--blue-action); padding-left:10px;">
            Desain Masuk (<?php echo count($designs); ?>)
        </h3>

        <?php if(empty($designs)): ?>
            <div style="background:white; padding:20px; border-radius:10px; text-align:center; color:#999; margin-bottom:30px; border:1px solid #eee;">
                Belum ada desain baru yang perlu ditinjau.
            </div>
        <?php else: ?>
            <div class="product-grid" style="margin-bottom:40px;">
                <?php foreach ($designs as $d): ?>
                <div class="product-card">
                    <div style="height:250px; background:#eee; display:flex; align-items:center; justify-content:center; padding:10px;">
                        <a href="<?php echo $d['image_path']; ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($d['image_path']); ?>" style="width:100%; height:100%; object-fit:contain;">
                        </a>
                    </div>
                    <div class="info">
                        <h4 style="color:var(--primary);"><?php echo htmlspecialchars($d['user_name']); ?></h4>
                        <p style="font-size:0.8rem; color:var(--gray); margin-bottom:10px;">
                            <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($d['contact']); ?><br>
                            <small><?php echo date('d M Y, H:i', strtotime($d['created_at'])); ?></small>
                        </p>
                        
                        <div class="admin-actions">
                            <a href="https://wa.me/<?php echo preg_replace('/^0/', '62', $d['contact']); ?>?text=Halo%20<?php echo $d['user_name']; ?>,%20terkait%20pesanan%20kaos%20custom..." target="_blank" class="btn-action" style="background:#2ecc71;">
                                <i class="fab fa-whatsapp"></i> Chat
                            </a>
                            <a href="admin.php?action=approve&design_id=<?php echo $d['id']; ?>" class="btn-action btn-edit" onclick="return confirm('Terima desain ini?');">
                                <i class="fas fa-check"></i> Acc
                            </a>
                            <a href="admin.php?action=reject&design_id=<?php echo $d['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus permanen?');">
                                <i class="fas fa-trash"></i> Tolak
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


        <div class="admin-form-card" id="form-area">
            <h3>
                <i class="fas <?php echo $is_edit ? 'fa-pen' : 'fa-plus-circle'; ?>"></i> 
                <?php echo $is_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
            </h3>
            
            <?php if($error_msg): ?>
                <div style="color:red; margin-bottom:10px; font-size:0.9rem;"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_data['image']; ?>">
                <?php endif; ?>
                
                <div class="form-group-admin">
                    <label>Nama Produk</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_data['name']); ?>" required placeholder="Contoh: Kaos Polos Hitam">
                </div>
                
                <div class="form-group-admin">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" value="<?php echo htmlspecialchars($edit_data['price']); ?>" required placeholder="Contoh: 85000">
                </div>
                
                <div class="form-group-admin">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                </div>
                
                <div class="form-group-admin">
                    <label>Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" <?php echo $is_edit ? '' : 'required'; ?>>
                    <?php if($is_edit && !empty($edit_data['image'])): ?>
                        <small style="color:#666;">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="submit_product" class="btn-primary">
                    <?php echo $is_edit ? 'Simpan Perubahan' : 'Upload Produk'; ?>
                </button>
                
                <?php if ($is_edit): ?>
                    <a href="admin.php" class="btn-primary" style="background:#95a5a6; margin-top:10px; display:inline-block;">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>


        <h3 style="color:var(--primary); margin-bottom:15px;">Daftar Produk Aktif</h3>
        
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <div style="height:150px; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="info">
                    <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                    <p class="price">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></p>
                </div>
                <div class="admin-actions">
                    <a href="admin.php?action=edit_prod&id=<?php echo $p['id']; ?>#form-area" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="admin.php?action=delete_prod&id=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?');">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </main>
</body>
</html>