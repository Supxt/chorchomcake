<?php
include_once('../dbconnect.php');

// Filter status
$selected_status = isset($_GET['order_status']) && $_GET['order_status'] !== '' ? $_GET['order_status'] : 'Pending';

// Pagination setup
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $items_per_page;

// Count total filtered orders
$count_sql = "SELECT COUNT(*) as total FROM orders WHERE order_status = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $selected_status);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch paginated orders
$sql = "SELECT * FROM orders WHERE order_status = ? ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selected_status);
$stmt->execute();
$result = $stmt->get_result();

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

.product-list th,
.product-list td {
  text-align: center;
}

.product-list td a {
  color: #1976d2;
  text-decoration: underline;
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
    <h1>คำสั่งซื้อที่รอดำเนินการทั้งหมด</h1>
    <p>คุณสามารถจัดการคำสั่งซื้อจากที่นี่</p>

    <!-- Filter form -->
    <form method="GET" class="filter-form">
      <label for="order_status">เลือกสถานะ:</label>
      <select name="order_status" id="order_status">
        <?php
        $statusOptions = ['Pending', 'Paid', 'Shipped', 'Cancelled', 'Completed'];
        foreach ($statusOptions as $status):
        ?>
        <option value="<?= $status ?>" <?= $selected_status === $status ? 'selected' : '' ?>>
          <?= $status ?>
        </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">กรอง</button>
    </form>

    <!-- Order table -->
    <table class="product-list">
      <thead>
        <tr>
          <th>#</th>
          <th>เลขที่คำสั่งซื้อ</th>
          <th>ชื่อผู้สั่ง</th>
          <th>อีเมล</th>
          <th>จำนวน</th>
          <th>ยอดรวม</th>
          <th>วันที่รับ</th>
          <th>สถานะ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php $index = $offset + 1; ?>
        <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $index++ ?></td>
          <td>
            <a href="order_detail.php?order_id=<?= $order['order_id'] ?>">
              <?= htmlspecialchars($order['order_no']) ?>
            </a>
          </td>
          <td><?= htmlspecialchars($order['full_name']) ?></td>
          <td><?= htmlspecialchars($order['user_email']) ?></td>
          <td><?= $order['total_qty'] ?></td>
          <td><?= number_format($order['grand_total'], 2) ?> บาท</td>
          <td><?= htmlspecialchars($order['receive_date']) ?></td>
          <td><?= htmlspecialchars($order['order_status']) ?></td>
          <td>
            <a href="order_detail.php?order_id=<?= $order['order_id'] ?>"><button>ดูรายละเอียด</button></a>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      <?php if ($current_page > 1): ?>
      <a href="?order_status=<?= urlencode($selected_status) ?>&page=<?= $current_page - 1 ?>">&laquo; ก่อนหน้า</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?order_status=<?= urlencode($selected_status) ?>&page=<?= $i ?>"
        <?= $i == $current_page ? 'style="font-weight:bold; text-decoration:underline;"' : '' ?>>
        <?= $i ?>
      </a>
      <?php endfor; ?>

      <?php if ($current_page < $total_pages): ?>
      <a href="?order_status=<?= urlencode($selected_status) ?>&page=<?= $current_page + 1 ?>">ถัดไป &raquo;</a>
      <?php endif; ?>
    </div>
  </div>
</div>