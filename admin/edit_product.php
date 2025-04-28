<?php
include('../dbconnect.php');

// ตรวจสอบว่ามีการส่งค่า p_id มาหรือไม่
if (!isset($_GET['p_id'])) {
  echo "ไม่พบรหัสสินค้า";
  exit;
}

$p_id = $_GET['p_id'];

// ฟังก์ชันเพื่อดึงข้อมูลสินค้าจากฐานข้อมูล
function getProduct($conn, $p_id)
{
  $sql = "SELECT * FROM product WHERE p_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $p_id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

// ฟังก์ชันเพื่อดึงข้อมูลหมวดหมู่ทั้งหมด
function getCategories($conn)
{
  $sql = "SELECT * FROM category";
  return $conn->query($sql);
}

$product = getProduct($conn, $p_id);
$category_result = getCategories($conn);
$sql_category = "SELECT * FROM category";
$result_category = $conn->query($sql_category);
include('admin.php');
?>

<style>
  .edit-form {
    max-width: 500px;
    margin: 40px auto;
    padding: 20px;
    background-color: #fce4ec;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  .edit-form h2 {
    margin-bottom: 20px;
    text-align: center;
  }

  .edit-form label {
    display: block;
    margin: 10px 0 5px;
  }

  .edit-form input[type="text"],
  .edit-form input[type="number"],
  .edit-form select {
    width: 95%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  .edit-form button {
    margin-top: 20px;
    width: 100%;
    background-color: #ec407a;
    color: white;
    border: none;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
  }

  .edit-form button:hover {
    background-color: #d81b60;
  }
</style>

<div class="edit-form">
  <h2>แก้ไขข้อมูลสินค้า</h2>
  <form action="update_product.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="p_id" value="<?= $product['p_id'] ?>">

    <label for="p_name">ชื่อสินค้า</label>
    <input type="text" name="p_name" id="p_name" value="<?= htmlspecialchars($product['p_name']) ?>" required>

    <label for="quantity">จำนวนสินค้า</label>
    <input type="number" step="1" name="quantity" id="quantity" value="<?= (int)$product['quantity'] ?>" required>

    <label for="description">รายละเอียดสินค้า</label>
    <input type="text" name="description" id="description" value="<?= htmlspecialchars($product['description']) ?>" required>

    <label for="price">ราคา (บาท)</label>
    <input type="number" step="0.01" name="price" id="price" value="<?= $product['price'] ?>" required>

    <!-- หมวดหมู่สินค้า -->
    <label for="category_id">เลือกหมวดหมู่</label>
    <select name="category_id" id="category_id" required>
      <?php while ($category = $result_category->fetch_assoc()): ?>
        <option value="<?= $category['category_id'] ?>" <?= ($category['category_id'] == $product['category_id']) ? 'selected' : '' ?>>
          <?= $category['category_name'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label for="image">รูปภาพสินค้า</label>
    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
    <img id="oldImage" src="../image/<?= htmlspecialchars($product['image']) ?>"
      alt="รูปสินค้า"
      style="margin-bottom:10px; max-width: 200px; height: auto; border: 1px solid #ccc; border-radius: 8px;">

    <img id="preview" src="#"
      alt="Preview"
      style="display: none; max-width: 200px; height: auto; border: 1px solid #ccc; border-radius: 8px;">

    <button type="submit">บันทึกการแก้ไข</button>
  </form>
</div>

<script>
  function previewImage(event) {
    const preview = document.getElementById('preview');
    const oldImage = document.getElementById('oldImage');
    const file = event.target.files[0];

    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        oldImage.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  }
</script>