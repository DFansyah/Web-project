<?php
ob_start();
session_start();
require 'db.php';

// Cek Session: Jika sudah login, lempar
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php"); exit;
}
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = trim($_POST['input']);
    $pass = $_POST['password'];

    // Cek Admin
    if ($input === 'admin' && $pass === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        unset($_SESSION['user_id']);
        header("Location: admin.php"); exit;
    }

    // Cek User
    try {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? OR name = ?");
        $stmt->execute([$input, $input]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            unset($_SESSION['admin_logged_in']);
            header("Location: index.php"); exit;
        } else {
            $msg = "Username/Email tidak ditemukan atau Password salah!";
        }
    } catch (PDOException $e) { $msg = "Database Error: " . $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BrandKamu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-sign-in-alt"></i>
            <h2>Selamat Datang</h2>
            <p>Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if(isset($_GET['registered'])): ?>
            <div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; margin-bottom:20px; font-size:0.9rem;">
                <i class="fas fa-check-circle"></i> Akun berhasil dibuat! Silakan login.
            </div>
        <?php endif; ?>

        <?php if($msg): ?>
            <div style="background:#ffecec; color:#e74c3c; padding:12px; border-radius:8px; margin-bottom:20px; font-size:0.9rem; border:1px solid #ffdcdc;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="input" placeholder="Email atau Username" required value="<?php echo isset($_POST['input']) ? htmlspecialchars($_POST['input']) : ''; ?>">
                <i class="fas fa-user"></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <button type="submit" class="btn-auth">Masuk Sekarang</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar Disini</a>
            <div style="margin-top:15px;">
                <a href="index.php" style="color:#999; font-weight:400; font-size:0.85rem;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

</body>
</html>