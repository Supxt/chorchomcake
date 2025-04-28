<!-- TODO please do this without using catch it always will throw to catch -->
<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');
// -----------------------------
// Handle Add-to-Cart (AJAX or Form)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $p_id = $_POST['product_id'];
  $p_name = $_POST['product_name'];
  $price = $_POST['price'];
  $qty = $_POST['qty'] ?? 1;
  $is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';


  $sql = "SELECT * FROM product WHERE p_id = $p_id";
  $result = $conn->query($sql);

  if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query failed']);
    exit;
  }

  $product = $result->fetch_assoc();

  if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
  }

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$p_id])) {
    $_SESSION['cart'][$p_id]['qty'] += $qty;
  } else {
    $_SESSION['cart'][$p_id] = [
      'product_id' => $p_id,
      'product_name' => $p_name,
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chorchomcake</title>
  <link rel="stylesheet" href="./styles/products.css">
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
            <div class="price"><?= number_format($row['price'], 2) ?> บาท</div>
            <div class="product-buttons">
              <button class="btn-cart"
                onclick="addToCart(event, <?= $row['p_id'] ?>, '<?= addslashes($row['p_name']) ?>', <?= $row['price'] ?>)">
                เพิ่มในตะกร้า
              </button>
              <form action="checkout.php" method="POST" class="form-button">
                <input type="hidden" name="product_id" value="<?= $row['p_id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['p_name']) ?>">
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
          body: `product_id=${productId}&product_name=${encodeURIComponent(productName)}&price=${price}&ajax=1`
        })
        .then(res => {
          console.log("Raw response:", res);
          return res.json(); // ถ้า response ไม่ใช่ JSON ที่ valid ตรงนี้จะ error
        })
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'เพิ่มสินค้าในตะกร้า',
              text: `${productName} ถูกเพิ่มในตะกร้าแล้ว!`,
              confirmButtonText: 'ตกลง'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.reload();
              }
            });

            const cartCount = document.getElementById("cart-count");
            if (cartCount) {
              cartCount.innerText = data.cart_count;
            }

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
          // in this
          Swal.fire({
            icon: 'success',
            title: 'เพิ่มสินค้าในตะกร้า',
            text: `${productName} ถูกเพิ่มในตะกร้าแล้ว!`,
            confirmButtonText: 'ตกลง'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.reload();
            }
          });

        });
    }
  </script>

</body>

</html>