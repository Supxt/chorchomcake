<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

$orderData = $_POST;
$cart = [];
$total = 0;
$total_qty = 0;

if (isset($_SESSION['buy_now'])) {
  $cart = [$_SESSION['buy_now']];
} else {
  $cart = $_SESSION['cart'] ?? [];
}

foreach ($cart as $item) {
  $total += $item['price'] * $item['qty'];
  $total_qty += $item['qty'];
}

$vat = ($total * 7) / 107;
$subtotal = $total - $vat;

function generateOrderNo() {
  $datePart = date('dmY');
  $randomPart = str_pad(rand(1, 100), 3, '0', STR_PAD_LEFT);
  return 'ORD' . $datePart . $randomPart;
}

$order_no = generateOrderNo();
$email = $orderData['email'];
$full_name = $orderData['fname'] . ' ' . $orderData['lname'];
$address = $orderData['address'] . ', ' . $orderData['subdistrict'] . ', ' . $orderData['district'] . ', ' . $orderData['province'] . ' ' . $orderData['zipcode'];
$tel = $orderData['phone'];
$receive_date = $orderData['receive_date'];
$payment_method = 'bank_transfer';
$order_status = 'รอการชำระเงิน';
$created_at = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO orders (user_email, full_name, order_no, address, tel, receive_date, total_qty, total_price, vat, grand_total, payment_method, order_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssiddddss", $email, $full_name, $order_no, $address, $tel, $receive_date, $total_qty, $subtotal, $vat, $total, $payment_method, $order_status, $created_at);
$stmt->execute();
$order_id = $conn->insert_id;

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_slip'])) {
  echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

  $fileName = time() . '_' . basename($_FILES["payment_slip"]["name"]);
  $targetDir = "uploads/";
  $targetFile = $targetDir . $fileName;

  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

  if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
    echo "<script>Swal.fire('ผิดพลาด', 'อนุญาตเฉพาะ JPG, JPEG, PNG เท่านั้น', 'error');</script>";
    $uploadOk = 0;
  }

  if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $targetFile)) {
      $sql = "SELECT * FROM orders WHERE user_email = ? ORDER BY order_id DESC LIMIT 1";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $result = $stmt->get_result();
      $order = $result->fetch_assoc();

      if ($order) {
        $updateSql = "UPDATE orders SET payment_slip = ?, payment_method = 'โอนเงิน', order_status = 'รอตรวจสอบการชำระเงิน' WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('si', $fileName, $order['order_id']);
        $updateStmt->execute();

        // ตัด stock สินค้าตาม order_details
        $sqlDetails = "SELECT p_id, o_qty FROM order_details WHERE order_id = ?";
        $detailStmt = $conn->prepare($sqlDetails);
        $detailStmt->bind_param("i", $order['order_id']);
        $detailStmt->execute();
        $detailsResult = $detailStmt->get_result();

        while ($item = $detailsResult->fetch_assoc()) {
          $updateStock = $conn->prepare("UPDATE product SET quantity = GREATEST(quantity - ?, 0) WHERE p_id = ?");
          $updateStock->bind_param("ii", $item['o_qty'], $item['p_id']);
          $updateStock->execute();
        }


        unset($_SESSION['cart']);
        unset($_SESSION['buy_now']);

        echo "<script>
          Swal.fire({
            icon: 'success',
            title: 'อัปโหลดสำเร็จ',
            text: 'ระบบจะพาไปยังหน้าตรวจสอบคำสั่งซื้อ',
            confirmButtonText: 'ตกลง',
            timer: 2000,
            timerProgressBar: true,
          }).then(() => {
            window.location.href = 'check_status.php';
          });
        </script>";

      } else {
        echo "<script>Swal.fire('ไม่พบคำสั่งซื้อ', 'กรุณาตรวจสอบอีเมลอีกครั้ง', 'error');</script>";
      }
    } else {
      echo "<script>Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');</script>";
    }
  }
}
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

    <form id="payment-form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="fname" value="<?= htmlspecialchars($orderData['fname']) ?>">
      <input type="hidden" name="lname" value="<?= htmlspecialchars($orderData['lname']) ?>">
      <input type="hidden" name="address" value="<?= htmlspecialchars($orderData['address']) ?>">
      <input type="hidden" name="subdistrict" value="<?= htmlspecialchars($orderData['subdistrict']) ?>">
      <input type="hidden" name="district" value="<?= htmlspecialchars($orderData['district']) ?>">
      <input type="hidden" name="province" value="<?= htmlspecialchars($orderData['province']) ?>">
      <input type="hidden" name="zipcode" value="<?= htmlspecialchars($orderData['zipcode']) ?>">
      <input type="hidden" name="phone" value="<?= htmlspecialchars($orderData['phone']) ?>">
      <input type="hidden" name="receive_date" value="<?= htmlspecialchars($orderData['receive_date']) ?>">
      <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">

      <div class="section">
        <h2>แนบหลักฐานการชำระเงิน</h2>
        <input type="file" name="payment_slip" id="payment_slip" accept="image/jpeg,image/png" required>
        <div id="preview" style="margin-top:10px;"></div>
      </div>
      <div class="section">
        <button type="button" id="confirm-payment-btn" class="btn">ยืนยันการชำระเงิน</button>
      </div>
    </form>
  </div>

  <script>
    const input = document.getElementById('payment_slip');
    const preview = document.getElementById('preview');
    const confirmBtn = document.getElementById('confirm-payment-btn');
    const form = document.getElementById('payment-form');

    input.addEventListener('change', () => {
      preview.innerHTML = '';
      const file = input.files[0];
      if (file && file.size <= 5 * 1024 * 1024) {
        const reader = new FileReader();
        reader.onload = e => {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.maxWidth = '300px';
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      } else {
        Swal.fire('ไฟล์ใหญ่เกินไป', 'กรุณาอัปโหลดไม่เกิน 5MB', 'error');
        input.value = '';
      }
    });

    confirmBtn.addEventListener('click', () => {
      if (!input.files.length) {
        Swal.fire('กรุณาแนบสลิปก่อน', '', 'warning');
        return;
      }

      Swal.fire({
        title: 'ยืนยันการชำระเงิน?',
        text: 'ตรวจสอบว่าสลิปถูกต้องแล้ว',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#f48fb1',
        cancelButtonColor: '#ccc'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  </script>
</body>
</html>
