<div class="sidebar">
    <ul>
      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-chart-line"></i> แดชบอร์ด</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="dashboard.php">วิเคราะห์ข้อมูล</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-bell"></i> คำสั่งซื้อ</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รอดำเนินการ</a></li>
        <li><a href="#">จัดส่งแล้ว</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-store"></i> สินค้าทั้งหมด</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="manage_product.php">จัดการสินค้า</a></li>
        <li><a href="add_product.php">เพิ่มสินค้า</a></li>
      </ul>

      <li onclick="toggleMenu(this)">
        <span><i class="fas fa-users"></i> ผู้ใช้งาน</span>
        <i class="fas fa-chevron-down"></i>
      </li>
      <ul class="submenu">
        <li><a href="#">รายชื่อผู้ใช้</a></li>
        <li><a href="#">สิทธิ์การเข้าถึง</a></li>
      </ul>
    </ul>
  </div>


  <style>

  .sidebar {
      width: 220px;
      background: #fff0f5;
      height: 100vh;
      position: fixed;
      overflow-y: auto;
      padding-top: 20px;
      border-right: 1px solid #ddd;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      padding: 12px 20px;
      color: #8d5544;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar li:hover,
    .sidebar li.active {
      background-color: #ffc0cb;
      color: #fff;
    }

    .sidebar li i {
      margin-right: 10px;
    }

    .submenu {
      display: none;
      background-color: #ffe4e1;
      padding-left: 20px;
    }

    .submenu a {
      display: block;
      padding: 8px 0;
      color: #8d5544;
      text-decoration: none;
    }

    .submenu a:hover {
      color: #d13c3c;
    }

    .content {
      margin-left: 240px;
      padding: 40px 20px;
    }

  </style>
