<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Shop</title>
    <!-- jQuery Library (Required by Assignment) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="/clothing_shop/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <nav class="main-nav">
        <!-- Logo -->
        <a href="<?php echo (isset($_SESSION["role"]) && $_SESSION["role"] === 'admin') ? '/clothing_shop/admin/dashboard.php' : '/clothing_shop/index.php'; ?>" class="nav-logo">
            <span class="nav-logo-icon"><i class="fa fa-scissors"></i></span>
            Clothing Shop
        </a>

        <!-- Nav Links -->
        <div class="nav-links">
            <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === 'admin'): ?>
                <a href="/clothing_shop/admin/dashboard.php" class="nav-link nav-link-admin">
                    <i class="fa fa-gauge-high"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="/clothing_shop/index.php" class="nav-link">Home</a>
            <?php endif; ?>

            <?php if (isset($_SESSION["user_id"])): ?>

                <div class="nav-divider"></div>

                <div class="profile-menu">
                    <div class="profile-trigger">
                        <?php if (!empty($_SESSION["profile_photo"])): ?>
                            <img src="/clothing_shop/<?php echo htmlspecialchars($_SESSION["profile_photo"]); ?>" class="nav-avatar">
                        <?php else: ?>
                            <div class="nav-avatar-placeholder"><i class="fa fa-user"></i></div>
                        <?php endif; ?>
                        <span class="nav-username"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                        <i class="fa fa-chevron-down nav-caret"></i>
                    </div>

                    <div class="dropdown">
                        <div class="dropdown-header">
                            <span class="dropdown-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                            <span class="dropdown-role"><?php echo strtoupper($_SESSION["role"]); ?></span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="/clothing_shop/profile.php"><i class="fa fa-user"></i> My Profile</a>
                        <a href="/clothing_shop/change_password.php"><i class="fa fa-shield-halved"></i> Security</a>
                        <div class="dropdown-divider"></div>
                        <a href="/clothing_shop/auth/logout.php" class="logout-link dropdown-logout"><i class="fa fa-sign-out-alt"></i> Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/clothing_shop/auth/login.php" class="nav-link">Login</a>
                <a href="/clothing_shop/auth/register.php" class="nav-btn">Register</a>
            <?php endif; ?>
        </div>
    </nav>