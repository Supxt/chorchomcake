<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: checkout.php");
  exit;
}

$orderData = $_POST;

// สรุปรายการสินค้า
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (isset($_SESSION['buy_now'])) {
  $cart = [$_SESSION['buy_now']];
}

$total = 0;
$total_qty = 0;
foreach ($cart as $item) {
  $total += $item['price'] * $item['qty'];
  $total_qty += $item['qty'];
}
$vat = ($total * 7) / 107;
$subtotal = $total - $vat;
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ชำระเงิน</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      line-height: 1.6;
      background-color: #fefefe;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff7f0;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      font-size: 16px;
    }

    h2 {
      color: #d96c6c;
    }

    .section {
      margin-bottom: 30px;
    }

    .section th.id-col,
    .section td.id-col,
    .section th.name-col,
    .section td.name-col {
      text-align: left;
    }

    .section th.price-col,
    .section td.price-col {
      text-align: right;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
    }

    th,
    td {
      padding: 10px;
      text-align: center;
    }

    .text-right {
      padding: 2px;
      text-align: right;
      color: rgb(179, 102, 102);
      font-size: 16px;
    }

    .text-right-1 {
      text-align: right;
    }

    .payment-methods {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .btn {
      padding: 10px 20px;
      background-color: #f48fb1;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      float: right;
    }

    .note {
      color: red;
      font-size: 12px;
      margin-top: 8px;
    }

    img {
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>

<body>
  <div class="container">
    <h1 style="text-align: center; color:rgb(134, 64, 64); margin-bottom: 30px;">
      📌 ช่องทางการชำระเงิน
    </h1>
    <div class="section">
      <h2>ข้อมูลผู้สั่งซื้อ</h2>
      <p>ชื่อ-นามสกุล: <?= htmlspecialchars($orderData['fname'] . ' ' . $orderData['lname']) ?></p>
      <p>ที่อยู่: <?= htmlspecialchars($orderData['address']) ?>, <?= htmlspecialchars($orderData['subdistrict']) ?>, <?= htmlspecialchars($orderData['district']) ?>, <?= htmlspecialchars($orderData['province']) ?> <?= htmlspecialchars($orderData['zipcode']) ?></p>
      <p>เบอร์โทร: <?= htmlspecialchars($orderData['phone']) ?></p>
      <p>อีเมล: <?= htmlspecialchars($orderData['email']) ?></p>
      <p>วันที่ต้องการรับสินค้า: <?= htmlspecialchars($orderData['receive_date']) ?></p>
    </div>

    <div class="section">
      <h2>รายการสินค้า</h2>
      <table>
        <thead>
          <tr>
            <th class="id-col">รหัสสินค้า</th>
            <th class="name-col">ชื่อสินค้า</th>
            <th class="price-col">จำนวน (ชิ้น)</th>
            <th class="price-col">ราคาต่อชิ้น (บาท)</th>
            <th class="price-col">รวม (บาท)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $item): ?>
            <tr>
              <td class="id-col"><?= htmlspecialchars($item['code']) ?></td>
              <td class="name-col"><?= htmlspecialchars($item['p_name']) ?></td>
              <td class="price-col"><?= $item['qty'] ?></td>
              <td class="price-col">
                </tdclass><?= number_format($item['price'], 2) ?> </td>
              <td class="price-col">
                </tdclass><?= number_format($item['price'] * $item['qty'], 2) ?> </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p class="text-right">จำนวนทั้งหมด: <?= $total_qty ?> ชิ้น</p>
      <p class="text-right">ราคาสินค้า (ก่อน VAT): <?= number_format($subtotal, 2) ?> บาท</p>
      <p class="text-right">ภาษีมูลค่าเพิ่ม (7%): <?= number_format($vat, 2) ?> บาท</p>
      <p class="text-right-1" style="background-color: #ffebcc; padding: 10px; font-size: 20px; font-weight: bold; border-radius: 6px;">
        รวมราคาทั้งสิ้น: <?= number_format($total, 2) ?> บาท
      </p>

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