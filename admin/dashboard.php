<?php
include('../dbconnect.php');
// --- คำนวณข้อมูลต่าง ๆ ---
$dateToday = date('Y-m-d');
$monthNow = date('Y-m');
$yearNow = date('Y');

$todaySales = 0;
$monthSales = 0;
$yearSales = 0;
$totalSales = 0;
$todayOrders = 0;
$totalOrders = 0;
$totalStock = 0;

// ยอดขายวันนี้
$sql_today = "SELECT SUM(total_price) AS todaySales, COUNT(*) AS todayOrders FROM orders WHERE DATE(order_date) = '$dateToday'";
$result_today = $conn->query($sql_today);
if ($row = $result_today->fetch_assoc()) {
  $todaySales = $row['todaySales'] ?: 0;
  $todayOrders = $row['todayOrders'] ?: 0;
}

// ยอดขายเดือนนี้
$sql_month = "SELECT SUM(total_price) AS monthSales FROM orders WHERE DATE_FORMAT(order_date, '%Y-%m') = '$monthNow'";
$result_month = $conn->query($sql_month);
if ($row = $result_month->fetch_assoc()) {
  $monthSales = $row['monthSales'] ?: 0;
}

// ยอดขายปีนี้
$sql_year = "SELECT SUM(total_price) AS yearSales FROM orders WHERE YEAR(order_date) = '$yearNow'";
$result_year = $conn->query($sql_year);
if ($row = $result_year->fetch_assoc()) {
  $yearSales = $row['yearSales'] ?: 0;
}

// ยอดขายรวม
$sql_total = "SELECT SUM(total_price) AS totalSales, COUNT(*) AS totalOrders FROM orders";
$result_total = $conn->query($sql_total);
if ($row = $result_total->fetch_assoc()) {
  $totalSales = $row['totalSales'] ?: 0;
  $totalOrders = $row['totalOrders'] ?: 0;
}

// สินค้าคงเหลือ
$sql_stock = "SELECT SUM(quantity) AS totalStock FROM product";
$result_stock = $conn->query($sql_stock);
if ($row = $result_stock->fetch_assoc()) {
  $totalStock = $row['totalStock'] ?: 0;
}

// สินค้าขายดี
$sql_bestsellers = "
SELECT p.p_name, SUM(od.qty) AS total_qty
FROM order_detail od
JOIN product p ON od.p_id = p.p_id
GROUP BY od.p_id
ORDER BY total_qty DESC
LIMIT 5
";
$result_bestsellers = $conn->query($sql_bestsellers);
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ด</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

    body {
      margin: 0;
      font-family: 'Kanit', sans-serif;
      background: #fff7f9;
    }

    .navbar {
      background-color: #d7ccc8;
      color: #5d4037;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar .title {
      font-size: 20px;
    }

    .logout-btn {
      background-color: #ef9a9a;
      border: none;
      padding: 8px 16px;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .logout-btn:hover {
      background-color: #e57373;
    }

    .sidebar {
      width: 220px;
      background: #fff0f5;
      height: 100vh;
      position: fixed;
      overflow-y: auto;
      padding-top: 20px;
      border-right: 1px solid #ddd;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      padding: 12px 20px;
      color: #8d5544;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar li:hover,
    .sidebar li.active {
      background-color: #ffc0cb;
      color: #fff;
    }

    .sidebar li i {
      margin-right: 10px;
    }

    .submenu {
      display: none;
      background-color: #ffe4e1;
      padding-left: 20px;
    }

    .submenu a {
      display: block;
      padding: 8px 0;
      color: #8d5544;
      text-decoration: none;
    }

    .submenu a:hover {
      color: #d13c3c;
    }

    .content {
      margin-left: 240px;
      padding: 40px 20px;
    }

    .card {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      border: 1px solid #f8bbd0;
      max-width: 1000px;
      margin: auto;
      text-align: center;
    }

    .card h1 {
      margin-bottom: 10px;
      color: #6d4c41;
    }

    .card p {
      color: #795548;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="title"><i class="fas fa-user-shield"></i> แอดมิน</div>
    <button class="logout-btn" onclick="logout()">ออกจากระบบ</button>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-chart-line"></i> แดชบอร์ด</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">วิเคราะห์ข้อมูล</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-bell"></i> คำสั่งซื้อ</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รอดำเนินการ</a></li>
        <li><a href="#">จัดส่งแล้ว</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-store"></i> สินค้าทั้งหมด</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="manage_product.php">จัดการสินค้า</a></li>
        <li><a href="add_product.php">เพิ่มสินค้า</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-users"></i> ผู้ใช้งาน</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รายชื่อผู้ใช้</a></li>
        <li><a href="#">สิทธิ์การเข้าถึง</a></li>
      </ul>
    </ul>
  </div>

  <!-- Content Dashboard -->
  <div class="content">
    <h1 style="text-align: center; margin-bottom: 30px;">แดชบอร์ด</h1>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">

      <div class="card">
        <h2>ยอดจำหน่ายวันนี้</h2>
        <p><?= number_format($todaySales, 2) ?> บาท</p>
      </div>

      <div class="card">
        <h2>ยอดจำหน่ายเดือนนี้</h2>
        <p><?= number_format($monthSales, 2) ?> บาท</p>
      </div>

      <div class="card">
        <h2>ยอดจำหน่ายปีนี้</h2>
        <p><?= number_format($yearSales, 2) ?> บาท</p>
      </div>

      <div class="card">
        <h2>ยอดจำหน่ายรวม</h2>
        <p><?= number_format($totalSales, 2) ?> บาท</p>
      </div>

      <div class="card">
        <h2>สินค้าคงเหลือ</h2>
        <p><?= number_format($totalStock) ?> ชิ้น</p>
      </div>

      <div class="card">
        <h2>คำสั่งซื้อวันนี้</h2>
        <p><?= number_format($todayOrders) ?> รายการ</p>
      </div>

      <div class="card">
        <h2>ยอดคำสั่งซื้อรวม</h2>
        <p><?= number_format($totalOrders) ?> รายการ</p>
      </div>

      <div class="card" style="flex: 1 1 100%;">
        <h2>สินค้าขายดี</h2>
        <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
          <thead>
            <tr style="background-color: #fce4ec;">
              <th style="padding: 10px; border: 1px solid #f8bbd0;">สินค้า</th>
              <th style="padding: 10px; border: 1px solid #f8bbd0;">จำนวนที่ขายได้</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result_bestsellers->fetch_assoc()) { ?>
              <tr>
                <td style="padding: 10px; border: 1px solid #f8bbd0;"><?= htmlspecialchars($row['p_name']) ?></td>
                <td style="padding: 10px; border: 1px solid #f8bbd0;"><?= number_format($row['total_qty']) ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script>
    function toggleMenu(el) {
      const next = el.nextElementSibling;
      if (next && next.classList.contains('submenu')) {
        next.style.display = next.style.display === 'block' ? 'none' : 'block';
      }
    }

    function logout() {
      if (confirm('คุณแน่ใจว่าต้องการออกจากระบบ?')) {
        window.location.href = '../login.php';
      }
    }
  </script>

</body>

</html>