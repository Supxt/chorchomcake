<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//   header("Location: checkout.php");
//   exit;
// }

$orderData = $_POST;

$cart = [];
$total = 0;
$total_qty = 0;

if (isset($_SESSION['buy_now'])) {
  $cart = [$_SESSION['buy_now']];
  foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
    $total_qty += $item['qty'];
  }
} else {
  $cart = $_SESSION['cart'] ?? [];
  foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
    $total_qty += $item['qty'];
  }
}

// คำนวณภาษี
$vat = ($total * 7) / 107;
$subtotal = $total - $vat;

// สร้าง order_no และบันทึก order
function generateOrderNo() {
  return 'ORD' . strtoupper(uniqid());
}
$order_no = generateOrderNo();

$email = $orderData['email'];
$full_name = $orderData['fname'] . ' ' . $orderData['lname'];
$address = $orderData['address'] . ', ' . $orderData['subdistrict'] . ', ' . $orderData['district'] . ', ' . $orderData['province'] . ' ' . $orderData['zipcode'];
$tel = $orderData['phone'];
$receive_date = $orderData['receive_date'];
$payment_method = 'bank_transfer';
$order_status = 'waiting_payment';
$created_at = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO orders (user_email, full_name, order_no, address, tel, receive_date, total_qty, total_price, vat, grand_total, payment_method, order_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssiddddss", $email, $full_name, $order_no, $address, $tel, $receive_date, $total_qty, $subtotal, $vat, $total, $payment_method, $order_status, $created_at);
$stmt->execute();
$order_id = $conn->insert_id;

// บันทึกลง order_detail
$stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, p_id, product_code, product_name, o_qty, product_price, total) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt_detail) {
  die("Prepare failed: " . $conn->error);
}
foreach ($cart as $item) {
  $pid = $item['p_id'];
  $code = $item['code'];
  $name = $item['p_name'];
  $qty = $item['qty'];
  $price = $item['price'];
  $line_total = $qty * $price;
  $stmt_detail->bind_param("iissidd", $order_id, $pid, $code, $name, $qty, $price, $line_total);
  $stmt_detail->execute();
}

// Clear cart session
unset($_SESSION['cart']);
unset($_SESSION['buy_now']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ชำระเงิน</title>
  <link rel="stylesheet" href="styles\payment.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
  <h1>📌 ช่องทางการชำระเงิน</h1>

  <div class="section">
    <h2>ข้อมูลผู้สั่งซื้อ</h2>
    <p>ชื่อ-นามสกุล: <?= htmlspecialchars($full_name) ?></p>
    <p>ที่อยู่: <?= htmlspecialchars($address) ?></p>
    <p>เบอร์โทร: <?= htmlspecialchars($tel) ?></p>
    <p>อีเมล: <?= htmlspecialchars($email) ?></p>
    <p>วันที่รับสินค้า: <?= htmlspecialchars($receive_date) ?></p>
  </div>

  <div class="section">
    <h2>รายการสินค้า</h2>
    <table>
      <thead>
        <tr>
          <th>รหัสสินค้า</th>
          <th>ชื่อสินค้า</th>
          <th>จำนวน</th>
          <th>ราคาต่อชิ้น</th>
          <th>รวม</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['code']) ?></td>
            <td><?= htmlspecialchars($item['p_name']) ?></td>
            <td><?= $item['qty'] ?></td>
            <td><?= number_format($item['price'], 2) ?> บาท</td>
            <td><?= number_format($item['price'] * $item['qty'], 2) ?> บาท</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="text-right">รวมก่อน VAT: <?= number_format($subtotal, 2) ?> บาท</p>
    <p class="text-right">VAT (7%): <?= number_format($vat, 2) ?> บาท</p>
    <p class="text-right"><strong>รวมสุทธิ: <?= number_format($total, 2) ?> บาท</strong></p>
  </div>

    <div class="section">
      <h2>เลือกช่องทางการชำระเงิน</h2>
      <div class="payment-methods">
        <label><strong>โอนเงินผ่านบัญชีธนาคาร</strong></label>

        <div style="display: flex; align-items: center; gap: 15px; background-color: #fefefe; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
          <img src="image/bbl.jpg" alt="ธนาคารกรุงเทพ" style="width: 100px; height: auto;">
          <div>
            <p><strong>ธนาคารกรุงเทพ</strong></p>
            <p>เลขที่บัญชี: <strong>904-7110-13-6</strong></p>
            <p>ชื่อบัญชี: บัณฑิตา คงโนนกอก</p>
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 15px; background-color: #fefefe; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
          <img src="image/kbank.jfif" alt="ธนาคารกสิกรไทย" style="width: 100px; height: auto;">
          <div>
            <p><strong>ธนาคารกสิกรไทย</strong></p>
            <p>เลขที่บัญชี: <strong>072-2882-14-8</strong></p>
            <p>ชื่อบัญชี: บัณฑิตา คงโนนกอก</p>
          </div>
        </div>
      </div>
    </div>
    <form action="confirm_payment.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="email" value="<?= htmlspecialchars($orderData['email']) ?>">
      <input type="hidden" name="total" value="<?= $total ?>">

      <div class="section">
        <h2>แนบหลักฐานการชำระเงิน</h2>
        <p>กรุณาอัปโหลดรูปภาพสลิปการโอนเงิน (ไฟล์ .jpg, .png ขนาดไม่เกิน 5MB)</p>
        <input type="file" name="payment_slip" id="payment_slip" accept="image/png, image/jpeg" required style="margin-top:10px;">
        <div id="preview" style="margin-top:15px;">
        </div>
      </div>
      <div class="note">**กรุณาชำระเงินก่อนกดยืนยัน**</div>

      <div class="section">
        <button class="btn" type="submit">ยืนยันการชำระเงิน</button>
      </div>
    </form>
  </div>
  <script>
    const input = document.getElementById('payment_slip');
    const preview = document.getElementById('preview');

    input.addEventListener('change', function() {
      preview.innerHTML = ''; // เคลียร์ก่อนทุกครั้ง

      const file = this.files[0];
      if (file) {
        if (file.size > 5 * 1024 * 1024) { // เช็กขนาด > 5MB
          alert('ไฟล์มีขนาดใหญ่เกิน 5MB');
          this.value = ''; // ล้างไฟล์ออก
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.maxWidth = '300px';
          img.style.maxHeight = '300px';
          img.style.border = '1px solid #ccc';
          img.style.padding = '5px';
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
