<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

// ดึงข้อมูล order ทั้งหมด (หรือจะให้กรองตาม email เฉพาะผู้ใช้งานก็ได้)
$sql = "SELECT order_id, order_no, full_name, user_email, created_at, order_status FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
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

    th,
    td {
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
  </style>
</head>

<body>
  <div class="container">
    <h1>📦 ตรวจสอบสถานะการสั่งซื้อ</h1>
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
        <?php if ($result->num_rows > 0): ?>
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
            <td colspan="6">ไม่มีข้อมูลการสั่งซื้อ</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>

</html>