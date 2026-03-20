<?php
ob_start();
session_start();
require_once "../config/database.php";

$errors = [];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $new_password = trim($_POST["new_password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    if ($username === "" || $full_name === "" || $new_password === "" || $confirm_password === "") {
        $errors[] = "All fields are required.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $errors[] = "Password must contain at least 1 uppercase letter and 1 number.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, password_hash FROM users WHERE username = ? AND full_name = ?");
        $stmt->execute([$username, $full_name]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($new_password, $user["password_hash"])) {
                $errors[] = "New password must be different from the old password.";
            } else {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $stmt->execute([$new_hash, $user["user_id"]]);
                $_SESSION["reset_success"] = "Password reset successful. You can login now.";
                // JavaScript replace overwrites the current entry in history
                echo "<script>window.location.replace('reset_password.php');</script>";
                exit;
            }
        } else {
            $errors[] = "Username and full name do not match any account.";
        }
    }
}

require_once "../includes/header.php"; 
?>

<div class="login-page-wrapper">
    <div class="login-image-side" style="background-image: url('https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&q=80&w=2070');"></div>
    <div class="login-form-side">
        <div class="container">
            <h2>Reset Password</h2>
            <p class="subtitle">Enter your details to reset your password</p>

            <?php if (!empty($errors)): ?>
                <div class="message error">
                    <i class="fa fa-exclamation-circle"></i> 
                    <ul><?php foreach ($errors as $err): ?><li><?php echo $err; ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION["reset_success"])): ?>
                <p class="message success">
                    <?php echo $_SESSION["reset_success"]; unset($_SESSION["reset_success"]); ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <label for="username">Username</label>
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Your username" required>
                </div>

                <label for="full_name">Full Name</label>
                <div class="input-group">
                    <input type="text" name="full_name" id="full_name" placeholder="Your full name" required>
                </div>

                <label for="new_password">New Password</label>
                <div class="input-group">
                    <input type="password" name="new_password" id="new_password" placeholder="Choose a new password" required>
                    <i class="fa fa-eye password-toggle" id="toggleNewIcon" data-target="new_password"></i>
                </div>

                <label for="confirm_password">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat new password" required>
                    <i class="fa fa-eye password-toggle" id="toggleConfirmIcon" data-target="confirm_password"></i>
                </div>

                <button type="submit">Reset Password</button>
            </form>

            <div class="auth-links">
                <a href="login.php">Back to <strong>Login</strong></a>
            </div>
        </div>
    </div>
</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<?php require_once "../includes/footer.php"; ?>