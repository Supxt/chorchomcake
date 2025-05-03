<?php
include('../dbconnect.php');
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $tel = $_POST['tel'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, tel, password) VALUES (?, ?, ?, ?, ?)");
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param("sssss", $first_name, $last_name, $email, $tel, $password);

  if ($stmt->execute()) {
    $success = true;
  } else {
    echo "Error: " . $stmt->error;
  }
}


include('admin.php');
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>เพิ่มผู้ใช้</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

    body {
      margin: 0;
      font-family: 'Kanit', sans-serif;
      background: #fff7f9;
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
      text-align: center;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #5d4037;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-family: 'Kanit', sans-serif;
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
      <h2>เพิ่มผู้ใช้ใหม่</h2>
      <form method="POST">
        <div class="form-group">
          <label>ชื่อ</label>
          <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
          <label>นามสกุล</label>
          <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
          <label>อีเมล</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>เบอร์โทรศัพท์</label>
          <input type="tel" name="tel" required>
        </div>
        <div class="form-group">
          <label>รหัสผ่าน</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-group">
          <button type="submit">เพิ่มผู้ใช้</button>
        </div>
      </form>
    </div>
  </div>
  <?php if ($success): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
        title: 'เพิ่มผู้ใช้เรียบร้อยแล้ว!',
        icon: 'success',
        confirmButtonText: 'สำเร็จ'
      }).then(() => {
        window.location.href = 'user.php';
      });
    </script>
  <?php endif; ?>
</body>

</html>