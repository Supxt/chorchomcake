<?php
session_start();
include('dbconnect.php');

// ตรวจสอบการส่งค่าแบบ POST และมีไฟล์แนบมาด้วย
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_slip'])) {

  $email = $_POST['email'];
  $total = $_POST['total'];

  // อัปโหลดไฟล์สลิป
  $fileName = time() . '_' . basename($_FILES["payment_slip"]["name"]);
  $targetDir = "uploads/";
  $targetFile = $targetDir . $fileName;

  // สร้างโฟลเดอร์ uploads ถ้ายังไม่มี
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

  // ตรวจสอบประเภทไฟล์
  if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
    $uploadOk = 0;
  }

  if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $targetFile)) {

      // หา order ล่าสุดของ user นี้
      $sql = "SELECT * FROM orders WHERE user_email = ? ORDER BY order_id DESC LIMIT 1";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $result = $stmt->get_result();
      $order = $result->fetch_assoc();

      if ($order) {
        // อัปเดตข้อมูลสลิปการโอน และสถานะ order
        $updateSql = "UPDATE orders SET payment_slip = ?, payment_method = 'โอนเงิน', order_status = 'รอตรวจสอบการชำระเงิน' WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('si', $fileName, $order['order_id']);
        $updateStmt->execute();

        // ล้างตะกร้า
        unset($_SESSION['cart']);
        unset($_SESSION['buy_now']);

        echo "<script>
                    alert('อัปโหลดสลิปเรียบร้อย รอการตรวจสอบ');
                    window.location.href = 'check_status.php';
                </script>";
      } else {
        echo "<script>
                    alert('ไม่พบรายการสั่งซื้อที่ตรงกับอีเมลนี้');
                    window.history.back();
                </script>";
      }
    } else {
      echo "<script>
                alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
                window.history.back();
            </script>";
    }
  } else {
    echo "<script>
            alert('อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น');
            window.history.back();
        </script>";
  }
} else {
  header("Location: checkout.php");
  exit;
}
