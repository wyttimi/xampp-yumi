<?php
ob_start();
header("Cache-Control: no-cache, must-revalidate");
session_start();
require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $selected_role = $_POST["role"] ?? "member"; // Get the selected role

    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {

        // Check for username and selected role to ensure they are logging into the right account type
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $selected_role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password_hash"])) {

            session_regenerate_id(true);

            $_SESSION["profile_photo"] = $user["profile_photo"] ?? null;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $error = "Invalid credentials for the selected role.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VELURA</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="/clothing_shop/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="login-page-wrapper">
    <div class="login-image-side">
        <img src="/clothing_shop/banner/Login Banner.jpg" class="login-banner" alt="Login">
    </div>
    <div class="login-form-side">
        <div class="container">
            <!-- Logo -->
            <div class="auth-logo">
                <div class="auth-logo-mark">V</div>
                <div class="auth-logo-text">
                    <span class="auth-logo-name">VELURA</span>
                    <span class="auth-logo-sub">CLOTHING CO.</span>
                </div>
            </div>

            <h2>Welcome Back</h2>
            <p class="subtitle">Please enter your details to continue</p>

            <?php if ($error): ?>
                <div class="message error">
                    <i class="fa fa-exclamation-circle"></i> 
                    <ul>
                        <li><?php echo $error; ?></li>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <!-- Professional Role Selector -->
                <label>Login As</label>
                <div class="role-selector">
                    <input type="radio" name="role" value="member" id="role_member" checked>
                    <label for="role_member" class="role-option">Member</label>

                    <input type="radio" name="role" value="admin" id="role_admin">
                    <label for="role_admin" class="role-option">Admin</label>
                </div>

                <label for="username">Username</label>
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required autofocus>
                </div>

                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <i class="fa fa-eye password-toggle" id="toggleIcon" data-target="password"></i>
                </div>

                <button type="submit">Log In</button>
            </form>

            <div class="auth-links">
                <a href="register.php">Don't have an account? <strong>Register</strong></a>
                <a href="reset_password.php">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Boutique Role Selector Styling */
.role-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
}

.role-selector input[type="radio"] {
    display: none;
}

.role-option {
    flex: 1;
    text-align: center;
    padding: 10px;
    border: 1px solid #eee;
    font-size: 12px !important;
    cursor: pointer;
    transition: 0.3s;
    letter-spacing: 1px;
    font-weight: 400 !important;
    color: #999 !important;
    margin-bottom: 0 !important;
}

.role-selector input[type="radio"]:checked + .role-option {
    background: #000;
    color: #fff !important;
    border-color: #000;
}

/* Auth Page Logo */
.auth-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 40px;
}
.auth-logo-mark {
    width: 38px;
    height: 38px;
    background: #000;
    color: #d4af37;
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1;
}
.auth-logo-name {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 700;
    color: #000;
    letter-spacing: 3px;
}
.auth-logo-sub {
    font-size: 8px;
    letter-spacing: 3px;
    color: #999;
    margin-top: 3px;
    font-weight: 500;
}
</style>

<script src="/clothing_shop/assets/js/script.js"></script>
</body>
</html>