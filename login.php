<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าล็อกอิน</title>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #d79577, rgb(240, 219, 227));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .input-field:focus {
            border-color: rgb(161, 94, 82);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #d79577;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-btn:hover {
            background-color: rgb(161, 94, 82);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            color: #d79577;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <form method="post" action="login_db.php">
            <h2>เข้าสู่ระบบ</h2>
            <input type="text" name="email" class="input-field" placeholder="อีเมล" required>
            <input type="password" name="password" class="input-field" placeholder="รหัสผ่าน" required>
            <button type="submit" class="login-btn" name="login">ล็อกอิน</button>
        </form>

        <div class="footer">
            <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
        </div>
    </div>

    <!-- แสดง SweetAlert2 ถ้ามี error -->
    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: '<?= $_SESSION['error']; ?>',
                    confirmButtonColor: '#d79577'
                });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

</body>

</html>