<?php
ob_start();
session_start();
require_once "auth/auth_check.php";
require_once "config/database.php";

$user_id = $_SESSION["user_id"];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = trim($_POST["current_password"] ?? "");
    $new_password = trim($_POST["new_password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    if ($current_password === "" || $new_password === "" || $confirm_password === "") {
        $errors[] = "All fields are required.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $errors[] = "Password must contain at least 1 uppercase letter and 1 number.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user["password_hash"])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$new_hash, $user_id]);
            $_SESSION["pass_success"] = "Password updated successfully.";
            
            // JavaScript replace overwrites the current entry in history
            echo "<script>window.location.replace('change_password.php');</script>";
            exit;
        } else {
            $errors[] = "Current password is incorrect.";
        }
    }
}

require_once "includes/header.php"; 
?>

<div class="profile-card">
    <a href="profile.php" style="text-decoration:none; color:#999; font-size:12px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-bottom:20px;">
        <i class="fa fa-arrow-left" style="font-size:10px; margin-right:5px;"></i> Back to Profile
    </a>

    <div class="profile-header">
        <h2 style="font-family: 'Playfair Display', serif; margin-bottom: 10px;">Security</h2>
        <p class="subtitle" style="text-align: center;">Manage your account password</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="message error">
            <i class="fa fa-exclamation-circle"></i> 
            <ul><?php foreach ($errors as $err): ?><li><?php echo $err; ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION["pass_success"])): ?>
        <p class="message success">
            <?php echo $_SESSION["pass_success"]; unset($_SESSION["pass_success"]); ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="profile-form-section">
        <label for="current_password">Current Password</label>
        <div class="input-group">
            <input type="password" name="current_password" id="current_password" placeholder="Enter current password" required>
            <i class="fa fa-eye password-toggle" id="toggleCurrentIcon" data-target="current_password"></i>
        </div>

        <label for="new_password">New Password</label>
        <div class="input-group">
            <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
            <i class="fa fa-eye password-toggle" id="toggleNewIcon" data-target="new_password"></i>
        </div>

        <label for="confirm_password">Confirm New Password</label>
        <div class="input-group">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat new password" required>
            <i class="fa fa-eye password-toggle" id="toggleConfirmIcon" data-target="confirm_password"></i>
        </div>

        <button type="submit">Update Password</button>
    </form>

    <div class="action-links-grid">
        <a href="profile.php"><i class="fa fa-user"></i> My Profile</a>
        <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    </div>
</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<?php require_once "includes/footer.php"; ?>