<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

$showLoginAlert = false;

if (!isset($_SESSION['email'])) {
  $showLoginAlert = true;
}

// Check if Cart Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_p_ids'])) {
  $cartProducts = [];
  $total = 0;
  $total_qty = 0;

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
} else if (isset($_SESSION['buy_now'])) {
  // Normal Buy Now flow
  $buyNowItem = $_SESSION['buy_now'];

  $p_id = intval($buyNowItem['p_id']);
  $sql = "SELECT * FROM product WHERE p_id = $p_id";
  $result = $conn->query($sql);
  $product = $result->fetch_assoc();

  $cartProducts = [];
  $total = 0;
  $total_qty = 0;

  if ($product) {
    $cartProducts[] = [
      'p_id' => $product['p_id'],
      'p_name' => $product['p_name'],
      'price' => $product['price'],
      'qty' => 1,
      'image' => $product['image'] ?? 'default.jpg',
      'code' => $product['code'],
    ];
    $total += $product['price'] * 1;
    $total_qty += 1;
  }

  echo "<script>console.log('Item: " . json_encode($cartProducts) . "');</script>";

} else {
  header('Location: product.php');
  exit;
}

// Fetch user info if logged in
$user = null;
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultUser = $stmt->get_result();
  $user = $resultUser->fetch_assoc();
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ตรวจสอบรายการสินค้า</title>
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

    .main-container {
      display: flex;
      max-width: 1100px;
      margin: 10px auto;
      padding: 20px;
      background-color: #fff0e5;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }


    .form-section,
    .cart-section {
      flex: 1;
      padding: 20px;
    }

    .form-section h2,
    .cart-section h2 {
      margin-bottom: 16px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .form-group {
      display: flex;
      gap: 10px;
    }

    .form-group>div {
      flex: 1;
    }

    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }

    .cart-table th,
    .cart-table td {
      font-size: 16px;
      padding: 20px;
      border-bottom: 1px solid #ddd;
    }

    .cart-table th.text-right,
    .cart-table td.text-right {
      text-align: right;
    }

    .cart-table th {
      background-color: #f7b7a3;
      font-size: 17px;
      font-weight: bold;
    }

    .cart-table img {
      width: 60px;
      border-radius: 6px;
    }

    .cart-summary {
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px;
      font-size: 18px;
      text-align: right;
    }

    .cart-summary strong {

      font-weight: bold;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
    }

    .btn-cancel {
      background-color: #ccc;
      color: #333;
    }

    .btn-confirm {
      background-color: #f48fb1;
      color: white;
      margin-left: 10px;
      margin-top: 50px;
    }

    .btn-confirm :hover {
      background-color: rgb(114, 52, 52);
    }


    .note {
      color: red;
      font-size: 12px;
      margin-top: 8px;
    }

    .note-1 {
      color: black;
      font-size: 12px;
      margin-top: 8px;
    }
  </style>
</head>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const receiveDateInput = document.getElementById("receive_date");
    if (receiveDateInput) {
      const today = new Date();
      today.setDate(today.getDate() + 3);

      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      const minDate = `${yyyy}-${mm}-${dd}`;

      receiveDateInput.min = minDate;
      receiveDateInput.value = minDate;
    }
  });
</script>

<body>

  <form action="payment.php" method="post">
    <div class="main-container">
      <div class="form-section">
        <h2>ข้อมูลในการจัดส่ง</h2><input type="email" name="email" placeholder="อีเมล" value="<?= $user['email'] ?? '' ?>" readonly required><input type="tel" name="phone" placeholder="เบอร์ติดต่อ" value="<?= $user['tel'] ?? '' ?>" readonly required>
        <div class="form-group"><input type="text" name="fname" placeholder="ชื่อ" value="<?= $user['first_name'] ?? '' ?>" readonly required><input type="text" name="lname" placeholder="นามสกุล" value="<?= $user['last_name'] ?? '' ?>" readonly required></div><input type="text" name="address" placeholder="ที่อยู่" value="<?= $user['address'] ?? '' ?>" readonly required>
        <div class="form-group"><input type="text" name="subdistrict" placeholder="แขวง/ตำบล" value="<?= $user['sub_district'] ?? '' ?>" readonly required><input type="text" name="district" placeholder="เขต/อำเภอ" value="<?= $user['district'] ?? '' ?>" readonly required></div>
        <div class="form-group"><input type="text" name="province" placeholder="จังหวัด" value="<?= $user['province'] ?? '' ?>" readonly required><input type="text" name="zipcode" placeholder="รหัสไปรษณีย์" value="<?= $user['post_code'] ?? '' ?>" readonly required></div>
        <input type="date" name="receive_date" id="receive_date" required min="<?= $minDate ?>">
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
              <th class="text-right">ราคาต่อชิ้น (บาท)</th>
              <th class="text-right">จำนวน</th>
              <th class="text-right">ราคารวม (บาท)</th>
            </tr>
          </thead>
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
        </table>
        <div class="cart-summary">
          <p> จำนวนสินค้า: <strong><?php echo $total_qty; ?></strong> รายการ</p>
          <p> ราคาทั้งหมด: <strong><?php echo number_format($total, 2); ?></strong>
          </p>
        </div>
        <div style="text-align: right;"><button type="reset" class="btn btn-cancel">ยกเลิกการสั่งซื้อ</button><button type="submit" class="btn btn-confirm">ดำเนินการชำระเงิน</button></div>
      </div>
    </div>
  </form>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const qtyInputs = document.querySelectorAll('.qty-input');

      qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
          const newQty = parseInt(this.value);
          const price = parseFloat(this.dataset.price);
          const itemTotalElement = this.closest('.cart-item').querySelector('.item-total');
          const productId = this.closest('.cart-item').querySelector('.item-remove a').href.split('=')[1];

          // คำนวณราคารวมของสินค้ารายการนั้น
          if (newQty > 0) {
            const itemTotal = price * newQty;
            itemTotalElement.textContent = itemTotal.toLocaleString('th-TH', {
              minimumFractionDigits: 2
            }) + ' บาท';

            // อัปเดตค่า input hidden ที่จะถูกส่งไป checkout
            const hiddenInput = document.getElementById(`qty_${productId}`);
            if (hiddenInput) {
              hiddenInput.value = newQty;
            }

            // อัปเดตราคารวมทั้งหมด
            updateCartTotal();
          }
        });
      });

      function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
          const input = item.querySelector('.qty-input');
          const qty = parseInt(input.value);
          const price = parseFloat(input.dataset.price);
          total += qty * price;
        });

        const totalElement = document.querySelector('.cart-footer span');
        totalElement.textContent = total.toLocaleString('th-TH', {
          minimumFractionDigits: 2
        }) + ' บาท';
      }
    });
  </script>
</body>

</html>


<?php if ($showLoginAlert): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'warning',
        title: 'คุณยังไม่ได้ login',
        text: 'กรุณาเข้าสู่ระบบเพื่อทำการสั่งซื้อ',
        showCancelButton: true,
        confirmButtonColor: ' #f48fb1',
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
    });
  </script>
<?php endif; ?>