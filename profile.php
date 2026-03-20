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

// Calculate days since joined
$joined_days = floor((time() - strtotime($user["created_at"])) / 86400);
$joined_text = $joined_days < 1 ? "Today" : $joined_days . " days ago";
?>

<!-- Profile Cover -->
<div class="pf-cover">
    <div class="pf-cover-gradient"></div>
    <div class="pf-cover-pattern"></div>
</div>

<!-- Profile Hero Card (overlaps cover) -->
<div class="pf-hero">
    <div class="pf-hero-card">
        <div class="pf-avatar-ring">
            <?php if (!empty($user["profile_photo"])): ?>
                <img src="<?php echo htmlspecialchars($user["profile_photo"]); ?>" class="pf-avatar-img" alt="Profile">
            <?php else: ?>
                <div class="pf-avatar-placeholder"><i class="fa fa-user"></i></div>
            <?php endif; ?>
            <div class="pf-avatar-status"></div>
        </div>
        <h2 class="pf-hero-name"><?php echo htmlspecialchars($user["full_name"]); ?></h2>
        <p class="pf-hero-handle">@<?php echo htmlspecialchars($user["username"]); ?></p>
        <div class="pf-hero-badges">
            <span class="pf-badge pf-badge-role"><i class="fa fa-crown"></i> <?php echo ucfirst($user["role"]); ?></span>
            <span class="pf-badge pf-badge-date"><i class="fa fa-calendar"></i> Joined <?php echo date("M Y", strtotime($user["created_at"])); ?></span>
        </div>
        <div class="pf-hero-stats">
            <div class="pf-stat">
                <span class="pf-stat-num"><?php echo $joined_days; ?></span>
                <span class="pf-stat-label">Days</span>
            </div>
            <div class="pf-stat-divider"></div>
            <div class="pf-stat">
                <span class="pf-stat-num"><?php echo strtoupper(substr($user["role"], 0, 1)); ?></span>
                <span class="pf-stat-label">Tier</span>
            </div>
            <div class="pf-stat-divider"></div>
            <div class="pf-stat">
                <span class="pf-stat-num"><i class="fa fa-check-circle" style="color:#2f855a;font-size:18px;"></i></span>
                <span class="pf-stat-label">Verified</span>
            </div>
        </div>
    </div>
</div>

<div class="pf-body">

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

    <div class="pf-grid">
        <!-- Left Column: Details + Quick Links -->
        <div class="pf-col-left">
            <div class="pf-card">
                <div class="pf-card-header">
                    <span class="pf-card-icon"><i class="fa fa-id-card"></i></span>
                    <h3>Account Details</h3>
                </div>
                <div class="pf-detail">
                    <div class="pf-detail-icon"><i class="fa fa-signature"></i></div>
                    <div class="pf-detail-content">
                        <span class="pf-detail-label">Full Name</span>
                        <span class="pf-detail-value pf-info-name"><?php echo htmlspecialchars($user["full_name"]); ?></span>
                    </div>
                </div>
                <div class="pf-detail">
                    <div class="pf-detail-icon"><i class="fa fa-at"></i></div>
                    <div class="pf-detail-content">
                        <span class="pf-detail-label">Username</span>
                        <span class="pf-detail-value"><?php echo htmlspecialchars($user["username"]); ?></span>
                    </div>
                </div>
                <div class="pf-detail">
                    <div class="pf-detail-icon"><i class="fa fa-user-tag"></i></div>
                    <div class="pf-detail-content">
                        <span class="pf-detail-label">Role</span>
                        <span class="pf-detail-value"><span class="pf-role-pill"><?php echo ucfirst($user["role"]); ?></span></span>
                    </div>
                </div>
                <div class="pf-detail pf-detail-last">
                    <div class="pf-detail-icon"><i class="fa fa-clock"></i></div>
                    <div class="pf-detail-content">
                        <span class="pf-detail-label">Member Since</span>
                        <span class="pf-detail-value"><?php echo date("F j, Y", strtotime($user["created_at"])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="pf-card pf-card-actions">
                <div class="pf-card-header">
                    <span class="pf-card-icon"><i class="fa fa-bolt"></i></span>
                    <h3>Quick Actions</h3>
                </div>
                <a href="change_password.php" class="pf-action">
                    <div class="pf-action-left">
                        <div class="pf-action-icon pf-action-icon-shield"><i class="fa fa-shield-halved"></i></div>
                        <div>
                            <span class="pf-action-title">Security</span>
                            <span class="pf-action-desc">Change your password</span>
                        </div>
                    </div>
                    <i class="fa fa-arrow-right pf-action-arrow"></i>
                </a>
                <a href="index.php" class="pf-action">
                    <div class="pf-action-left">
                        <div class="pf-action-icon pf-action-icon-home"><i class="fa fa-home"></i></div>
                        <div>
                            <span class="pf-action-title">Dashboard</span>
                            <span class="pf-action-desc">Back to homepage</span>
                        </div>
                    </div>
                    <i class="fa fa-arrow-right pf-action-arrow"></i>
                </a>
            </div>
        </div>

        <!-- Right Column: Edit Form -->
        <div class="pf-col-right">
            <div class="pf-card">
                <div class="pf-card-header">
                    <span class="pf-card-icon"><i class="fa fa-pen-to-square"></i></span>
                    <h3>Edit Profile</h3>
                </div>
                <form method="POST" enctype="multipart/form-data" class="pf-edit-form profile-form-section">
                    <label for="full_name">Full Name</label>
                    <div class="input-group">
                        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user["full_name"]); ?>">
                    </div>

                    <label for="profile_photo">Profile Picture</label>
                    <div class="pf-upload-zone" id="pf-upload-zone">
                        <div class="pf-upload-preview" id="pf-upload-preview">
                            <?php if (!empty($user["profile_photo"])): ?>
                                <img src="<?php echo htmlspecialchars($user["profile_photo"]); ?>" alt="Current" class="pf-upload-thumb">
                            <?php else: ?>
                                <i class="fa fa-cloud-arrow-up pf-upload-icon-big"></i>
                            <?php endif; ?>
                        </div>
                        <div class="pf-upload-info">
                            <span class="pf-upload-text">Drag & drop or click to upload</span>
                            <span class="pf-upload-hint">JPG or PNG, max 2MB</span>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png">
                    </div>
                    <div class="pf-upload-name" id="pf-upload-name"></div>

                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== Profile Cover ===== */
    .pf-cover {
        height: 200px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 70%, #533483 100%);
        position: relative;
        overflow: hidden;
    }
    .pf-cover-gradient {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 50%, rgba(212,175,55,0.15) 0%, transparent 60%),
                    radial-gradient(circle at 80% 30%, rgba(83,52,131,0.3) 0%, transparent 50%);
    }
    .pf-cover-pattern {
        position: absolute;
        inset: 0;
        opacity: 0.03;
        background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%);
        background-size: 20px 20px;
    }

    /* ===== Hero Card ===== */
    .pf-hero {
        display: flex;
        justify-content: center;
        margin-top: -90px;
        position: relative;
        z-index: 2;
        padding: 0 20px;
    }
    .pf-hero-card {
        background: #fff;
        padding: 35px 50px 30px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        width: 100%;
        max-width: 480px;
    }
    .pf-avatar-ring {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto 18px;
        padding: 4px;
        background: linear-gradient(135deg, #d4af37, #f5e6a3, #d4af37);
        border-radius: 50%;
    }
    .pf-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
    }
    .pf-avatar-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #f5f5f5;
        border: 3px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #ccc;
    }
    .pf-avatar-status {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 16px;
        height: 16px;
        background: #2f855a;
        border: 3px solid #fff;
        border-radius: 50%;
    }
    .pf-hero-name {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        margin: 0 0 4px;
        color: #000;
        text-align: center;
    }
    .pf-hero-handle {
        color: #aaa;
        font-size: 14px;
        margin: 0 0 18px;
        letter-spacing: 0.5px;
    }
    .pf-hero-badges {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .pf-badge {
        font-size: 11px;
        padding: 5px 14px;
        border-radius: 20px;
        letter-spacing: 0.5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pf-badge i { font-size: 10px; }
    .pf-badge-role {
        background: linear-gradient(135deg, #fff9e6, #fff3cc);
        color: #b8860b;
        border: 1px solid #f0e0a0;
    }
    .pf-badge-date {
        background: #f7f7f7;
        color: #888;
        border: 1px solid #eee;
    }
    .pf-hero-stats {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        padding-top: 22px;
        border-top: 1px solid #f0f0f0;
    }
    .pf-stat { text-align: center; }
    .pf-stat-num {
        display: block;
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: #000;
    }
    .pf-stat-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #aaa;
        margin-top: 2px;
        display: block;
    }
    .pf-stat-divider {
        width: 1px;
        height: 35px;
        background: #eee;
    }

    /* ===== Body & Grid ===== */
    .pf-body {
        max-width: 900px;
        margin: 35px auto 60px;
        padding: 0 20px;
    }
    .pf-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }

    /* ===== Cards ===== */
    .pf-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        box-shadow: 0 6px 25px rgba(0,0,0,0.05);
        margin-bottom: 22px;
        overflow: hidden;
    }
    .pf-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pf-card-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 17px;
        font-weight: 700;
        margin: 0;
    }
    .pf-card-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #fff9e6, #fff3cc);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
        color: #d4af37;
    }

    /* ===== Account Details ===== */
    .pf-detail {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid #fafafa;
        transition: background 0.2s;
    }
    .pf-detail:hover { background: #fcfcfc; }
    .pf-detail-last { border-bottom: none; }
    .pf-detail-icon {
        width: 36px;
        height: 36px;
        background: #f8f8f8;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #999;
        flex-shrink: 0;
    }
    .pf-detail-content { display: flex; flex-direction: column; gap: 2px; }
    .pf-detail-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #bbb;
        font-weight: 600;
    }
    .pf-detail-value {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }
    .pf-role-pill {
        font-size: 11px;
        padding: 3px 12px;
        background: linear-gradient(135deg, #fff9e6, #fff3cc);
        color: #b8860b;
        border: 1px solid #f0e0a0;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* ===== Quick Actions ===== */
    .pf-card-actions { margin-bottom: 0; }
    .pf-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        text-decoration: none;
        color: #1a1a1a;
        border-bottom: 1px solid #fafafa;
        transition: all 0.2s;
    }
    .pf-action:last-child { border-bottom: none; }
    .pf-action:hover { background: #fcfcfc; padding-left: 28px; }
    .pf-action-left { display: flex; align-items: center; gap: 14px; }
    .pf-action-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .pf-action-icon-shield { background: #eef6ff; color: #3b82f6; }
    .pf-action-icon-home { background: #f0fdf4; color: #22c55e; }
    .pf-action-title { display: block; font-weight: 600; font-size: 13px; letter-spacing: 0.5px; }
    .pf-action-desc { display: block; font-size: 11px; color: #aaa; margin-top: 1px; }
    .pf-action-arrow { color: #ddd; font-size: 12px; transition: all 0.2s; }
    .pf-action:hover .pf-action-arrow { color: #333; transform: translateX(3px); }

    /* ===== Edit Form ===== */
    .pf-edit-form {
        padding: 24px !important;
        border-top: none !important;
    }

    /* Upload Zone */
    .pf-upload-zone {
        display: flex;
        align-items: center;
        gap: 18px;
        border: 2px dashed #e0e0e0;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        margin-bottom: 8px;
    }
    .pf-upload-zone:hover {
        border-color: #d4af37;
        background: #fffdf5;
    }
    .pf-upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    .pf-upload-preview {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid #eee;
    }
    .pf-upload-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pf-upload-icon-big {
        font-size: 22px;
        color: #ccc;
    }
    .pf-upload-info { display: flex; flex-direction: column; gap: 3px; }
    .pf-upload-text { font-size: 13px; color: #666; font-weight: 500; }
    .pf-upload-hint { font-size: 11px; color: #bbb; }
    .pf-upload-name { font-size: 12px; color: #d4af37; padding: 4px 0; min-height: 18px; font-weight: 500; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .pf-grid { grid-template-columns: 1fr; }
        .pf-hero-card { padding: 30px 25px 25px; }
        .pf-hero-name { font-size: 22px; }
        .pf-hero-stats { gap: 20px; }
        .pf-cover { height: 160px; }
    }
    @media (max-width: 480px) {
        .pf-hero-badges { flex-direction: column; align-items: center; }
        .pf-upload-zone { flex-direction: column; text-align: center; }
    }
</style>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function() {
        // Show selected file name + preview
        $('#profile_photo').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#pf-upload-name').text(file.name);
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#pf-upload-preview').html('<img src="' + e.target.result + '" class="pf-upload-thumb" alt="Preview">');
                };
                reader.readAsDataURL(file);
            } else {
                $('#pf-upload-name').text('');
            }
        });

        // Drag highlight
        $('#pf-upload-zone').on('dragover', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#d4af37').css('background', '#fffdf5');
        }).on('dragleave drop', function() {
            $(this).css('border-color', '#e0e0e0').css('background', '');
        });

        // AJAX form submit
        $('.profile-form-section').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(this);
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Saving...');

            $('.pf-body > .message').remove();

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
                    $('.pf-body').prepend(msgHtml);

                    if (res.success && res.full_name) {
                        $('.pf-hero-name').text(res.full_name);
                        $('.pf-info-name').text(res.full_name);
                    }
                    if (res.success && res.photo) {
                        $('.pf-avatar-img').attr('src', res.photo);
                        $('.pf-upload-thumb').attr('src', res.photo);
                    }
                    $('#profile_photo').val('');
                    $('#pf-upload-name').text('');
                },
                error: function() {
                    $('.pf-body').prepend('<div class="message error"><i class="fa fa-exclamation-circle"></i> Something went wrong. Please try again.</div>');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Changes');
                }
            });
        });
    });
</script>

<?php require_once "includes/footer.php"; ?>