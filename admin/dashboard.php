<?php
include_once('../dbconnect.php');
include('admin.php');

$sql = "SELECT 
            od.p_id,
            p.p_name,
            p.price,
            p.quantity AS stock_remaining,
            p.image,
            SUM(od.o_qty) AS total_sold
        FROM 
            order_details od
        JOIN 
            product p ON od.p_id = p.p_id
        GROUP BY 
            od.p_id, p.p_name, p.price, p.quantity, p.image
        ORDER BY 
            total_sold DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);
$topProducts = [];

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $topProducts[] = $row;
  }
}

// Total Sales
$sqlTotalSales = "SELECT SUM(totol) AS total_sales FROM order_details";
$resTotalSales = mysqli_query($conn, $sqlTotalSales);
$totalSales = mysqli_fetch_assoc($resTotalSales)['total_sales'] ?? 0;

// Total Stock
$sqlStock = "SELECT SUM(quantity) AS total_stock FROM product";
$resStock = mysqli_query($conn, $sqlStock);
$totalStock = mysqli_fetch_assoc($resStock)['total_stock'] ?? 0;

// Today's Orders
$today = date('Y-m-d');
$sqlTodayOrders = "SELECT COUNT(*) AS today_orders FROM orders WHERE DATE(order_date) = '$today'";
$resTodayOrders = mysqli_query($conn, $sqlTodayOrders);
$todayOrders = mysqli_fetch_assoc($resTodayOrders)['today_orders'] ?? 0;

// Total Orders
$sqlTotalOrders = "SELECT COUNT(*) AS total_orders FROM orders";
$resTotalOrders = mysqli_query($conn, $sqlTotalOrders);
$totalOrders = mysqli_fetch_assoc($resTotalOrders)['total_orders'] ?? 0;


$sqlAvailableToday = "SELECT p_id, p_name, quantity, image, price FROM product WHERE quantity > 0";
$resultAvailableToday = mysqli_query($conn, $sqlAvailableToday);

$availableToday = [];
if ($resultAvailableToday) {
  while ($row = mysqli_fetch_assoc($resultAvailableToday)) {
    $availableToday[] = $row;
  }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ด</title>
  <link rel="stylesheet" href="../styles/products.css">
  <link rel="stylesheet" href="../styles/productcard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

    body {
      font-family: 'Kanit', sans-serif;
      background-color: #fff7f9;
      margin: 0;
    }

    .dashboard-container {
      max-width: 100%;
      margin: auto;
      margin-top: 30px;
      margin-left: 220px;
      /* <<< เพิ่มบรรทัดนี้ เพื่อเว้นที่ด้านซ้าย */
      padding: 0 20px;
    }

    .stats {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      margin : 30px
    }

    .card {
      background-color: #fff;
      border: 1px solid #f3d1c0;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      /* width: 170px; */
      width: 260px;
      height: 120px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .card h3 {
      margin-bottom: 10px;
      color: #6d4c41;
    }

    .text-container-center {
      text-align: center;
    }

    .text-header {
      color: #6d4c41;
      margin-bottom: 20px;
    }

    .filter-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      gap: 10px;
    }

    select,
    button {
      font-family: 'Kanit', sans-serif;
      padding: 8px 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #fff;
      cursor: pointer;
    }

    select:focus,
    button:focus {
      outline: none;
      border-color: #DAA994;
    }

    canvas {
      background-color: #ffffff;
      border-radius: 10px;
      padding: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }


    .card-1 {
      background-color: #fff;
      border: 1px solid #f3d1c0;
      padding: 100px;
      border-radius: 12px;
      text-align: center;
      width: 760px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      margin-top: 40px;
    }
  </style>
</head>

<body>


  <div class="dashboard-container">
  <div >

  <div class="text-container-center">
      <h1 class="text-header" >แดชบอร์ด</h1>
      
  </div>



</div>
<h3>ภาพรวม</h3>
    <div class="stats">
      <div class="card">
        <h3>ยอดจำหน่ายรวม</h3>
        <p ><?= number_format($totalSales, 2) ?> บาท</p>
      </div>
      <div class="card">
        <h3>คำสั่งซื้อวันนี้</h3>
        <p id=""><?= $todayOrders ?> รายการ</p>

      </div>
     
      <div class="card">
        <h3>ยอดคำสั่งซื้อรวม</h3>
        <p id=""><?= $totalOrders ?> รายการ</p>

      </div>
      <div class="card">
        <h3>สินค้าคงเหลือทั้งหมด</h3>
        <p id=""><?= number_format($totalStock) ?> ชิ้น</p>
      </div>
    </div>
<div>

<h3>สินค้าขายดี</h3>
<div class="container">
<?php foreach ($topProducts as $product): ?>

  <div class="product-card">
    <div href="/product_detail.php?p_id=<?= $product['p_id'] ?>" style="text-decoration: none; color: inherit;">
      <img src="../image/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['p_name']) ?>" class="image">
      <div class="product-info">
      <strong><?= htmlspecialchars($product['p_name']) ?></strong><br>
        ขายแล้ว: <?= $product['total_sold'] ?> ชิ้น<br>
        คงเหลือ: <?= $product['stock_remaining'] ?> ชิ้น<br>
        ราคา: <?= number_format($product['price'], 2) ?> บาท<br>
      </div>
</div>
  </div>
  <?php endforeach; ?>
  </div>
    
  <h3>สถิติยอดขายรวม</h3>

<div class="filter-section">
  <div>
    <label>ช่วงเวลา:
      <select id="timeRange" onchange="updateChart()">
        <option value="day">วันนี้</option>
        <option value="week" selected>สัปดาห์นี้</option>
        <option value="year">ปีนี้</option>
      </select>
    </label>
  </div>
  <div>
    <label>หมวดหมู่:
      <select id="category" onchange="updateChart()">
        <option value="ทั้งหมด" selected>ทั้งหมด</option>
        <option value="เค้กมินิมอล">เค้กมินิมอล</option>
        <option value="เค้กวินเทจ">เค้กวินเทจ</option>
        <option value="คัพเค้ก">คัพเค้ก</option>
        <option value="เค้กมะพร้าว">เค้กมะพร้าว</option>
        <option value="บาน้อฟฟี่เค้ก">บาน้อฟฟี่เค้ก</option>
      </select>
    </label>
  </div>
</div>
    <canvas id="salesChart" height="100"></canvas>




<h3 style="margin:30px" >สินค้าคงเหลือวันนี้</h3>
<div class="container">
  <?php foreach ($availableToday as $product): ?>
    <div class="product-card">
      <img src="../image/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['p_name']) ?>" class="image">
      <div class="product-info">
        <strong><?= htmlspecialchars($product['p_name']) ?></strong><br>
        คงเหลือ: <?= $product['quantity'] ?> ชิ้น<br>
        ราคา: <?= number_format($product['price'], 2) ?> บาท
      </div>
    </div>
  <?php endforeach; ?>
</div>

  </div>

  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์', 'อาทิตย์'],
        datasets: [{
          label: 'ยอดขาย (บาท)',
          data: [1200, 1900, 3000, 2500, 2200, 1300, 1500],
          backgroundColor: '#DAA994'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: 'สถิติยอดขาย'
          }
        },
        scales: {
      x: {
        ticks: {
          callback: function(value, index, ticks) {
            // Format from yyyy-mm-dd to dd-mm-yyyy
            const raw = this.getLabelForValue(value);
            const [y, m, d] = raw.split("-");
            return `${d}-${m}-${y}`;
          }
        }
      },
      y: {
        beginAtZero: true
      }
    }
      }
    });

    async function updateChart() {
      const range = document.getElementById('timeRange').value;
      const category = document.getElementById('category').value;

      const res = await fetch(`sales_data.php?range=${range}&category=${category}`);
      const json = await res.json();

      const labels = json.data.map(row => row.sale_date);
      const values = json.data.map(row => parseFloat(row.daily_sales));

      salesChart.data.labels = labels;
      salesChart.data.datasets[0].data = values;
      salesChart.update();
    }

    

    updateChart()

  </script>

</body>

</html>