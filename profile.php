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
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $full_name = trim($_POST["full_name"]);
    $photo_path = $user["profile_photo"];
    $new_photo_uploaded = !empty($_FILES["profile_photo"]["name"]);
    $response = ["success" => false, "message" => ""];

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
                $response = ["success" => true, "message" => "No changes have been made."];
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, profile_photo = ? WHERE user_id = ?");
                $stmt->execute([$full_name, $photo_path, $user_id]);
                $_SESSION["profile_photo"] = $photo_path;
                $response = ["success" => true, "message" => "Profile updated successfully.", "full_name" => $full_name, "photo" => $photo_path];
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            $_SESSION["prof_success"] = $response["message"];
            echo "<script>window.location.replace('profile.php');</script>";
            exit;
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => $error]);
        exit;
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
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function() {
        $('.profile-form-section').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(this);
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Updating...');

            // Remove existing messages
            $('.profile-card .message').remove();

            $.ajax({
                url: 'profile.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    var msgClass = res.success ? 'success' : 'error';
                    var icon = res.success ? 'fa-check-circle' : 'fa-exclamation-circle';
                    var msgHtml = '<div class="message ' + msgClass + '"><i class="fa ' + icon + '"></i> ' + res.message + '</div>';
                    form.before(msgHtml);

                    if (res.success && res.full_name) {
                        $('.profile-info h3').text(res.full_name);
                    }
                    if (res.success && res.photo) {
                        $('.profile-avatar').attr('src', res.photo);
                    }
                    // Reset file input
                    $('#profile_photo').val('');
                },
                error: function() {
                    form.before('<div class="message error"><i class="fa fa-exclamation-circle"></i> Something went wrong. Please try again.</div>');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update Account');
                }
            });
        });
    });
</script>

<?php require_once "includes/footer.php"; ?>