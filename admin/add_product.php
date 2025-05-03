<?php
include('../dbconnect.php');
include('admin.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['p_name'];
  $price = $_POST['price'];
  $category_id = $_POST['category_id'];
  $description = $_POST['description'];
  $quantity = $_POST['quantity'] ?? 1;

  $image = '';
  if ($_FILES['image']['name']) {
    $image_name = time() . '_' . basename($_FILES['image']['name']);
    $target = '../image/' . $image_name;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $image = $image_name;
    }
  }

  // Get next auto-increment ID for product
  $res = $conn->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product'");
  $nextId = $res->fetch_assoc()['AUTO_INCREMENT'] ?? 1;

  // Generate product code e.g. CH2-001
  $code = 'CH' . $category_id . str_pad($nextId, 3, '0', STR_PAD_LEFT);

  $stmt = $conn->prepare("INSERT INTO product (p_name, code, price, description, quantity, image, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param("ssdsisi", $name, $code, $price, $description, $quantity, $image, $category_id);

  if ($stmt->execute()) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
      Swal.fire({
        title: 'เพิ่มสินค้าเรียบร้อยแล้ว!',
        text: 'รหัสสินค้า: $code',
        icon: 'success',
        confirmButtonText: 'ตกลง'
      }).then(() => {
        window.location.href = 'manage_product.php';
      });
    </script>";
    exit;
  } else {
    echo "Error: " . $stmt->error;
  }
}

$cat_result = $conn->query("SELECT * FROM category");
?>


<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>เพิ่มสินค้า</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

    body {
      margin: 0;
      font-family: 'Kanit', sans-serif;
      background: #fff7f9;
    }

    .navbar {
      background-color: #d7ccc8;
      color: #5d4037;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar .title {
      font-size: 20px;
    }

    .logout-btn {
      background-color: #ef9a9a;
      border: none;
      padding: 8px 16px;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .logout-btn:hover {
      background-color: #e57373;
    }

    .content {
      margin-left: 240px;
      padding: 40px 20px;
    }

    .form-container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      border: 1px solid #f8bbd0;
      max-width: 600px;
      margin: auto;
    }

    .form-container h2 {
      color: #6d4c41;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #5d4037;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-family: 'Kanit', sans-serif;
    }

    .form-group input[type="file"] {
      padding: 5px;
    }

    .form-group button {
      width: 100%;
      padding: 10px;
      background: #d88c9a;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    .form-group button:hover {
      background: #c46a7d;
    }
  </style>
</head>

<body>

  <!-- Content -->
  <div class="content">
    <div class="form-container">
      <h2>เพิ่มสินค้าใหม่</h2>
      <form method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label>ชื่อสินค้า</label>
    <input type="text" name="p_name" required>
  </div>
  <div class="form-group">
    <label>จำนวนสินค้า</label>
    <input type="number" name="quantity" required>
  </div>
  <div class="form-group">
    <label>ราคาสินค้า</label>
    <input type="number" name="price" step="0.01" required>
  </div>
  <div class="form-group">
    <label>รายละเอียดสินค้า</label>
    <textarea name="description" required></textarea>
  </div>
  <div class="form-group">
    <label>หมวดหมู่</label>
    <select name="category_id" required>
      <option value="">-- เลือกหมวดหมู่ --</option>
      <?php while ($cat = $cat_result->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>"><?= $cat['category_name'] ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="form-group">
    <label>รูปภาพ</label>
    <input type="file" name="image" accept="image/*">
  </div>
  <div class="form-group">
    <button type="submit">เพิ่มสินค้า</button>
  </div>
</form>


    </div>
  </div>

  <script>
    function toggleMenu(el) {
      const next = el.nextElementSibling;
      if (next && next.classList.contains('submenu')) {
        next.style.display = next.style.display === 'block' ? 'none' : 'block';
      }
    }

    // ฟังก์ชันเปิด-ปิดเมนูย่อย
    function toggleMenu(el) {
      const next = el.nextElementSibling;
      if (next && next.classList.contains('submenu')) {
        next.style.display = next.style.display === 'block' ? 'none' : 'block';
      }
    }

    function logout() {
      if (confirm('คุณแน่ใจว่าต้องการออกจากระบบ?')) {
        window.location.href = 'login.php';
      }
    }
  </script>
  <select name="category_id" required>
    <option value="">-- เลือกหมวดหมู่ --</option>
    <?php
    $cat_result = $conn->query("SELECT * FROM category");
    while ($cat = $cat_result->fetch_assoc()):
    ?>
      <option value="<?= $cat['category_id'] ?>"><?= $cat['category_name'] ?></option>
    <?php endwhile; ?>
  </select>
</body>

</html>