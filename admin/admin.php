<?php
session_start();
include_once('../dbconnect.php');

// Fetch all products
/*
$sql = "SELECT * FROM product";
$result = $conn->query($sql); 
*/
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>Admin</title>
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
        <li><a href="dashboard.php">วิเคราะห์ข้อมูล</a></li>
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