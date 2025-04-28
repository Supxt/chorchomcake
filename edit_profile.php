<?php
session_start();
include('dbconnect.php');

// ตรวจสอบว่าเข้าสู่ระบบอยู่
if (!isset($_SESSION['email'])) {
    $_SESSION['error'] = "กรุณาเข้าสู่ระบบก่อน";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_user'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $tel = mysqli_real_escape_string($conn, $_POST['tel']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $sub_district = mysqli_real_escape_string($conn, $_POST['sub_district']);
    $post_code = mysqli_real_escape_string($conn, $_POST['post_code']);

    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';

    $errors = [];

    // ตรวจสอบรหัสผ่านหากมีการกรอก
    $update_password = false;
    if (!empty($password) || !empty($confirmpassword)) {
        if ($password !== $confirmpassword) {
            $errors[] = "รหัสผ่านไม่ตรงกัน";
        } else {
            $update_password = true;
            $hashed_password = md5($password); // เปลี่ยนเป็น password_hash() ใน production
        }
    }

    if (count($errors) === 0) {
        if ($update_password) {
            $sql = "UPDATE users SET 
                        email = ?, 
                        password = ?, 
                        first_name = ?, 
                        last_name = ?, 
                        tel = ?, 
                        address = ?, 
                        province = ?, 
                        district = ?, 
                        sub_district = ?, 
                        post_code = ? 
                    WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssssss",
                $email,
                $hashed_password,
                $firstname,
                $lastname,
                $tel,
                $address,
                $province,
                $district,
                $sub_district,
                $post_code,
                $_SESSION['email']
            );
        } else {
            $sql = "UPDATE users SET 
                        email = ?, 
                        first_name = ?, 
                        last_name = ?, 
                        tel = ?, 
                        address = ?, 
                        province = ?, 
                        district = ?, 
                        sub_district = ?, 
                        post_code = ? 
                    WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssss",
                $email,
                $firstname,
                $lastname,
                $tel,
                $address,
                $province,
                $district,
                $sub_district,
                $post_code,
                $_SESSION['email']
            );
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "อัปเดตข้อมูลสำเร็จ";
            $_SESSION['email'] = $email; // อัปเดต session ถ้ามีการเปลี่ยนอีเมล
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
        }

        header("Location: user_profile.php");
        exit();
    } else {
        $_SESSION['error'] = implode(" | ", $errors);
        header("Location: user_profile.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ไม่สามารถเข้าถึงหน้านี้ได้โดยตรง";
    header("Location: user_profile.php");
    exit();
}
