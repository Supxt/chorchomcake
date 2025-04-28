<?php
session_start();
include('dbconnect.php');
$errors = array();

if (isset($_POST['reg_user'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $tel = mysqli_real_escape_string($conn, $_POST['tel']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmpassword = mysqli_real_escape_string($conn, $_POST['confirmpassword']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $sub_district = mysqli_real_escape_string($conn, $_POST['sub_district']);
    $district = mysqli_real_escape_string($conn, string: $_POST['district']);
    $province = mysqli_real_escape_string($conn, string: $_POST['province']);
    $post_code = mysqli_real_escape_string($conn, string: $_POST['post_code']);

    if (empty($email)) {
        array_push($errors, "กรุณากรอกอีเมล");
        $_SESSION['error'] = "กรุณากรอกอีเมล";
    }

    if (empty($firstname)) {
        array_push($errors, "กรุณากรอกชื่อ");
        $_SESSION['error'] = "กรุณากรอกชื่อ";
    }
    if (empty($lastname)) {
        array_push($errors, "กรุณากรอกนามสกุล");
        $_SESSION['error'] = "กรุณากรอกนามสกุล";
    }

    if (empty($tel)) {
        array_push($errors, "กรุณากรอกเบอร์โทรศัพท์");
        $_SESSION['error'] = "กรุณากรอกเบอร์โทรศัพท์";
    }

    if (empty($password)) {
        array_push($errors, "กรุณากรอกรหัสผ่าน");
        $_SESSION['error'] = "กรุณากรอกรหัสผ่าน";
    }

    if ($password != $confirmpassword) {
        array_push($errors, "รหัสผ่านทั้งสองต้องตรงกัน");
        $_SESSION['error'] = "รหัสผ่านทั้งสองต้องตรงกัน";
    }


    if (empty($address)) {
        array_push($errors, "กรุณากรอกที่อยู่");
        $_SESSION['error'] = "กรุณากรอกที่อยู่";
    }

    if (empty($sub_district)) {
        array_push($errors, "กรุณากรอกตำบล");
        $_SESSION['error'] = "กรุณากรอกตำบล";
    }

    if (empty($district)) {
        array_push($errors, "กรุณากรอกอำเภอ");
        $_SESSION['error'] = "กรุณากรอกอำเภอ";
    }

    if (empty($province)) {
        array_push($errors, "กรุณากรอกจังหวัด");
        $_SESSION['error'] = "กรุณากรอกจังหวัด";
    }

    if (empty($post_code)) {
        array_push($errors, "กรุณากรอกรหัสไปรษณีย์");
        $_SESSION['error'] = "กรุณากรอกรหัสไปรษณีย์";
    }

    $user_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $query = mysqli_query($conn, $user_check_query);
    $result = mysqli_fetch_assoc($query);

    if ($result) { // if user exists

        if ($result['email'] === $email) {
            array_push($errors, "อีเมลนี้ถูกใช้แล้ว");
        }
    }

    if (count($errors) == 0) {
        $password = md5($password);

        $sql = "INSERT INTO users (	first_name,last_name,tel,email,password,address,sub_district,district,province,post_code) VALUES (  '$firstname','$lastname','$tel','$email', '$password', '$address','$sub_district','$district', '$province', '$post_code')";
        mysqli_query($conn, $sql);

        $_SESSION['firstname'] = $firstname;
        $_SESSION['success'] = "ล็อกอินสำเร็จ!";
        header('location: login.php');
        exit;
    } else {
        $_SESSION['error'] = $errors[0];
        header("location: register.php");
        exit;
    }
}
