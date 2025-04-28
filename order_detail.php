<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

if (!isset($_GET['order_id'])) {
  header("Location: check_status.php");
  exit;
}

$order_id = intval($_GET['order_id']);

// ดึงข้อมูลคำสั่งซื้อ
$sql_order = "SELECT * FROM orders WHERE order_id = $order_id";
$result_order = $conn->query($sql_order);

if ($result_order->num_rows == 0) {
  echo "ไม่พบข้อมูลการสั่งซื้อ";
  exit;
}

$order = $result_order->fetch_assoc();

// **ถ้ามีระบบแยกรายการสินค้าตามออร์เดอร์ ก็เพิ่ม SQL ตรงนี้**
// เช่น ดึงจากตาราง order_items แต่ตอนนี้เอาเฉพาะข้อมูลรวมมาก่อน
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>รายละเอียดการสั่งซื้อ</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff7f0;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      color: #c67878;
      margin-bottom: 30px;
    }

    .detail {
      margin-bottom: 20px;
    }

    .detail p {
      margin-bottom: 10px;
      font-size: 16px;
    }

    img.payment-slip {
      max-width: 300px;
      border-radius: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
    }

    .back-btn {
      display: block;
      margin-top: 30px;
      text-align: center;
    }

    .btn {
      background-color: #f48fb1;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
    }

    .btn:hover {
      background-color: #e56b90;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>📄 รายละเอียดการสั่งซื้อ</h1>

    <div class="detail">
      <p><strong>รหัสการสั่งซื้อ:</strong> <?= htmlspecialchars($order['order_no']) ?></p>
      <p><strong>วันที่สั่งซื้อ:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['created_at']))) ?></p>
      <p><strong>ชื่อผู้สั่งซื้อ:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
      <p><strong>อีเมล:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
      <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($order['tel']) ?></p>
      <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($order['address']) ?></p>
      <p><strong>วันที่รับสินค้า:</strong> <?= htmlspecialchars($order['receive_date']) ?></p>
      <p><strong>จำนวนสินค้า:</strong> <?= htmlspecialchars($order['total_qty']) ?> ชิ้น</p>
      <p><strong>ราคาก่อน VAT:</strong> <?= number_format($order['total_price'], 2) ?> บาท</p>
      <p><strong>ภาษีมูลค่าเพิ่ม (7%):</strong> <?= number_format($order['vat'], 2) ?> บาท</p>
      <p><strong>รวมราคาทั้งสิ้น:</strong> <?= number_format($order['grand_total'], 2) ?> บาท</p>
      <p><strong>ช่องทางการชำระเงิน:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
      <p><strong>สถานะการสั่งซื้อ:</strong> <?= htmlspecialchars($order['order_status']) ?></p>

      <?php if (!empty($order['payment_slip'])): ?>
        <p><strong>หลักฐานการชำระเงิน:</strong></p>
        <img class="payment-slip" src="image/<?= htmlspecialchars($order['payment_slip']) ?>" alt="หลักฐานการชำระเงิน">
      <?php endif; ?>
    </div>

    <div class="back-btn">
      <a class="btn" href="check_status.php">🔙 กลับไปหน้าตรวจสอบสถานะ</a>
    </div>
  </div>
</body>

</html>