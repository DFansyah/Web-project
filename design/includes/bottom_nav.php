<nav class="bottom-nav-capsule">
    <a href="index.php"><i class="fas fa-home"></i></a>
    <div class="nav-center-btn">
        <a href="cart.php">
            <i class="fas fa-shopping-bag"></i>
            <?php if (isset($cart_count) && $cart_count > 0): ?>
                <span class="badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
    <a href="<?php echo $is_logged_in
      ? "logout.php"
      : "login.php"; ?>"><i class="fas fa-user"></i></a>
</nav>