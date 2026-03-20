<?php
ob_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
?>

<?php require_once "includes/header.php"; ?>

<div class="container" style="max-width: 800px; margin-top: 50px; padding: 40px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">

    <?php if (isset($_SESSION["prof_success"])): ?>
        <p class="message success">
            <?php echo $_SESSION["prof_success"]; unset($_SESSION["prof_success"]); ?>
        </p>
    <?php endif; ?>

    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin-bottom: 10px;">Welcome Back, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        <p class="subtitle" style="text-align: center; margin-bottom: 0;">Member Status: <?php echo strtoupper($_SESSION["role"]); ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px;">
        <a href="profile.php" style="display: block; padding: 30px; border: 1px solid #eee; text-decoration: none; color: black; transition: all 0.3s ease; text-align: center;">
            <i class="fa fa-user" style="font-size: 24px; margin-bottom: 15px; display: block;"></i>
            <span style="font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 13px;">My Profile</span>
        </a>
        
        <a href="change_password.php" style="display: block; padding: 30px; border: 1px solid #eee; text-decoration: none; color: black; transition: all 0.3s ease; text-align: center;">
            <i class="fa fa-key" style="font-size: 24px; margin-bottom: 15px; display: block;"></i>
            <span style="font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 13px;">Security</span>
        </a>

        <a href="auth/logout.php" class="logout-link" style="display: block; padding: 30px; border: 1px solid #eee; text-decoration: none; color: black; transition: all 0.3s ease; text-align: center;">
            <i class="fa fa-sign-out-alt" style="font-size: 24px; margin-bottom: 15px; display: block;"></i>
            <span style="font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 13px;">Logout</span>
        </a>
    </div>

</div>

<style>
    .container a:hover {
        border-color: #000;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
</style>

<script>
    // Logic to intercept the browser back button
    (function() {
        // Push a dummy state to the history
        history.pushState(null, null, window.location.href);

        window.addEventListener('popstate', function (event) {
            // When user hits 'Back', show confirmation
            if (confirm("Are you sure you want to log out?")) {
                // If yes, redirect to logout script
                window.location.href = "auth/logout.php";
            } else {
                // If no, push the dummy state again to stay on index
                history.pushState(null, null, window.location.href);
            }
        });
    })();

    window.addEventListener( "pageshow", function ( event ) {
        if ( event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2) ) {
            window.location.reload();
        }
    });
</script>

<?php require_once "includes/footer.php"; ?>