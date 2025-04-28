<?php
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";  // เปลี่ยนเป็นของคุณ
$password = "";      // เปลี่ยนเป็นของคุณ
$dbname = "chorchomcake"; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $dbname);



// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error());
}

