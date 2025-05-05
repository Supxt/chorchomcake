<?php
include_once('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $order_id = intval($_POST['order_id']);
  $new_status = $_POST['new_status'];

  $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
  $stmt->bind_param("si", $new_status, $order_id);

  if ($stmt->execute()) {
    header("Location: order_detail.php?order_id=" . $order_id);
    exit;
  } else {
    echo "เกิดข้อผิดพลาดในการอัปเดตสถานะ";
  }
} else {
  echo "ไม่อนุญาตให้เข้าถึงโดยตรง";
}

?>