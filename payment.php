<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

// ตรวจสอบว่าเป็น POST เท่านั้น
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: checkout.php");
  exit;
}

$orderData = $_POST;
$cart = [];
$total = 0;
$total_qty = 0;

// ตรวจสอบว่ามาจาก buy_now หรือ cart
if (isset($_SESSION['buy_now'])) {
  $cart = [$_SESSION['buy_now']];
} else {
  $cart = $_SESSION['cart'] ?? [];
}

// คำนวณราคารวม
foreach ($cart as $item) {
  $total += $item['price'] * $item['qty'];
  $total_qty += $item['qty'];
}

$vat = ($total * 7) / 107;
$subtotal = $total - $vat;

// สร้างหมายเลขคำสั่งซื้อ
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

// บันทึก order
$stmt = $conn->prepare("INSERT INTO orders (user_email, full_name, order_no, address, tel, receive_date, total_qty, total_price, vat, grand_total, payment_method, order_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssiddddss", $email, $full_name, $order_no, $address, $tel, $receive_date, $total_qty, $subtotal, $vat, $total, $payment_method, $order_status, $created_at);
$stmt->execute();
$order_id = $conn->insert_id;

// บันทึกสินค้าใน order_detail
$stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, p_id, product_code, product_name, o_qty, product_price, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
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

// ล้าง session
unset($_SESSION['cart']);
unset($_SESSION['buy_now']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ยืนยันการชำระเงิน</title>
  <link rel="stylesheet" href="styles/payment.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <h1>📌 ช่องทางการชำระเงิน</h1>

    <div class="section">
      <h2>ข้อมูลผู้สั่งซื้อ</h2>
      <p>Order No: <?= htmlspecialchars($order_no) ?></p>
      <p>ชื่อ: <?= htmlspecialchars($full_name) ?></p>
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
            <th>รหัส</th><th>ชื่อ</th><th>จำนวน</th><th>ราคาชิ้น</th><th>รวม</th>
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

    <div class="section">
      <h2>แนบสลิปการชำระเงิน</h2>
      <input type="file" id="payment_slip" accept="image/png, image/jpeg" required>
      <div id="preview" style="margin-top: 10px;"></div>
    </div>

    <div class="section">
      <button id="confirm-payment-btn" class="btn">ยืนยันการชำระเงิน</button>
    </div>
  </div>

<script>
const input = document.getElementById('payment_slip');
const preview = document.getElementById('preview');
const confirmBtn = document.getElementById('confirm-payment-btn');

input.addEventListener('change', function () {
  preview.innerHTML = '';
  const file = this.files[0];

  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: 'error',
        title: 'ขนาดไฟล์เกิน 5MB',
        confirmButtonColor: '#f48fb1'
      });
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.style.maxWidth = '300px';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  }
});

confirmBtn.addEventListener('click', function () {
  if (!input.files.length) {
    Swal.fire({
      icon: 'warning',
      title: 'กรุณาแนบสลิปก่อน',
      confirmButtonColor: '#f48fb1'
    });
    return;
  }

  Swal.fire({
    title: 'ยืนยันการชำระเงิน?',
    text: 'โปรดตรวจสอบว่าสลิปถูกต้องแล้ว',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f48fb1',
    cancelButtonColor: '#ccc',
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
  }).then(result => {
    if (result.isConfirmed) {
      Swal.fire({
        icon: 'success',
        title: 'ส่งข้อมูลสำเร็จ',
        text: 'ระบบกำลังนำคุณไปยังหน้าตรวจสอบคำสั่งซื้อ...',
        showConfirmButton: false,
        timer: 2000
      }).then(() => {
        window.location.href = 'check_status.php';
      });
    }
  });
});
</script>
</body>
</html>
