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

// Admin should use the admin dashboard instead
if ($_SESSION["role"] === "admin") {
    header("Location: admin/dashboard.php");
    exit;
}
?>

<?php
require_once "config/database.php";
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();
?>

<?php require_once "includes/header.php"; ?>

<!-- Hero Banner -->
<div class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <p class="hero-tagline">New Season, New Style</p>
        <h1 class="hero-title">Discover Our Collection</h1>
        <p class="hero-sub">Curated fashion pieces for the modern wardrobe</p>
    </div>
</div>

<!-- Welcome Section -->
<div class="dashboard-wrapper">

    <?php if (isset($_SESSION["prof_success"])): ?>
        <p class="message success">
            <?php echo $_SESSION["prof_success"]; unset($_SESSION["prof_success"]); ?>
        </p>
    <?php endif; ?>

    <div class="welcome-section">
        <div class="welcome-avatar">
            <?php if (!empty($user["profile_photo"])): ?>
                <img src="<?php echo htmlspecialchars($user["profile_photo"]); ?>" alt="Profile">
            <?php else: ?>
                <div class="welcome-avatar-placeholder">
                    <i class="fa fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="welcome-text">
            <h2 class="welcome-heading">Welcome back, <?php echo htmlspecialchars($user["full_name"]); ?></h2>
            <p class="welcome-sub">@<?php echo htmlspecialchars($_SESSION["username"]); ?> &middot; <?php echo strtoupper($_SESSION["role"]); ?></p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 class="section-title">Quick Actions</h3>
        <div class="action-cards">
            <a href="profile.php" class="action-card">
                <div class="action-icon"><i class="fa fa-user"></i></div>
                <div class="action-details">
                    <span class="action-name">My Profile</span>
                    <span class="action-desc">View and edit your details</span>
                </div>
                <i class="fa fa-chevron-right action-arrow"></i>
            </a>

            <a href="change_password.php" class="action-card">
                <div class="action-icon"><i class="fa fa-shield-halved"></i></div>
                <div class="action-details">
                    <span class="action-name">Security</span>
                    <span class="action-desc">Update your password</span>
                </div>
                <i class="fa fa-chevron-right action-arrow"></i>
            </a>

            <a href="auth/logout.php" class="action-card logout-link">
                <div class="action-icon action-icon-logout"><i class="fa fa-sign-out-alt"></i></div>
                <div class="action-details">
                    <span class="action-name">Log Out</span>
                    <span class="action-desc">End your current session</span>
                </div>
                <i class="fa fa-chevron-right action-arrow"></i>
            </a>
        </div>
    </div>
</div>

<style>
    /* Hero Banner */
    .hero-banner {
        position: relative;
        height: 340px;
        background: url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&q=80&w=2070') no-repeat center center;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
        position: relative;
        text-align: center;
        color: #fff;
        padding: 0 20px;
    }
    .hero-tagline {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 15px;
        color: #d4af37;
    }
    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 48px;
        font-weight: 700;
        margin: 0 0 15px;
        letter-spacing: -0.5px;
    }
    .hero-sub {
        font-size: 15px;
        color: rgba(255,255,255,0.8);
        letter-spacing: 0.5px;
    }

    /* Dashboard Wrapper */
    .dashboard-wrapper {
        max-width: 700px;
        margin: -50px auto 60px;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    /* Welcome Section */
    .welcome-section {
        background: #fff;
        padding: 35px 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 40px;
    }
    .welcome-avatar img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f0f0f0;
    }
    .welcome-avatar-placeholder {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #ccc;
    }
    .welcome-heading {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        margin: 0 0 5px;
        text-align: left;
    }
    .welcome-sub {
        color: #888;
        font-size: 13px;
        margin: 0;
        letter-spacing: 0.5px;
    }

    /* Quick Actions */
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        margin: 0 0 20px;
        font-weight: 700;
    }
    .action-cards {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .action-card {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 28px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        text-decoration: none;
        color: #1a1a1a;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    .action-card:hover {
        border-color: #000;
        transform: translateX(5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .action-icon {
        width: 48px;
        height: 48px;
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #333;
        flex-shrink: 0;
    }
    .action-icon-logout {
        background: #fff5f5;
        color: #c53030;
    }
    .action-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .action-name {
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .action-desc {
        font-size: 13px;
        color: #999;
    }
    .action-arrow {
        color: #ccc;
        font-size: 12px;
        transition: color 0.3s;
    }
    .action-card:hover .action-arrow {
        color: #000;
    }

    @media (max-width: 600px) {
        .hero-banner { height: 250px; }
        .hero-title { font-size: 32px; }
        .welcome-section { flex-direction: column; text-align: center; padding: 30px 25px; }
        .welcome-heading { text-align: center; font-size: 22px; }
        .action-card { padding: 18px 20px; }
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