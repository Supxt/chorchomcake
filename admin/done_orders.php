<?php
include_once('../dbconnect.php');

$orders_sql = "SELECT * FROM `orders`";
$orders_result = $conn->query($orders_sql);

$orders = [];

if ($orders_result && $orders_result->num_rows > 0) {
    while ($row = $orders_result->fetch_assoc()) {
        $orders[] = $row; // Push each row into the array
    }

    // Log entire array to browser console
    echo "<script>console.log(" . json_encode($orders) . ");</script>";
} else {
    echo "No orders found or query failed.";
}
// ดึงรายการหมวดหมู่ทั้งหมด
$category_sql = "SELECT * FROM category";
$category_result = $conn->query($category_sql);

// ตรวจสอบหมวดหมู่ที่เลือก
$selected_category = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;


// Pagination setup
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $items_per_page;

// ดึงจำนวนสินค้าทั้งหมด
$count_sql = "SELECT COUNT(*) as total FROM orders" . ($selected_category ? " WHERE category_id = ?" : "");
$count_stmt = $conn->prepare($count_sql);
if ($selected_category) {
  $count_stmt->bind_param("i", $selected_category);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// ดึงข้อมูลสินค้าพร้อม LEFT JOIN กับ category
// เขียน SQL ด้วยการฝังค่า limit และ offset ตรง ๆ
$sql = "
  SELECT p.*, c.category_name 
  FROM product p 
  LEFT JOIN category c ON p.category_id = c.category_id
";

if ($selected_category) {
  $sql .= " WHERE p.category_id = " . (int)$selected_category;
}

$sql .= " LIMIT $items_per_page OFFSET $offset";

$result = $conn->query($sql);

include('admin.php');
?>

<style>
.product-list {
  margin-top: 30px;
  width: 100%;
  border-collapse: collapse;
}

.product-list th,
.product-list td {
  padding: 12px;
  border: 1px solid #ddd;
}

.product-list th {
  background-color: #f8bbd0;
}

.product-list th.id-col,
.product-list td.id-col,
.product-list th.name-col,
.product-list td.name-col {
  text-align: left;
}

.product-list th.price-col,
.product-list td.price-col {
  text-align: right;
}

.product-list th.action-col,
.product-list td.action-col {
  text-align: center;
}

.product-list td button {
  padding: 5px 10px;
  background-color: #ef9a9a;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 5px;
}

.product-list td button:hover {
  background-color: #e57373;
}

.product-list img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 5px;
}

.filter-form {
  margin-bottom: 20px;
}

.filter-form select,
.filter-form button {
  padding: 8px;
  font-family: 'Kanit', sans-serif;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-right: 8px;
}

.filter-form button {
  background-color: #ef9a9a;
  color: white;
  cursor: pointer;
}

.filter-form button:hover {
  background-color: #e57373;
}

.pagination {
  margin-top: 20px;
  text-align: center;
}

.pagination a {
  display: inline-block;
  padding: 8px 12px;
  margin: 0 4px;
  background-color: #f8bbd0;
  color: #333;
  border-radius: 5px;
  text-decoration: none;
}

.pagination a:hover,
.pagination a[style*="font-weight:bold"] {
  background-color: #e57373;
  color: white;
}
</style>

<div class="content">
  <div class="card">
    <h1>จัดการสินค้าทั้งหมด</h1>
    <p>คุณสามารถจัดการสินค้าได้จากที่นี่</p>

    <!-- ฟอร์มกรองหมวดหมู่ -->
    <form method="GET" class="filter-form">
      <label for="category_id">เลือกหมวดหมู่:</label>
      <select name="category_id" id="category_id">
        <option value="">-- แสดงทั้งหมด --</option>
        <?php while ($cat = $category_result->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>" <?= $selected_category == $cat['category_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['category_name']) ?>
        </option>
        <?php endwhile; ?>
      </select>
      <button type="submit">แสดงผล</button>
    </form>

    <!-- ตารางสินค้า -->

    <!-- <h1>Order Management</h1> -->

    <?php if (count($orders) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Order No</th>
          <th>Address</th>
          <th>Tel</th>
          <th>Receive Date</th>
          <th>Total Qty</th>
          <th>Total Price</th>
          <th>VAT</th>
          <th>Grand Total</th>
          <th>Payment Slip</th>
          <th>Payment Method</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $index => $order): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($order['full_name']) ?></td>
          <td><?= htmlspecialchars($order['user_email']) ?></td>
          <td><?= $order['order_no'] ?></td>
          <td><?= htmlspecialchars($order['address']) ?></td>
          <td><?= $order['tel'] ?></td>
          <td><?= $order['receive_date'] ?></td>
          <td><?= $order['total_qty'] ?></td>
          <td><?= number_format($order['total_price'], 2) ?></td>
          <td><?= number_format($order['vat'], 2) ?></td>
          <td><?= number_format($order['grand_total'], 2) ?></td>
          <td><a href="uploads/<?= $order['payment_slip'] ?>" target="_blank"><?= $order['payment_slip'] ?></a></td>
          <td><?= $order['payment_method'] ?></td>
          <td><?= $order['order_status'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p>No orders found.</p>
    <?php endif; ?>

    <script>
    const ordersData = <?= json_encode($orders, JSON_PRETTY_PRINT) ?>;
    console.log("Orders:", ordersData);
    </script>



    <!-- Pagination -->
    <div class="pagination">
      <?php if ($current_page > 1): ?>
      <a href="?category_id=<?= $selected_category ?>&page=<?= $current_page - 1 ?>">&laquo; ก่อนหน้า</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?category_id=<?= $selected_category ?>&page=<?= $i ?>"
        <?= $i == $current_page ? 'style="font-weight:bold; text-decoration:underline;"' : '' ?>>
        <?= $i ?>
      </a>
      <?php endfor; ?>

      <?php if ($current_page < $total_pages): ?>
      <a href="?category_id=<?= $selected_category ?>&page=<?= $current_page + 1 ?>">ถัดไป &raquo;</a>
      <?php endif; ?>
    </div>
  </div>
</div>