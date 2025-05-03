<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
  session_start();
  unset($_SESSION['cart']);
  unset($_SESSION['buy_now']);
  header("Location: product.php");
  exit;
}

session_start();
include('dbconnect.php');
include('./components/navbar.php');

$showLoginAlert = false;

if (!isset($_SESSION['email'])) {
  $showLoginAlert = true;
}

// Check if Cart Checkout
$cartProducts = [];
$total = 0;
$total_qty = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_p_ids'])) {
  foreach ($_POST['cart_p_ids'] as $p_id) {
    $p_id = intval($p_id);
    $qty = intval($_POST['cart_qtys'][$p_id] ?? 1);
    if ($qty <= 0) $qty = 1;

    $sql = "SELECT * FROM product WHERE p_id = $p_id";
    $result = $conn->query($sql);
    if ($result && $product = $result->fetch_assoc()) {
      $cartProducts[] = [
        'p_id' => $product['p_id'],
        'p_name' => $product['p_name'],
        'price' => $product['price'],
        'qty' => $qty,
        'image' => $product['image'] ?? 'default.jpg',
        'code' => $product['code'] ?? '',
      ];
      $total += $product['price'] * $qty;
      $total_qty += $qty;
    }
  }
} elseif (isset($_SESSION['buy_now'])) {
  $buyNowItem = $_SESSION['buy_now'];

  $p_id = intval($buyNowItem['p_id']);
  $sql = "SELECT * FROM product WHERE p_id = $p_id";
  $result = $conn->query($sql);
  $product = $result->fetch_assoc();

  if ($product) {
    $cartProducts[] = [
      'p_id' => $product['p_id'],
      'p_name' => $product['p_name'],
      'price' => $product['price'],
      'qty' => 1,
      'image' => $product['image'] ?? 'default.jpg',
      'code' => $product['code'],
    ];
    $total += $product['price'];
    $total_qty += 1;
  }
} else {
  header('Location: product.php');
  exit;
}

// Fetch user info
$user = null;
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultUser = $stmt->get_result();
  $user = $resultUser->fetch_assoc();
}

// Minimum date
$today = new DateTime();
$today->modify('+3 days');
$minDate = $today->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ตรวจสอบรายการสินค้า</title>
  <link rel="stylesheet" href="./styles/checkout.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<form id="checkout-form" action="payment.php" method="post">
  <div class="main-container">
    <div class="form-section">
      <h2>ข้อมูลในการจัดส่ง</h2>
      <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" readonly required>
      <input type="tel" name="phone" value="<?= $user['tel'] ?? '' ?>" readonly required>
      <div class="form-group">
        <input type="text" name="fname" value="<?= $user['first_name'] ?? '' ?>" readonly required>
        <input type="text" name="lname" value="<?= $user['last_name'] ?? '' ?>" readonly required>
      </div>
      <input type="text" name="address" value="<?= $user['address'] ?? '' ?>" readonly required>
      <div class="form-group">
        <input type="text" name="subdistrict" value="<?= $user['sub_district'] ?? '' ?>" readonly required>
        <input type="text" name="district" value="<?= $user['district'] ?? '' ?>" readonly required>
      </div>
      <div class="form-group">
        <input type="text" name="province" value="<?= $user['province'] ?? '' ?>" readonly required>
        <input type="text" name="zipcode" value="<?= $user['post_code'] ?? '' ?>" readonly required>
      </div>
      <input type="date" name="receive_date" id="receive_date" required min="<?= $minDate ?>" value="<?= $minDate ?>">
      <div class="note">**กรุณาเลือกวันที่รับสินค้าและตรวจสอบที่อยู่ให้ถูกต้องก่อนยืนยันการสั่งซื้อ**</div>
      <div class="note-1">(สินค้าจะได้รับหลังการสั่งซื้ออย่างน้อย 3 วัน)</div>
    </div>

    <div class="cart-section">
      <h2>รายการสินค้า</h2>
      <table class="cart-table">
        <thead>
        <tr>
          <th>รหัสสินค้า</th>
          <th>ชื่อสินค้า</th>
          <th>ภาพ</th>
          <th class="text-right">ราคาต่อชิ้น</th>
          <th class="text-right">จำนวน</th>
          <th class="text-right">ราคารวม</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cartProducts as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['code']) ?></td>
            <td><?= htmlspecialchars($item['p_name']) ?></td>
            <td><img src="image/<?= htmlspecialchars($item['image']) ?>" alt=""></td>
            <td class="text-right"><?= number_format($item['price'], 2) ?> บาท</td>
            <td class="text-right"><?= $item['qty'] ?></td>
            <td class="text-right"><?= number_format($item['price'] * $item['qty'], 2) ?> บาท</td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <div class="cart-summary">
        <p>จำนวนสินค้า: <strong><?= $total_qty ?></strong> รายการ</p>
        <p>ราคาทั้งหมด: <strong><?= number_format($total, 2) ?> บาท</strong></p>
      </div>

      <div style="text-align: right;">
        <button type="submit" name="cancel_order" formmethod="post" formaction="checkout.php" class="btn btn-cancel">
          ยกเลิกการสั่งซื้อ
        </button>
        <button type="button" id="confirm-order" class="btn btn-confirm">ดำเนินการชำระเงิน</button>
      </div>
    </div>
  </div>
</form>

<?php if ($showLoginAlert): ?>
<script>
  Swal.fire({
    icon: 'warning',
    title: 'คุณยังไม่ได้ login',
    text: 'กรุณาเข้าสู่ระบบเพื่อทำการสั่งซื้อ',
    showCancelButton: true,
    confirmButtonColor: '#f48fb1',
    cancelButtonColor: 'rgb(177, 175, 174)',
    confirmButtonText: 'เข้าสู่ระบบ',
    cancelButtonText: 'สมัครสมาชิก',
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'login.php';
    } else {
      window.location.href = 'register.php';
    }
  });
</script>
<?php endif; ?>

<script>
document.getElementById("confirm-order").addEventListener("click", function () {
  Swal.fire({
    title: 'ยืนยันการสั่งซื้อ?',
    text: 'โปรดตรวจสอบข้อมูลให้ถูกต้องก่อนดำเนินการ',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f48fb1',
    cancelButtonColor: '#ccc',
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById("checkout-form").submit();
    }
  });
});
</script>

</body>
</html>
