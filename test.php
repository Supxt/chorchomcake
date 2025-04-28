<?php
include_once 'dbconnect.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "เชื่อมต่อฐานข้อมูลสำเร็จ!";
} else {
    echo "เชื่อมต่อไม่สำเร็จ";
}
?>
