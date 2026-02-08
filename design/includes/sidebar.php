<div class="overlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Menu Utama</h3>
        <span class="close-btn" onclick="toggleSidebar()">&times;</span>
    </div>
    <div class="sidebar-content">
        <div class="user-profile-widget">
            <div class="avatar-circle"><i class="fas fa-user"></i></div>
            <div>
                <?php if ($is_logged_in): ?>
                    <p style="margin:0; font-size:0.8rem;">Halo,</p>
                    <div style="font-weight:600;"><?php echo htmlspecialchars($user_name); ?></div>
                    <a href="logout.php" style="color:#e74c3c; font-size:0.8rem;">Keluar</a>
                <?php else: ?>
                    <p style="margin:0; font-size:0.8rem;">Selamat Datang,</p>
                    <a href="login.php" style="color:#e67e22; font-weight:600;">Masuk / Daftar</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="menu-section">
            <h4>KATEGORI</h4>
            <ul>
                <li><a href="#">Kaos Custom</a></li>
                <li><a href="#">Hoodie & Jaket</a></li>
            </ul>
        </div>
    </div>
</aside>