<?php
include_once('../dbconnect.php');

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
$count_sql = "SELECT COUNT(*) as total FROM product" . ($selected_category ? " WHERE category_id = ?" : "");
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
    <table class="product-list">
      <thead>
        <tr>
          <th class="id-col">รหัสสินค้า</th>
          <th class="name-col">ชื่อสินค้า</th>
          <th class="price-col">ราคา (บาท)</th>
          <th>หมวดหมู่</th>
          <th>รูปภาพ</th>
          <th class="action-col">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="id-col"><?= $row['p_id'] ?></td>
            <td class="name-col"><?= htmlspecialchars($row['p_name']) ?></td>
            <td class="price-col"><?= number_format($row['price'], 2) ?> </td>
            <td><?= isset($row['category_name']) ? htmlspecialchars($row['category_name']) : '-' ?></td>
            <td>
              <?php if (!empty($row['image'])): ?>
                <img src="../image/<?= htmlspecialchars($row['image']) ?>" alt="รูปสินค้า">
              <?php else: ?>
                <img src="../uploads/default.jpg" alt="ไม่มีรูป" />
              <?php endif; ?>
            </td>
            <td class="action-col">
              <a href="edit_product.php?p_id=<?= $row['p_id'] ?>"><button>แก้ไข</button></a>
              <form action="delete_product.php" method="POST" style="display:inline;">
                <input type="hidden" name="p_id" value="<?= $row['p_id'] ?>">
                <button type="submit" onclick="return confirm('คุณแน่ใจว่าต้องการลบสินค้านี้?')">ลบ</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      <?php if ($current_page > 1): ?>
        <a href="?category_id=<?= $selected_category ?>&page=<?= $current_page - 1 ?>">&laquo; ก่อนหน้า</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?category_id=<?= $selected_category ?>&page=<?= $i ?>" <?= $i == $current_page ? 'style="font-weight:bold; text-decoration:underline;"' : '' ?>>
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($current_page < $total_pages): ?>
        <a href="?category_id=<?= $selected_category ?>&page=<?= $current_page + 1 ?>">ถัดไป &raquo;</a>
      <?php endif; ?>
    </div>
  </div>
</div>