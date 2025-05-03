<?php
include('../dbconnect.php');

if (!isset($_GET['id'])) {
  header('Location: user.php');
  exit;
}

$user_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  echo "ไม่พบผู้ใช้นี้";
  exit;
}

// เมื่อบันทึกการแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, tel=?, email=? WHERE user_id=?");
  $stmt->bind_param("ssssi", $first_name, $last_name, $tel, $email, $user_id);
  $stmt->execute();

  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
  echo "<script>
      Swal.fire({
          title: 'บันทึกสำเร็จ',
          icon: 'success',
          confirmButtonText: 'ตกลง'
      }).then(() => {
          window.location.href = 'user.php';
      });
  </script>";
  exit;
}
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
  .edit-form input[type="email"],
  .edit-form input[type="tel"],
  .edit-form input[type="password"] {
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
  <h2>แก้ไขข้อมูลผู้ใช้</h2>
  <form action="user.php" method="POST">
    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

    <label for="first_name">ชื่อ</label>
    <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

    <label for="last_name">นามสกุล</label>
    <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

    <label for="tel">เบอร์โทรศัพท์</label>
    <input type="tel" name="tel" id="tel" value="<?= htmlspecialchars($user['tel']) ?>" required>

    <label for="email">อีเมล</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="password">รหัสผ่าน (ใส่เฉพาะถ้าต้องการเปลี่ยน)</label>
    <input type="password" name="password" id="password">

    <button type="submit">บันทึกการแก้ไข</button>
  </form>
</div>