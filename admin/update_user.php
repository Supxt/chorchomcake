<?php
include('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = intval($_POST['user_id']);
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (!empty($password)) {
    // ถ้าเปลี่ยนรหัสผ่าน ให้ hash ก่อน
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, tel=?, email=?, password=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $tel, $email, $hashed_password, $user_id);
  } else {
    // ถ้าไม่เปลี่ยนรหัสผ่าน
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, tel=?, email=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $tel, $email, $user_id);
  }

  if ($stmt->execute()) {
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
  } else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถบันทึกข้อมูลได้',
                icon: 'error',
                confirmButtonText: 'ลองอีกครั้ง'
            }).then(() => {
                window.history.back();
            });
        </script>";
  }
}
