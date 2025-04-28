<?php
include('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['p_name'];
  $code = $_POST['code'];
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
        icon: 'success',
        confirmButtonText: 'สำเร็จ'
      }).then(() => {
        window.location.href = 'admin.php';
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

    .sidebar {
      width: 220px;
      background: #fff0f5;
      height: 100vh;
      position: fixed;
      overflow-y: auto;
      padding-top: 20px;
      border-right: 1px solid #ddd;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      padding: 12px 20px;
      color: #8d5544;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar li:hover,
    .sidebar li.active {
      background-color: #ffc0cb;
      color: #fff;
    }

    .sidebar li i {
      margin-right: 10px;
    }

    .submenu {
      display: none;
      background-color: #ffe4e1;
      padding-left: 20px;
    }

    .submenu a {
      display: block;
      padding: 8px 0;
      color: #8d5544;
      text-decoration: none;
    }

    .submenu a:hover {
      color: #d13c3c;
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

  <!-- Navbar -->
  <div class="navbar">
    <div class="title"><i class="fas fa-box"></i> เพิ่มสินค้า</div>
    <button class="logout-btn" onclick="logout()">ออกจากระบบ</button>
  </div>
  <div class="sidebar">
    <ul>
      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-chart-line"></i> แดชบอร์ด</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">วิเคราะห์ข้อมูล</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-bell"></i> คำสั่งซื้อ</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รอดำเนินการ</a></li>
        <li><a href="#">จัดส่งแล้ว</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-store"></i> สินค้าทั้งหมด</span>
        <i class="fas fa-chevron-down"></i>
      </li>

      <ul class="submenu">
        <li><a href="manage_product.php">จัดการสินค้า</a></li>
        <li><a href="add_product.php">เพิ่มสินค้า</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-users"></i> ผู้ใช้งาน</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รายชื่อผู้ใช้</a></li>
        <li><a href="#">สิทธิ์การเข้าถึง</a></li>
      </ul>
    </ul>
  </div>

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
          <label>รหัสสินค้า</label>
          <input type="text" name="code" required>
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
          <textarea name="description" rows="4" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-family: 'Kanit', sans-serif;"></textarea>

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