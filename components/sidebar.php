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
      <li><a href="paying_orders.php">รอการชำระเงิน</a></li>
      <li><a href="waiting_orders.php">รอตรวจสอบการชำระเงิน</a></li>
      <li><a href="pending_orders.php">กำลังดำเนินการ</a></li>
      <li><a href="transporting_orders.php">กำลังจัดส่ง</a></li>
      <li><a href="done_orders.php">สำเร็จ</a></li>
      <li><a href="canceled_orders.php">ยกเลิก</a></li>
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
      <span><i class="fas fa-users"></i> ลูกค้า</span>
      <i class="fas fa-chevron-down"></i>
    </li>
    <ul class="submenu">
      <li><a href="user.php">รายชื่อลูกค้า</a></li>
      <!-- <li><a href="add_user.php">เพิ่มชื่อผู้ใช้</a></li> -->
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