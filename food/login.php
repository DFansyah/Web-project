<?php
ob_start();
session_start();
require 'db.php';

// Jika sudah login admin, langsung ke admin.php
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php"); exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = trim($_POST['input']);
    $pass = $_POST['password'];

    // Hardcode Admin: admin / admin123
    if ($input === 'admin' && $pass === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php"); exit;
    } else {
        $msg = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - APHPMART</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-user-shield"></i>
            <h2>APHPMART</h2>
            <p>Masuk sebagai Admin</p>
        </div>

        <?php if($msg): ?>
            <div style="background:#ffecec; color:#c0392b; padding:10px; border-radius:8px; margin-bottom:20px; font-size:0.9rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="input" placeholder="Username Admin" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn-auth">Masuk</button>
        </form>

        <div class="auth-footer">
            <div style="margin-top:15px;">
                <a href="index.php" style="color:#999; font-weight:400;">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

</body>
</html>