<?php
session_start();
include('dbconnect.php');

// -----------------------------
// Handle Add-to-Cart (AJAX or Form)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $p_id = $_POST['p_id'];
  $p_name = $_POST['p_name'];
  $price = $_POST['price'];
  $qty = $_POST['qty'] ?? 1;
  $image = $_POST['image'] ?? 'default.jpg';
  $is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

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

  if ($is_ajax) {
    header('Content-Type: application/json');
    $cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
    exit;
  } else {
    header("Location: product.php");
    exit;
  }
}

// -----------------------------
// Product Page Load
// -----------------------------

$category = $_GET['category'] ?? 'all';
$limit = 6;
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

if ($category === 'all') {
  $sql_product = "SELECT * FROM product LIMIT $start, $limit";
  $total_sql = "SELECT COUNT(*) as total FROM product";
} else {
  $category_safe = $conn->real_escape_string($category);
  $sql_product = "SELECT * FROM product p
                  LEFT JOIN category c ON p.category_id = c.category_id
                  WHERE c.category_name = '$category_safe'
                  LIMIT $start, $limit";
  $total_sql = "SELECT COUNT(*) as total FROM product p
                LEFT JOIN category c ON p.category_id = c.category_id
                WHERE c.category_name = '$category_safe'";
}

$result_product = $conn->query($sql_product);
$total_result = $conn->query($total_sql);
$total_products = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

$sql_category = "SELECT * FROM category";
$result_category = $conn->query($sql_category);
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>สินค้า</title>
  <link rel="stylesheet" href="./styles/productcard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <!-- Category Filter -->
  <div class="categories">
    <div class="category-row">
      <a href="?category=all" class="category-item <?= $category === 'all' ? 'active' : '' ?>">สินค้าทั้งหมด</a>
      <?php while ($row = $result_category->fetch_assoc()): ?>
        <a href="?category=<?= urlencode($row['category_name']) ?>"
          class="category-item <?= $category === $row['category_name'] ? 'active' : '' ?>">
          <?= $row['category_name'] ?>
        </a>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Product Cards -->
  <div class="container">
    <?php while ($row = $result_product->fetch_assoc()): ?>
      <div class="product-card">
        <a href="product_detail.php?p_id=<?= $row['p_id'] ?>" style="text-decoration: none; color: inherit;">
          <img src="image/<?= $row['image'] ?>" alt="<?= $row['p_name'] ?>" class="image">
          <div class="product-info">
            <div class="p_name"><?= $row['code'] . ' ' . $row['p_name'] ?></div>
            <div class="stock">เหลือ <?= $row['quantity'] ?> ชิ้น</div>
            <div class="price"><?= number_format($row['price'], 2) ?> บาท</div>
            <div class="product-buttons">
              <button class="btn-cart"
                onclick="addToCart(event, <?= $row['p_id'] ?>, '<?= addslashes($row['p_name']) ?>', <?= $row['price'] ?>)">
                เพิ่มในตะกร้า
              </button>

              <form action="checkout.php" method="POST" class="form-button">
                <input type="hidden" name="p_id" value="<?= $row['p_id'] ?>">
                <input type="hidden" name="p_name" value="<?= htmlspecialchars($row['p_name']) ?>">
                <input type="hidden" name="price" value="<?= $row['price'] ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn-buy">ซื้อเลย</button>
              </form>
            </div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Pagination -->
  <div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?= $i ?>&category=<?= urlencode($category) ?>" class="<?= $page == $i ? 'active' : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </div>

  <!-- JavaScript -->
  <script>
    function addToCart(event, productId, productName, price) {
      event.preventDefault();

      fetch('product.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `p_id=${productId}&p_name=${encodeURIComponent(productName)}&price=${price}&ajax=1`
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'เพิ่มสินค้าในตะกร้า',
              text: `${productName} ถูกเพิ่มในตะกร้าแล้ว!`,
              confirmButtonText: 'ตกลง'
            });

            const cartCount = document.getElementById("cart-count");
            if (cartCount) {
              cartCount.innerText = data.cart_count;
            }
            console.log("DEBUG RESPONSE:", data);

          } else {
            Swal.fire({
              icon: 'error',
              title: 'เกิดข้อผิดพลาด',
              text: 'ไม่สามารถเพิ่มสินค้าได้',
              confirmButtonText: 'ตกลง'
            });
          }
        })
        .catch(() => {
          Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
            confirmButtonText: 'ตกลง'
          });
        });
    }
  </script>

</body>

</html>