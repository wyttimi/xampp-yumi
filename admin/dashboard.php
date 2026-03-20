<?php
require_once "../auth/admin_check.php";
require_once "../config/database.php";

// Fetch counts for dashboard stats
$member_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$admin_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

require_once "../includes/header.php";
?>

<!-- Admin Hero -->
<div class="admin-hero">
    <div class="admin-hero-overlay"></div>
    <div class="admin-hero-content">
        <p class="admin-hero-tagline">Administration Panel</p>
        <h1 class="admin-hero-title">Dashboard</h1>
        <p class="admin-hero-sub">Welcome back, <?php echo htmlspecialchars($_SESSION["username"]); ?></p>
    </div>
</div>

<div class="admin-dashboard-wrapper">

    <!-- Stats Row -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $member_count; ?></div>
            <div class="stat-label">Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $admin_count; ?></div>
            <div class="stat-label">Admins</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Orders</div>
        </div>
    </div>

    <!-- Management Sections -->
    <h3 class="admin-section-title">Management</h3>
    <div class="admin-cards">

        <a href="#" class="admin-card">
            <div class="admin-card-icon"><i class="fa fa-users"></i></div>
            <div class="admin-card-body">
                <h4>User Management</h4>
                <p>Manage registered customers and administrators.</p>
            </div>
            <i class="fa fa-chevron-right admin-card-arrow"></i>
        </a>

        <a href="#" class="admin-card">
            <div class="admin-card-icon"><i class="fa fa-tshirt"></i></div>
            <div class="admin-card-body">
                <h4>Product Management</h4>
                <p>Add, edit, or remove clothing items from the shop.</p>
            </div>
            <i class="fa fa-chevron-right admin-card-arrow"></i>
        </a>

        <a href="#" class="admin-card">
            <div class="admin-card-icon"><i class="fa fa-shopping-bag"></i></div>
            <div class="admin-card-body">
                <h4>Order Management</h4>
                <p>Track and process customer orders.</p>
            </div>
            <i class="fa fa-chevron-right admin-card-arrow"></i>
        </a>

    </div>

    <!-- Quick Links -->
    <h3 class="admin-section-title">Account</h3>
    <div class="admin-quick-links">
        <a href="/clothing_shop/profile.php" class="admin-quick-link">
            <i class="fa fa-user"></i> My Profile
        </a>
        <a href="/clothing_shop/change_password.php" class="admin-quick-link">
            <i class="fa fa-shield-halved"></i> Security
        </a>
        <a href="/clothing_shop/auth/logout.php" class="admin-quick-link logout-link">
            <i class="fa fa-sign-out-alt"></i> Log Out
        </a>
    </div>

</div>

<style>
    /* Admin Hero */
    .admin-hero {
        position: relative;
        height: 260px;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .admin-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #000 0%, #1a1a1a 50%, #333 100%);
    }
    .admin-hero-content {
        position: relative;
        text-align: center;
        color: #fff;
    }
    .admin-hero-tagline {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin-bottom: 15px;
        color: #d4af37;
    }
    .admin-hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 44px;
        font-weight: 700;
        margin: 0 0 12px;
    }
    .admin-hero-sub {
        font-size: 14px;
        color: rgba(255,255,255,0.6);
        letter-spacing: 0.5px;
    }

    /* Dashboard Wrapper */
    .admin-dashboard-wrapper {
        max-width: 800px;
        margin: -40px auto 60px;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    /* Stats */
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 45px;
    }
    .stat-card {
        background: #fff;
        padding: 28px 20px;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
    }
    .stat-number {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 700;
        color: #000;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #999;
    }

    /* Section Title */
    .admin-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 20px;
    }

    /* Management Cards */
    .admin-cards {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 45px;
    }
    .admin-card {
        display: flex;
        align-items: center;
        gap: 22px;
        padding: 24px 28px;
        background: #fff;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        text-decoration: none;
        color: #1a1a1a;
        transition: all 0.3s ease;
    }
    .admin-card:hover {
        border-color: #000;
        transform: translateX(5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .admin-card-icon {
        width: 52px;
        height: 52px;
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #333;
        flex-shrink: 0;
    }
    .admin-card-body {
        flex: 1;
    }
    .admin-card-body h4 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .admin-card-body p {
        margin: 0;
        font-size: 13px;
        color: #999;
    }
    .admin-card-arrow {
        color: #ccc;
        font-size: 12px;
        transition: color 0.3s;
    }
    .admin-card:hover .admin-card-arrow {
        color: #000;
    }

    /* Quick Links */
    .admin-quick-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .admin-quick-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 18px;
        background: #fff;
        border: 1px solid #f0f0f0;
        text-decoration: none;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    .admin-quick-link:hover {
        border-color: #000;
        color: #000;
    }

    @media (max-width: 600px) {
        .admin-hero { height: 200px; }
        .admin-hero-title { font-size: 32px; }
        .admin-stats { grid-template-columns: repeat(2, 1fr); }
        .admin-quick-links { grid-template-columns: 1fr; }
        .admin-card { padding: 18px 20px; }
    }
</style>

<?php require_once "../includes/footer.php"; ?>