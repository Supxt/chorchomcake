<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');

// Handle Buy Now or Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['p_id'], $_POST['p_name'], $_POST['price'])) {
    $p_id = intval($_POST['p_id']);
    $p_name = trim($_POST['p_name']);
    $price = floatval($_POST['price']);
    $qty = max(1, intval($_POST['qty'] ?? 1));
    $code = $_POST['code'] ?? '';

    if (isset($_POST['buy_now']) && $_POST['buy_now'] == '1') {
        // Buy Now case
        $_SESSION['buy_now'] = [
            'p_id' => $p_id,
            'p_name' => $p_name,
            'price' => $price,
            'qty' => $qty,
            'code' => $code,
        ];

        // Redirect to checkout
        header('Location: checkout.php');
        exit;
        echo "<script>console.log('Buy Now Session:', " . json_encode($_SESSION['buy_now']) . ");</script>";
    } else {
        // Add to Cart case
        $sql = "SELECT * FROM product WHERE p_id = $p_id";
        $result = $conn->query($sql);

        if ($result && $product = $result->fetch_assoc()) {
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

            $_SESSION['success_message'] = 'เพิ่มสินค้าในตะกร้าเรียบร้อยแล้ว!';
        } else {
            $_SESSION['error_message'] = 'ไม่พบข้อมูลสินค้า!';
        }

        // Redirect back to product page
        $redirect_url = $_POST['current_url'] ?? 'product.php';
        header("Location: $redirect_url");
        exit;
        echo "<script>console.log('Cart Session:', " . json_encode($_SESSION['cart']) . ");</script>";
    }
}

// ====== Product Listing Logic ======
$category = $_GET['category'] ?? 'all';
$limit = 6;
$page = max(1, intval($_GET['page'] ?? 1));
$start = ($page - 1) * $limit;

// Get product list
if ($category === 'all') {
    $sql_product = "SELECT * FROM product LIMIT $start, $limit";
    $total_sql = "SELECT COUNT(*) as total FROM product";
} else {
    $category_safe = $conn->real_escape_string($category);
    $sql_product = "SELECT p.* FROM product p
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

// Get all categories
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

<?php
if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])):
?>
<script>
<?php if (isset($_SESSION['success_message'])): ?>
    Swal.fire({
      icon: 'success',
      title: 'สำเร็จ',
      text: '<?= addslashes($_SESSION['success_message']) ?>',
      confirmButtonText: 'ตกลง'
    });
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    Swal.fire({
      icon: 'error',
      title: 'เกิดข้อผิดพลาด',
      text: '<?= addslashes($_SESSION['error_message']) ?>',
      confirmButtonText: 'ตกลง'
    });
<?php unset($_SESSION['error_message']); endif; ?>
</script>
<?php endif; ?>

<!-- Category Filter -->
<div class="categories">
    <div class="category-row">
        <a href="?category=all" class="category-item <?= $category === 'all' ? 'active' : '' ?>">สินค้าทั้งหมด</a>
        <?php while ($row = $result_category->fetch_assoc()): ?>
            <a href="?category=<?= urlencode($row['category_name']) ?>"
               class="category-item <?= $category === $row['category_name'] ? 'active' : '' ?>">
                <?= htmlspecialchars($row['category_name']) ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- Product Cards -->
<div class="container">
    <?php while ($product = $result_product->fetch_assoc()): ?>
    <div class="product-card">
        <a href="product_detail.php?p_id=<?= $product['p_id'] ?>" style="text-decoration: none; color: inherit;">
            <img src="image/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['p_name']) ?>" class="image">
            <div class="product-info">
                <div class="p_name"><?= htmlspecialchars($product['code']) . ' ' . htmlspecialchars($product['p_name']) ?></div>
                <div class="price"><?= number_format($product['price'], 2) ?> บาท</div>
                <div class="product-buttons">

                    <!-- Add to Cart -->
                    <form action="product.php" method="POST" style="display:inline-block;">
                        <input type="hidden" name="p_id" value="<?= $product['p_id'] ?>">
                        <input type="hidden" name="p_name" value="<?= htmlspecialchars($product['p_name']) ?>">
                        <input type="hidden" name="code" value="<?= htmlspecialchars($product['code']) ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                        <input type="hidden" name="qty" value="1">
                        <input type="hidden" name="current_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <button type="submit" class="btn-cart">เพิ่มในตะกร้า</button>
                    </form>

                    <!-- Buy Now -->
                    <form action="product.php" method="POST" class="form-button" style="display:inline-block;">
                        <input type="hidden" name="p_id" value="<?= $product['p_id'] ?>">
                        <input type="hidden" name="p_name" value="<?= htmlspecialchars($product['p_name']) ?>">
                        <input type="hidden" name="code" value="<?= htmlspecialchars($product['code']) ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                        <input type="hidden" name="qty" value="1">
                        <input type="hidden" name="buy_now" value="1">
                        <input type="hidden" name="current_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

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

<!-- Checkout Section (Only show if Buy Now) -->
<?php if ($buyNowProduct): ?>
<div class="checkout-container" style="margin-top: 30px;">
    <h2>สรุปการสั่งซื้อ</h2>
    <p>สินค้า: <?= htmlspecialchars($buyNowProduct['p_name']) ?></p>
    <p>จำนวน: <?= $buyNowProduct['qty'] ?></p>
    <p>ราคาต่อชิ้น: <?= number_format($buyNowProduct['price'], 2) ?> บาท</p>
    <p>รวมทั้งหมด: <strong><?= number_format($buyNowProduct['price'] * $buyNowProduct['qty'], 2) ?> บาท</strong></p>

    <form action="payment.php" method="POST">
        <input type="hidden" name="p_id" value="<?= $buyNowProduct['p_id'] ?>">
        <input type="hidden" name="qty" value="<?= $buyNowProduct['qty'] ?>">
        <input type="hidden" name="total_price" value="<?= $buyNowProduct['price'] * $buyNowProduct['qty'] ?>">
        <button type="submit" class="btn-buy">ยืนยันการสั่งซื้อ</button>
    </form>
</div>
<?php endif; ?>

</body>
</html>
