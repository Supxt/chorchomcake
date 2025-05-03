<?php
session_start();
include('./components/navbar.php');
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนการสั่งซื้อ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/how-to-order.css">
</head>

<body>

  <header>
    <h1>ขั้นตอนการสั่งซื้อสินค้า</h1>
  </header>

  <div class="container">

    <div class="step">
      <img src="image/cake.png" alt="ขั้นตอนที่ 1">
      <div class="step-content">
        <h3>1. เลือกสินค้าที่คุณต้องการ</h3>
        <p>ไปที่ <a href="product.php" style="color: #8d5544;">หน้าสินค้า</a> และเลือกเค้กหรือของขวัญที่คุณสนใจ</p>
      </div>
    </div>

    <div class="step">
      <img src="image/purchase.png" alt="ขั้นตอนที่ 2">
      <div class="step-content">
        <h3>2. เพิ่มลงในตะกร้า</h3>
        <p>กดปุ่ม "เพิ่มในตะกร้า" เพื่อบันทึกสินค้าที่ต้องการซื้อ</p>
      </div>
    </div>

    <div class="step">
      <img src="image/complete.png" alt="ขั้นตอนที่ 3">
      <div class="step-content">
        <h3>3. ดำเนินการสั่งซื้อ</h3>
        <p>ไปที่หน้าตะกร้า และกรอกข้อมูลสำหรับจัดส่ง จากนั้นกดยืนยัน (สินค้าจะได้รับหลังการสั่งซื้ออย่างน้อย 3 วัน)</p>
      </div>
    </div>

    <div class="step">
      <img src="image/mobile.png" alt="ขั้นตอนที่ 4">
      <div class="step-content">
        <h3>4. โอนเงิน & แจ้งชำระ</h3>
        <p>โอนเงินผ่านบัญชีธนาคาร/พร้อมเพย์ แล้วแจ้งการชำระผ่านหน้าเว็บ </p>
      </div>
    </div>

    <div class="step">
      <img src="image/package.png" alt="ขั้นตอนที่ 5">
      <div class="step-content">
        <h3>5. รอรับสินค้าถึงหน้าบ้าน</h3>
        <p>ทีมงานจัดส่งสินค้าพร้อมแพ็คอย่างดี จัดส่งภายใน 3 วันหลังจากวันที่ชำระสินค้า 🚚</p>
      </div>
    </div>

  </div>

  <footer>
    &copy; <?= date('Y') ?> CHORCHOMCAKE | ขอบคุณที่สั่งซื้อกับเรา
  </footer>

</body>

</html>