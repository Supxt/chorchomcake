<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ยินดีต้อนรับสู่ร้านเค้ก</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/home.css">
</head>

<body>

  <header>
    <h1>CHORCHOMCAKE</h1>
    <p>เค้กทำมือสุดน่ารัก ส่งความสุขทุกคำ</p>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>สั่งเค้กสุดคิวท์ได้ที่นี่!</h2>
      <p>เค้กน่ารัก เค้กวันเกิด ของขวัญพิเศษ</p>
      <a href="product.php">ดูสินค้าทั้งหมด</a>
    </div>
  </section>

  <section class="features">
    <div class="feature">
      <img src="image/birthday-cake.png" alt="เค้กหลากหลาย">
      <h3>เค้กหลายแบบ</h3>
      <p>มีให้เลือกทั้งคัพเค้ก เค้กปอนด์ เค้กธีมต่าง ๆ</p>
    </div>
    <div class="feature">
      <img src="image/trolley.png" alt="วิธีการสั่งซื้อ">
      <h3><a href="how-to-order.php" style="color: #8d5544; text-decoration: none;">วิธีการสั่งซื้อ</a></h3>
      <p>สั่งซื้อง่าย สะดวก รวดเร็ว</p>
    </div>
    <div class="feature">
      <img src="image/party.png" alt="ของขวัญพิเศษ">
      <h3>ของขวัญสุดประทับใจ</h3>
      <p>เค้กสั่งทำเฉพาะโอกาสพิเศษ </p>
    </div>

  </section>

  <footer>
    &copy; <?= date('Y') ?> CHORCHOMCAKE | chombunkhox@gmail.com
  </footer>

</body>

</html>