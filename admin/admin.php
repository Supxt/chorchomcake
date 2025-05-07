<?php
session_start();
include_once('../dbconnect.php');

// ตรวจสอบสิทธิ์เข้าใช้งาน
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@cc.co') {
  echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location.href = '../index.php';</script>";
  exit();
}
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
  <?php
  include('../components/sidebar.php');
  ?>

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