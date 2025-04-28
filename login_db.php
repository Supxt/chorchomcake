<?php
session_start();
include('dbconnect.php');

$errors = [];

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ตรวจสอบค่าว่าง
    if (empty($email)) {
        $errors[] = "กรุณากรอกอีเมล";
    }

    if (empty($password)) {
        $errors[] = "กรุณากรอกรหัสผ่าน";
    }

    if (count($errors) === 0) {
        $hashed_password = md5($password); // ควรใช้ password_hash + password_verify ใน production
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$hashed_password'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result); // ดึงข้อมูลผู้ใช้

            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name']; // <-- เซฟชื่อจริง
            $_SESSION['success'] = "เข้าสู่ระบบสำเร็จ";
            unset($_SESSION['error']);

            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = implode(" | ", $errors);
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "โปรดเข้าสู่ระบบผ่านฟอร์มที่กำหนด";
    header("Location: login.php");
    exit();
}
