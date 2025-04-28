<!-- navbar.php -->
<?php

$current_page = basename($_SERVER['PHP_SELF']);

// ตรวจสอบว่า user คลิก "logout"
if (isset($_GET['logout'])) {
    session_unset();      // ล้างตัวแปรใน session
    session_destroy();    // ทำลาย session
    header("Location: login.php"); // 
    exit();
}
?>

<div class="topnav">
    <div class="logo">
        <img src="image/img2.png" alt="Logo">
        <span style="color: white; font-size: 22px; font-weight: bold;">Chorchom Cake</span>
    </div>
    <div class="menu-left">
        <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">หน้าแรก</a>
        <a href="product.php" class="<?= $current_page == 'product.php' ? 'active' : '' ?>">รายการสินค้า</a>
        <a href="how-to-order.php" class="<?= $current_page == 'how-to-order.php' ? 'active' : '' ?>">ขั้นตอนการสั่งซื้อ</a>
        <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">ติดต่อเรา</a>
        <a href="check_status.php" class="<?= $current_page == 'check_status.php' ? 'active' : '' ?>">ตรวจสอบสถานะ</a>
    </div>
    <div class="menu-right">
        <?php if (isset($_SESSION['success'])) : ?>
            <span style="color:white; margin-right:10px;">
                <a href="user_profile.php" style="color:white;">
                    สวัสดี,

                    <?= htmlspecialchars($_SESSION['first_name']) ?>
                </a>
            </span>
            <a href="?logout=true">ออกจากระบบ</a>
        <?php else : ?>
            <a href="register.php" class="<?= $current_page == 'register.php' ? 'active' : '' ?>">สมัครสมาชิก</a>
            <a href="login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>">เข้าสู่ระบบ</a>
        <?php endif; ?>
        <a href="cart_view.php" class="right">
            🛒 <span id="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0; ?></span>
        </a>
    </div>
</div>

<style>
    body {
        margin: 0;
    }

    .topnav {
        overflow: hidden;
        background-color: #d79577;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 16px;
    }

    .topnav .menu-left {
        display: flex;
    }

    .topnav .menu-right {
        margin-left: auto;
    }

    .topnav a {
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 17px;
    }

    .topnav a:hover {
        background-color: #ddd;
        color: pink;
    }

    .topnav a.active {
        background-color: rgb(141, 85, 68);
        color: white;
    }

    .logo {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

    .logo img {
        height: 40px;
        margin-right: 10px;
    }

    .cart-icon {
        height: 28px;
        vertical-align: middle;
    }
</style>