<?php
ob_start();
session_start();
require_once "auth/auth_check.php";
require_once "config/database.php";

$user_id = $_SESSION["user_id"];
$error = "";

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $photo_path = $user["profile_photo"];
    $new_photo_uploaded = !empty($_FILES["profile_photo"]["name"]);

    if ($full_name === "") {
        $error = "Full name cannot be empty.";
    } else {
        if ($new_photo_uploaded) {
            $file = $_FILES["profile_photo"];
            $allowed_types = ["image/jpeg", "image/png", "image/jpg"];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file["type"], $allowed_types)) {
                $error = "Only JPG and PNG files are allowed.";
            } elseif ($file["size"] > $max_size) {
                $error = "File size must be less than 2MB.";
            } else {
                $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $filename = uniqid() . "." . $ext;
                $target = "uploads/" . $filename;

                if (move_uploaded_file($file["tmp_name"], $target)) {
                    $photo_path = $target;
                } else {
                    $error = "Failed to upload file.";
                }
            }
        }

        if ($error === "") {
            if ($full_name === $user["full_name"] && !$new_photo_uploaded) {
                $_SESSION["prof_success"] = "No changes have been made.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, profile_photo = ? WHERE user_id = ?");
                $stmt->execute([$full_name, $photo_path, $user_id]);
                $_SESSION["profile_photo"] = $photo_path;
                $_SESSION["prof_success"] = "Profile updated successfully.";
            }

            // JavaScript replace overwrites the current entry in history
            echo "<script>window.location.replace('profile.php');</script>";
            exit;
        }
    }
}

require_once "includes/header.php"; 
?>

<div class="profile-card">
    <!-- Back button for easier navigation -->
    <a href="index.php" style="text-decoration:none; color:#999; font-size:12px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-bottom:20px;">
        <i class="fa fa-arrow-left" style="font-size:10px; margin-right:5px;"></i> Back to Dashboard
    </a>

    <div class="profile-header">
        <div class="profile-avatar-wrapper">
            <?php if (!empty($user["profile_photo"])): ?>
                <img src="<?php echo htmlspecialchars($user["profile_photo"]); ?>" class="profile-avatar">
            <?php else: ?>
                <img src="https://via.placeholder.com/120" class="profile-avatar">
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h3><?php echo htmlspecialchars($user["full_name"]); ?></h3>
            <p>@<?php echo htmlspecialchars($user["username"]); ?> | <?php echo strtoupper($user["role"]); ?></p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="message error">
            <i class="fa fa-exclamation-circle"></i> 
            <ul><li><?php echo $error; ?></li></ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION["prof_success"])): ?>
        <p class="message success">
            <?php echo $_SESSION["prof_success"]; unset($_SESSION["prof_success"]); ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="profile-form-section">
        <label for="full_name">Full Name</label>
        <div class="input-group">
            <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user["full_name"]); ?>">
        </div>

        <label for="profile_photo">Change Profile Picture</label>
        <div class="input-group">
            <input type="file" name="profile_photo" id="profile_photo" style="border-bottom: none; font-size: 13px;">
        </div>

        <button type="submit">Update Account</button>
    </form>

    <div class="action-links-grid">
        <a href="change_password.php"><i class="fa fa-key"></i> Security</a>
        <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    </div>
</div>

<script>
    // This cleans up the history state immediately after loading
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<?php require_once "includes/footer.php"; ?>