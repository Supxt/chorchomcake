<?php
include('../dbconnect.php');

// ลบผู้ใช้
if (isset($_GET['delete'])) {
  $user_id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
  echo "<script>
        Swal.fire({
            title: 'ลบผู้ใช้สำเร็จ',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            window.location.href = 'user.php';
        });
    </script>";
  exit;
}

// ดึงข้อมูลผู้ใช้
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
include('admin.php');

?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>จัดการผู้ใช้</title>
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

    .content {
      margin: 20px;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    table th,
    table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    table th {
      background: #f8bbd0;
      color: #5d4037;
    }

    .action-btn {
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      font-size: 14px;
    }

    .edit-btn {
      background-color: #4db6ac;
    }

    .edit-btn:hover {
      background-color: #009688;
    }

    .delete-btn {
      background-color: #ef5350;
    }

    .delete-btn:hover {
      background-color: #d32f2f;
    }
  </style>
</head>

<body>

  <div class="navbar">
    <div class="title"><i class="fas fa-users"></i> จัดการผู้ใช้</div>
    <button class="logout-btn" onclick="logout()">ออกจากระบบ</button>
  </div>

  <div class="content">
    <h2>รายชื่อผู้ใช้งาน</h2>
    <table>
      <thead>
        <tr>
          <th>ชื่อ-นามสกุล</th>
          <th>เบอร์โทร</th>
          <th>อีเมล</th>
          <th>วันที่สมัคร</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
            <td><?= htmlspecialchars($user['tel']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="action-btn edit-btn">แก้ไข</a>
              <button class="action-btn delete-btn" onclick="confirmDelete(<?= $user['user_id'] ?>)">ลบ</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'คุณต้องการลบผู้ใช้นี้จริงหรือ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'user.php?delete=' + id;
        }
      });
    }

    function logout() {
      if (confirm('คุณแน่ใจว่าต้องการออกจากระบบ?')) {
        window.location.href = 'login.php';
      }
    }
  </script>

</body>

</html>