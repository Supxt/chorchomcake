<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

// -----------------------------
// Handle Add to Cart (Form Submission)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['p_id'])) {
  $p_id = $_POST['p_id'];
  $p_name = $_POST['p_name'];
  $price = $_POST['price'];
  $qty = $_POST['qty'] ?? 1;

  $sql = "SELECT * FROM product WHERE p_id = $p_id";
  $result = $conn->query($sql);
  $product = $result->fetch_assoc();

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$p_id])) {
    $_SESSION['cart'][$p_id]['qty'] += $qty;
  } else {
    $_SESSION['cart'][$p_id] = [
      'p_id' => $p_id,
      'p_name' => $p_name,
      'price' => $price,
      'qty' => $qty,
      'image' => $product['image'] ?? 'default.jpg',
      'code' => $product['code'] ?? '',
    ];
  }

  // Redirect back to same product detail page with a success flag
  header("Location: product_detail.php?p_id=$p_id&added=1");
  exit;
}

// -----------------------------
// Load Product Details
// -----------------------------
$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : 0;
$sql = "SELECT * FROM product p LEFT JOIN category c ON p.category_id = c.category_id WHERE p.p_id = $p_id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title><?php echo $product['p_name']; ?></title>
  <link rel="stylesheet" href="./styles/product_detail.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <a href="product.php" class="back-btn">← ดูรายการสินค้าอื่นๆ</a>
  <div class="product-container">
    <div class="product-image">
      <img src="image/<?php echo $product['image']; ?>" alt="<?php echo $product['p_name']; ?>">
    </div>
    <div class="product-details">
      <h2><?php echo $product['p_name']; ?></h2>
      <div class="code">รหัสสินค้า : <?php echo $product['code']; ?></div>
      <div class="price"><?php echo number_format($product['price'], 0); ?> บาท</div>
      <div class="stock">เหลือ <?php echo $product['quantity']; ?> ชิ้น</div>

      <form method="POST" onsubmit="return updateTotal()">
        <div class="qty-control" style="width: 100%; max-width: 350px;">
          <button type="button" onclick="changeQty(-1)">-</button>
          <input type="number" name="qty" id="qty" value="1" min="1" max="<?php echo $product['quantity']; ?>">
          <button type="button" onclick="changeQty(1)">+</button>
        </div>

        <input type="hidden" name="p_id" value="<?php echo $product['p_id']; ?>">
        <input type="hidden" name="p_name" value="<?php echo $product['p_name']; ?>">
        <input type="hidden" name="price" id="price" value="<?php echo $product['price']; ?>">

        <button type="submit" class="add-cart-btn">🛒 เพิ่มไปยังรถเข็น</button>
      </form>

      <div class="product-info">
        <p><strong>ประเภทสินค้า:</strong> <?php echo $product['category_name']; ?></p>
        <p><strong>รายละเอียดสินค้า:</strong> <?php echo $product['description']; ?></p>
      </div>

      <div class="total-price" id="total-price">
        รวม <?php echo number_format($product['price'], 0); ?> บาท
      </div>
    </div>
  </div>

  <script>
    function changeQty(amount) {
      const qtyInput = document.getElementById('qty');
      let qty = parseInt(qtyInput.value);
      const maxQty = <?php echo $product['quantity']; ?>;

      qty += amount;
      if (qty < 1) qty = 1;
      if (qty > maxQty) qty = maxQty;

      qtyInput.value = qty;
      updateTotal();
    }

    function updateTotal() {
      const qty = parseInt(document.getElementById('qty').value);
      const price = parseFloat(document.getElementById('price').value);
      const total = qty * price;

      document.getElementById('total-price').innerText = "รวม " + total.toLocaleString() + " บาท";
      return true;
    }

    <?php if (isset($_GET['added']) && $_GET['added'] == '1'): ?>
      Swal.fire({
        icon: 'success',
        title: 'เพิ่มสินค้าสำเร็จ',
        text: 'สินค้าถูกเพิ่มในตะกร้าเรียบร้อยแล้ว!',
        confirmButtonText: 'ตกลง'
      });
    <?php endif; ?>
  </script>

</body>

</html>