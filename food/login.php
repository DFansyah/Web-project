<?php
ob_start();
session_start();
require 'db.php';

if (isset($_SESSION['user_id']) || isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php"); exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = trim($_POST['input']);
    $pass = $_POST['password'];

    // Hardcode Admin: admin / admin123
    if ($input === 'admin' && $pass === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php"); exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? OR name = ?");
        $stmt->execute([$input]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php"); exit;
        } else {
            $msg = "Email tidak ditemukan atau Password salah!";
        }
    } catch (PDOException $e) { $msg = "Error DB."; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Foodies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-utensils"></i> <h2>Selamat Datang</h2>
            <p>Silakan masuk untuk memesan makanan.</p>
        </div>

        <?php if($msg): ?>
            <div style="background:#ffecec; color:#c0392b; padding:10px; border-radius:8px; margin-bottom:20px; font-size:0.9rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="input" placeholder="Email Kamu" required>
                <i class="fas fa-envelope"></i>
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
                <a href="index.php" style="color:#999; font-weight:400;">Kembali ke Menu</a>
            </div>
        </div>
    </div>

</body>
</html>