<?php include('../dbconnect.php'); $order_id=intval($_GET['order_id']); 
// ดึงข้อมูลคำสั่งซื้อ
  $sql_order="SELECT * FROM orders WHERE order_id = $order_id" ; $result_order=$conn->query($sql_order);



  $order = $result_order->fetch_assoc();

  $redirectPage = 'pending_orders.php'; // default fallback

switch ($order['order_status']) {
  case 'รอการชำระเงิน':
    $redirectPage = 'paying_orders.php';
    break;
  case 'รอตรวจสอบการชำระเงิน':
    $redirectPage = 'waiting_orders.php';
    break;
  case 'กำลังดำเนินการ':
    $redirectPage = 'pending_orders.php';
    break;
  case 'กำลังจัดส่ง':
    $redirectPage = 'transporting_orders.php';
    break;
  case 'สำเร็จ':
    $redirectPage = 'done_orders.php';
    break;
    case 'ยกเลิก':
      $redirectPage = 'canceled_orders.php';
      break;
}
  include('admin.php');


// ดึงรายละเอียดสินค้าใน order นี้
$sql_items = "SELECT od.product_code, od.product_name, od.o_qty, od.product_price
              FROM order_details od
              WHERE od.order_id = $order_id";
$result_items = $conn->query($sql_items);

$order_items = [];
if ($result_items && $result_items->num_rows > 0) {
  while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
  }
}

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
    cursor: pointer;
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
    margin-right: 100px
  }

  .btn:hover {
    background-color: #e56b90;
  }

  .flex-head {
    display: flex;
    align-items: center;
  }

  #imgModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
  }

  #imgModal img {
    max-width: 90%;
    max-height: 90%;
    border: 5px solid white;
    border-radius: 8px;
    box-shadow: 0 0 15px black;
  }

  #imgModal:target {
    display: flex;
    cursor: pointer;
  }
  </style>
</head>

<body>

  <div id="imgModal" onclick="closeModal()">
    <img id="modalImg" src="" alt="Preview">
  </div>

  <script>
  function openModal(src) {
    document.getElementById("modalImg").src = src;
    document.getElementById("imgModal").style.display = "flex";
  }

  function closeModal() {
    document.getElementById("imgModal").style.display = "none";
  }
  </script>

  <div class="container">

    <div class="flex-head">
      <a class="btn" href="<?= $redirectPage ?>">🔙 กลับ</a>
      <h1>📄 รายละเอียดการสั่งซื้อ</h1>
    </div>


    <div class="detail">
      <p><strong>รหัสการสั่งซื้อ:</strong> <?= htmlspecialchars($order['order_no']) ?></p>
      <p><strong>วันที่สั่งซื้อ:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['created_at']))) ?>
      </p>
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


      <?php if (!empty($order_items)): ?>
<div class="section">
  <h2>📦 รายการสินค้า</h2>
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
      <?php
      $subtotal = 0;
      foreach ($order_items as $item):
        $line_total = $item['product_price'] * $item['o_qty'];
        $subtotal += $line_total;
      ?>
      <tr>
        <td><?= htmlspecialchars($item['product_code']) ?></td>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= $item['o_qty'] ?></td>
        <td><?= number_format($item['product_price'], 2) ?> บาท</td>
        <td><?= number_format($line_total, 2) ?> บาท</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="text-right">รวมก่อน VAT: <?= number_format($subtotal, 2) ?> บาท</p>
  <p class="text-right">VAT (7%): <?= number_format($order['vat'], 2) ?> บาท</p>
  <p class="text-right"><strong>รวมสุทธิ: <?= number_format($order['grand_total'], 2) ?> บาท</strong></p>
</div>
<?php endif; ?>

      <?php if (!empty($order['payment_slip'])): ?>
      <p><strong>หลักฐานการชำระเงิน:</strong></p>
      <img class="payment-slip" src="../uploads/<?= htmlspecialchars($order['payment_slip']) ?>"
        alt="หลักฐานการชำระเงิน" onclick="openModal(this.src)">
      <?php endif; ?>

    </div>
    <div>
      <?php if (!in_array($order['order_status'], ['สำเร็จ', 'ยกเลิก'])): ?>
      <form method="POST" action="update_order_status.php" style="margin-top: 20px; text-align:center;">
        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

        <?php if ($order['order_status'] === 'รอการชำระเงิน'): ?>
        <button class="btn" type="submit" name="new_status" value="ยกเลิก">❌ ยกเลิกคำสั่งซื้อ</button>

        <?php elseif ($order['order_status'] === 'รอตรวจสอบการชำระเงิน'): ?>
        <button class="btn" type="submit" name="new_status" value="กำลังดำเนินการ">➡️ กำลังดำเนินการ</button>
        <button class="btn" type="submit" name="new_status" value="ยกเลิก">❌ ยกเลิกคำสั่งซื้อ</button>

        <?php elseif ($order['order_status'] === 'กำลังดำเนินการ'): ?>
        <button class="btn" type="submit" name="new_status" value="กำลังจัดส่ง">📦 กำลังจัดส่ง</button>

        <?php elseif ($order['order_status'] === 'กำลังจัดส่ง'): ?>
        <button class="btn" type="submit" name="new_status" value="สำเร็จ">✅ สำเร็จ</button>
        <?php endif; ?>
      </form>
      <?php endif; ?>

    </div>


  </div>
</body>

</html>