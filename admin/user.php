<?php
include('../dbconnect.php');

// ลบผู้ใช้
if (isset($_GET['delete'])) {
  $user_id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();

  // ✅ Redirect กลับมาหน้าหลักพร้อม query string
  header('Location: user.php?deleted=1');
  exit;
}

// ---- Pagination ----
$limit = 10; // จำนวนรายการต่อหน้า
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// นับจำนวนผู้ใช้ทั้งหมด
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// ดึงข้อมูลผู้ใช้ตามหน้า
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC LIMIT $limit OFFSET $offset");

include('admin.php');
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>จัดการลูกค้า</title>
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
      padding: 20px;
      margin-left: 220px;
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

    .action-btn {
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      font-size: 14px;
      margin-right: 5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .content th.action-col,
    .content td.action-col {
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="content">
    <h2>รายชื่อลูกค้า</h2>
    <table>
      <thead>
        <tr>
          <th>ชื่อ-นามสกุล</th>
          <th>เบอร์โทร</th>
          <th>อีเมล</th>
          <th class="action-col">จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
            <td><?= htmlspecialchars($user['tel']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td class="action-col">

              <!-- ปุ่มดูประวัติการสั่งซื้อ -->
              <a href="order_history.php?user_id=<?= $user['user_id'] ?>" class="action-btn" style="background-color: #5d4037;">
                <i class="fas fa-shopping-cart"></i> ดูออเดอร์
              </a>

              <!-- ปุ่มแก้ไข -->
              <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="action-btn" style="background-color:#ef9a9a;">
                <i class="fas fa-edit"></i> แก้ไข
              </a>

              <!-- ปุ่มลบ -->
              <button onclick="confirmDelete(<?= $user['user_id'] ?>)" class="action-btn" style="background-color:#f44336;">
                <i class="fas fa-trash"></i> ลบ
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div class="pagination">
      <?php if ($current_page > 1): ?>
        <a href="?page=<?= $current_page - 1 ?>">&laquo; ก่อนหน้า</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" <?= $i == $current_page ? 'style="font-weight:bold; text-decoration:underline;"' : '' ?>>
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?= $current_page + 1 ?>">ถัดไป &raquo;</a>
      <?php endif; ?>
    </div>
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

    // ✅ ตรวจสอบการลบสำเร็จ
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('deleted') === '1') {
        Swal.fire({
          title: 'ลบผู้ใช้สำเร็จ',
          icon: 'success',
          confirmButtonText: 'ตกลง'
        }).then(() => {
          // ล้าง query string หลังแสดงเสร็จเพื่อไม่ให้โชว์ซ้ำตอนรีเฟรช
          const url = new URL(window.location);
          url.searchParams.delete('deleted');
          window.history.replaceState({}, document.title, url);
        });
      }
    });

    function logout() {
      if (confirm('คุณแน่ใจว่าต้องการออกจากระบบ?')) {
        window.location.href = 'login.php';
      }
    }
  </script>

</body>

</html>