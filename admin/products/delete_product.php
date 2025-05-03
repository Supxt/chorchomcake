<?php
include('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $p_id = $_POST['p_id'];

  // ลบสินค้าจากฐานข้อมูล
  $sql = "DELETE FROM product WHERE p_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $p_id);
  if ($stmt->execute()) {
    header('Location: manage_product.php');
  } else {
    echo "เกิดข้อผิดพลาดในการลบสินค้า.";
  }
}
