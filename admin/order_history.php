<?php
include('../dbconnect.php');

if (!isset($_GET['user_id'])) {
  echo "ไม่พบข้อมูลลูกค้า";
  exit;
}

$user_id = intval($_GET['user_id']);

// ดึงข้อมูลลูกค้า
$user = $conn->query("SELECT * FROM users WHERE user_id = $user_id")->fetch_assoc();
$user_email = $conn->real_escape_string($user['email']);

// ดึงประวัติการสั่งซื้อ
$sql = "SELECT * FROM orders WHERE user_email = '$user_email' ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
  echo "Query Error: " . $conn->error;
  exit;
}
include('admin.php');
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ประวัติการสั่งซื้อของ <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

    body {
      font-family: 'Kanit', sans-serif;
      background: #fff7f0;

    }

    .container {
      max-width: 900px;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin: 30px auto;
    }

    h1 {
      text-align: center;
      color: #5d4037;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }

    th,
    td {
      padding: 12px;
      border: 1px solid #ddd;
    }

    th {
      background-color: #ffe4e1;
      color: #8d5544;
    }

    tr:hover {
      background-color: #fff0f5;
    }

    a.view-link {
      color: #e56b90;
      text-decoration: none;
    }

    a.view-link:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>🛍 ประวัติการสั่งซื้อของ <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
    <?php if ($result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>รหัสการสั่งซื้อ</th>
            <th>วันที่สั่งซื้อ</th>
            <th>ยอดรวม</th>
            <th>สถานะ</th>
            <th>ดูรายละเอียด</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['order_no']) ?></td>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['created_at']))) ?></td>
              <td><?= number_format($row['grand_total'], 2) ?> บาท</td>
              <td><?= htmlspecialchars($row['order_status']) ?></td>
              <td><a class="view-link" href="order_detail.php?order_id=<?= $row['order_id'] ?>">ดู</a></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="text-align: center;">ยังไม่มีคำสั่งซื้อ</p>
    <?php endif; ?>
  </div>
</body>

</html>