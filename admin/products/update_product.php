<?php
include('../dbconnect.php');

if (isset($_POST['p_id'], $_POST['p_name'], $_POST['quantity'], $_POST['description'], $_POST['price'], $_POST['category_id'])) {
  $p_id = $_POST['p_id'];
  $p_name = $_POST['p_name'];
  $quantity = $_POST['quantity'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $category_id = $_POST['category_id']; // รับค่าหมวดหมู่ที่เลือก

  $imageName = null;

  // ตรวจสอบและอัปโหลดรูปภาพใหม่ถ้ามี
  if (!empty($_FILES['image']['name'])) {
    $targetDir = "../image/";
    $imageName = time() . '_' . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // ตรวจสอบไฟล์ภาพ
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
      move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    } else {
      echo "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ";
      exit;
    }
  }

  // ถ้ามีการอัปโหลดรูปใหม่
  if ($imageName) {
    $sql = "UPDATE product SET p_name=?, quantity=?, description=?, price=?, category_id=?, image=? WHERE p_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisdisi", $p_name, $quantity, $description, $price, $category_id, $imageName, $p_id);
  } else {
    // ไม่มีการอัปโหลดรูปใหม่
    $sql = "UPDATE product SET p_name=?, quantity=?, description=?, price=?, category_id=? WHERE p_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisdii", $p_name, $quantity, $description, $price, $category_id, $p_id);
  }
  // Execute query
  if ($stmt->execute()) {
    header("Location: manage_product.php"); // เปลี่ยนหน้าไปที่หน้าแสดงสินค้าทั้งหมด
    exit;
  } else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
  }

  // ปิด statement และเชื่อมต่อ
  $stmt->close();
} else {
  echo "ข้อมูลไม่ครบ";
}

$conn->close();
