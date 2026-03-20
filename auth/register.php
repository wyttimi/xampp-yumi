<?php
ob_start();
session_start();
require_once "../config/database.php";

$errors = []; 
$old_full_name = "";
$old_username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $username = strtolower(trim($_POST["username"] ?? ""));
    $full_name = trim($_POST["full_name"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    $old_full_name = $full_name;
    $old_username = $username;

    if ($username === "" || $full_name === "" || $password === "" || $confirm_password === "") {
        $errors[] = "All fields are required.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, or underscores (4-20 chars).";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least 1 uppercase letter and 1 number.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username already exists. Please choose another.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password_hash, role) VALUES (?, ?, ?, 'member')");
                $stmt->execute([$username, $full_name, $hash]);

                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(["success" => true, "message" => "Registration successful! You can login now."]);
                    exit;
                }
                $_SESSION["reg_success"] = "Registration successful! You can login now.";
                echo "<script>window.location.replace('register.php');</script>";
                exit;
            } catch (PDOException $e) {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }

    if ($is_ajax && !empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "errors" => $errors]);
        exit;
    }
}

require_once "../includes/header.php"; 
?>

<div class="login-page-wrapper">
    <div class="login-image-side" style="background-image: url('https://images.unsplash.com/photo-1581044777550-4cfa60707998?auto=format&fit=crop&q=80&w=2070');"></div>
    <div class="login-form-side">
        <div class="container">
            <h2>Join Us</h2>
            <p class="subtitle">Create an account to start shopping</p>

            <?php if (!empty($errors)): ?>
                <div class="message error">
                    <i class="fa fa-exclamation-circle"></i> 
                    <ul><?php foreach ($errors as $err): ?><li><?php echo $err; ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION["reg_success"])): ?>
                <p class="message success">
                    <?php echo $_SESSION["reg_success"]; unset($_SESSION["reg_success"]); ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <label for="full_name">Full Name</label>
                <div class="input-group">
                    <input type="text" name="full_name" id="full_name" placeholder="Your full name" 
                           value="<?php echo htmlspecialchars($old_full_name); ?>" required>
                </div>

                <label for="username">Username</label>
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Choose a username" 
                           value="<?php echo htmlspecialchars($old_username); ?>" required>
                </div>

                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Create a password" required>
                    <i class="fa fa-eye password-toggle" id="togglePasswordIcon" data-target="password"></i>
                </div>

                <label for="confirm_password">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat your password" required>
                    <i class="fa fa-eye password-toggle" id="toggleConfirmPasswordIcon" data-target="confirm_password"></i>
                </div>

                <button type="submit">Register</button>
            </form>

            <div class="auth-links">
                <a href="login.php">Already have an account? <strong>Login</strong></a>
            </div>
        </div>
    </div>
</div>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function() {
        $('.container form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('Registering...');
            $('.container .message').remove();

            $.ajax({
                url: 'register.php',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    var msgClass = res.success ? 'success' : 'error';
                    var icon = res.success ? 'fa-check-circle' : 'fa-exclamation-circle';
                    var content = '';
                    if (res.errors) {
                        content = '<ul>';
                        res.errors.forEach(function(err) { content += '<li>' + err + '</li>'; });
                        content += '</ul>';
                    } else {
                        content = ' ' + res.message;
                    }
                    var msgHtml = '<div class="message ' + msgClass + '"><i class="fa ' + icon + '"></i>' + content + '</div>';
                    form.before(msgHtml);
                    if (res.success) form[0].reset();
                },
                error: function() {
                    form.before('<div class="message error"><i class="fa fa-exclamation-circle"></i> Something went wrong. Please try again.</div>');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Register');
                }
            });
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>