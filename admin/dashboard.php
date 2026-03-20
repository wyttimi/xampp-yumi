<?php
require_once "../auth/admin_check.php";
require_once "../includes/header.php";
?>

<div class="container" style="margin-top: 50px; min-height: 60vh;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 32px;">Admin Dashboard</h1>
        <div style="background: #f8f8f8; padding: 10px 20px; border-radius: 5px; font-size: 14px;">
            Logged in as: <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong> (Admin)
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div style="background: white; border: 1px solid #eee; padding: 30px; text-align: center; transition: 0.3s;">
            <i class="fa fa-users" style="font-size: 40px; margin-bottom: 15px; color: #333;"></i>
            <h3 style="margin-bottom: 10px;">User Management</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Manage registered customers and administrators.</p>
            <a href="#" style="display: inline-block; background: #000; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Manage Users</a>
        </div>

        <div style="background: white; border: 1px solid #eee; padding: 30px; text-align: center; transition: 0.3s;">
            <i class="fa fa-tshirt" style="font-size: 40px; margin-bottom: 15px; color: #333;"></i>
            <h3 style="margin-bottom: 10px;">Product Management</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Add, edit, or remove clothing items from the shop.</p>
            <a href="#" style="display: inline-block; background: #000; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Manage Products</a>
        </div>

        <div style="background: white; border: 1px solid #eee; padding: 30px; text-align: center; transition: 0.3s;">
            <i class="fa fa-shopping-bag" style="font-size: 40px; margin-bottom: 15px; color: #333;"></i>
            <h3 style="margin-bottom: 10px;">Order Management</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Track and process customer orders.</p>
            <a href="#" style="display: inline-block; background: #000; color: #fff; text-decoration: none; padding: 10px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">View Orders</a>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>