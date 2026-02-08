<?php
session_start();
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "Semua kolom wajib diisi!";
    } elseif ($password !== $confirm) {
        $message = "Konfirmasi password tidak cocok!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "Email sudah terdaftar! Silakan login.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hash]);
                header("Location: login.php?registered=success");
                exit();
            } catch (PDOException $e) { $message = "Terjadi kesalahan sistem."; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - BrandKamu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Buat Akun Baru</h2>
            <p>Mulai desain kaos impianmu sekarang</p>
        </div>

        <?php if ($message): ?>
            <div style="background:#ffecec; color:#e74c3c; padding:12px; border-radius:8px; margin-bottom:20px; font-size:0.9rem; border:1px solid #ffdcdc;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="Nama Lengkap" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <i class="fas fa-id-card"></i>
            </div>
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Alamat Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Buat Password" required>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Ulangi Password" required>
                <i class="fas fa-check-circle"></i>
            </div>
            
            <button type="submit" class="btn-auth">Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Masuk Disini</a>
            <div style="margin-top:15px;">
                <a href="index.php" style="color:#999; font-weight:400; font-size:0.85rem;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

</body>
</html>