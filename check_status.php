<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

$user_email = $_SESSION['email'] ?? null;
$result = null;

if ($user_email) {
  $sql = "SELECT order_id, order_no, full_name, user_email, created_at, order_status
          FROM orders
          WHERE user_email = ?
          ORDER BY created_at DESC";

  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Prepare failed: " . $conn->error); // เช็คว่ามี error อะไร
  }

  $stmt->bind_param("s", $user_email);
  $stmt->execute();
  $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ตรวจสอบสถานะการสั่งซื้อ</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff7f0;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-top: 10px;
    }

    h1 {
      text-align: center;
      color: #c67878;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #f0d9d9;
      color: #7d4b4b;
    }

    .btn {
      background-color: #f48fb1;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
    }

    .btn:hover {
      background-color: #e56b90;
    }

    .center-message {
      text-align: center;
      margin-top: 30px;
    }

    .login-buttons a {
      display: inline-block;
      margin: 0 10px;
      padding: 10px 20px;
      background-color: #f48fb1;
      color: white;
      border-radius: 8px;
      text-decoration: none;
    }

    .login-buttons a:hover {
      background-color: #e56b90;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>📦 ตรวจสอบสถานะการสั่งซื้อ</h1>

    <?php if (!$user_email): ?>
      <div class="center-message">
        <p>กรุณาลงชื่อเข้าใช้เพื่อดูคำสั่งซื้อของคุณ</p>
        <div class="login-buttons">
          <a href="login.php">เข้าสู่ระบบ</a>
          <a href="register.php">สมัครสมาชิก</a>
        </div>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>รหัสการสั่งซื้อ</th>
            <th>วันที่สั่งซื้อ</th>
            <th>ชื่อผู้สั่งซื้อ</th>
            <th>อีเมล</th>
            <th>สถานะการสั่งซื้อ</th>
            <th>ตรวจสอบข้อมูล</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['order_no']) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['created_at']))) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td><?= htmlspecialchars($row['order_status']) ?></td>
                <td>
                  <a class="btn" href="order_detail.php?order_id=<?= $row['order_id'] ?>">ดูรายละเอียด</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">ไม่มีข้อมูลการสั่งซื้อของคุณ</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>
