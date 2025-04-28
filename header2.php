<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chorchomcake</title>
    <style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #d79577;
  display: flex;
  justify-content: space-between; /* ทำให้เมนูกระจายเต็มแถว */
  align-items: center;
  padding: 0 16px; /* เพิ่มระยะห่างด้านข้าง */
}

.topnav .menu-left {
  display: flex;
}

.topnav .menu-right {
  margin-left: auto; /* ดันให้ไปขวาสุด */
}

.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color:pink;
}

.topnav a.active {
  background-color:rgb(141, 85, 68);
  color: white;
}
.center-image {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh; /* ให้รูปอยู่กึ่งกลางของหน้าจอ */
        }
        .center-image img {
            max-width: 50%; /* กำหนดขนาดสูงสุดของรูป */
            height: auto;
        }
        .logo {
  display: flex;
  align-items: center;
  margin-right: 20px;
}

.logo img {
  height: 40px;
  margin-right: 10px;
}
</style>
</head>

<body>
<div class="topnav">
<div class="logo">
    <img src="image/img2.png" alt="Logo">
    <span style="color: white; font-size: 22px; font-weight: bold;">Chorchomcake</span>
</div>
<div class = "content">
  <div class="menu-left">
    <a class="active" href="#home">หน้าแรก</a>
  </div>
  <div class="menu-right">
    <a href="index.php">ออกจากระบบ</a>
  </div>
</div>

</body>
</html>
