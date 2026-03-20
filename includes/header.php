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

    <nav style="background:black; color:white; padding:15px; display:flex; justify-content:space-between; align-items:center;">

        <a href="/clothing_shop/index.php" style="font-size:20px; font-weight:bold; font-family: 'Playfair Display', serif; color:white; text-decoration:none;">
            Clothing Shop
        </a>

        <div style="display:flex; align-items:center; gap:20px;">
            <a href="/clothing_shop/index.php" style="color:white; text-decoration:none; font-size:14px; text-transform:uppercase; letter-spacing:1px;">Home</a>

            <?php if (isset($_SESSION["user_id"])): ?>
                <?php if ($_SESSION["role"] === 'admin'): ?>
                    <a href="/clothing_shop/admin/dashboard.php" style="color:#d4af37; text-decoration:none; font-size:14px; text-transform:uppercase; letter-spacing:1px; font-weight:bold;">Admin Dashboard</a>
                <?php endif; ?>
                <div class="profile-menu">
                    <div class="profile-trigger">
                        <img src="<?php echo $_SESSION["profile_photo"] ?? 'https://via.placeholder.com/40'; ?>"
                            style="width:30px; height:30px; border-radius:50%; object-fit:cover; border:1px solid #fff;">
                        <span style="font-size:13px; font-weight:500;"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    </div>

                    <div class="dropdown">
                        <a href="/clothing_shop/profile.php">My Profile</a>
                        <a href="/clothing_shop/auth/logout.php" class="logout-link">Logout</a>
                    </div>                </div>
            <?php else: ?>
                <a href="/clothing_shop/auth/login.php" style="color:white; text-decoration:none; font-size:14px; text-transform:uppercase; letter-spacing:1px;">Login</a>
                <a href="/clothing_shop/auth/register.php" style="color:white; text-decoration:none; font-size:14px; text-transform:uppercase; letter-spacing:1px;">Register</a>
            <?php endif; ?>
        </div>

    </nav>