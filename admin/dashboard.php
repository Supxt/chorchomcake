<?php
include_once('../dbconnect.php');
include('admin.php');
?>
<!DOCTYPE html>
<html lang="th">


<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ด</title>
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
      margin-bottom: 30px;
    }

    .card {
      background-color: #fff;
      border: 1px solid #f3d1c0;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      width: 170px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .card h3 {
      margin-bottom: 10px;
      color: #6d4c41;
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
    <div class="stats">
      <div class="card">
        <h3>ยอดจำหน่ายรวม</h3>
        <p id="totalSales">0 บาท</p>
      </div>
      <div class="card">
        <h3>สินค้าคงเหลือทั้งหมด</h3>
        <p id="stock">0 ชิ้น</p>
      </div>
      <div class="card">
        <h3>คำสั่งซื้อวันนี้</h3>
        <p id="todayOrders">0 รายการ</p>
      </div>
      <div class="card">
        <h3>ยอดคำสั่งซื้อรวม</h3>
        <p id="totalOrders">0 รายการ</p>
      </div>
    </div>

    <div class="filter-section">
      <div>
        <label>ช่วงเวลา:
          <select id="timeRange" onchange="updateChart()">
            <option value="day">วันนี้</option>
            <option value="week">สัปดาห์นี้</option>
            <option value="year">ปีนี้</option>
          </select>
        </label>
      </div>
      <div>
        <label>หมวดหมู่:
          <select id="category" onchange="updateChart()">
            <option value="ทั้งหมด">ทั้งหมด</option>
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
    <div class="card-1">
      <h3>สินค้าคงเหลือวันนี้</h3>
      <p id="stock">0 ชิ้น</p>
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
          y: {
            beginAtZero: true
          }
        }
      }
    });

    function updateChart() {
      const range = document.getElementById('timeRange').value;
      const category = document.getElementById('category').value;

      // จำลองการเปลี่ยนข้อมูลตามช่วงเวลา/หมวดหมู่
      const dataSets = {
        day: [3000],
        week: [1000, 2000, 1500, 2200, 1800, 1900, 2100],
        year: [10000, 11000, 9000, 8500, 12000, 13000, 11000, 10000, 9000, 8500, 9500, 11500]
      };

      const labels = {
        day: ['วันนี้'],
        week: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์', 'อาทิตย์'],
        year: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']
      };

      salesChart.data.labels = labels[range];
      salesChart.data.datasets[0].data = dataSets[range];
      salesChart.update();
    }

    // ใส่ข้อมูลจำลองกล่องแสดงยอดรวม
    document.getElementById('totalSales').textContent = '52,500 บาท';
    document.getElementById('stock').textContent = '2,342 ชิ้น';
    document.getElementById('todayOrders').textContent = '27 รายการ';
    document.getElementById('totalOrders').textContent = '1,280 รายการ';
    const stockData = [{
        name: 'เค้กมินิมอล',
        quantity: 150
      },
      {
        name: 'เค้กวินเทจ',
        quantity: 90
      },
      {
        name: 'คัพเค้ก',
        quantity: 200
      },
      {
        name: 'เค้กมะพร้าว',
        quantity: 120
      },
      {
        name: 'บาน้อฟฟี่เค้ก',
        quantity: 75
      }
    ];
    const stockList = document.getElementById('stockList');
    stockList.innerHTML = '';
    stockData.forEach(item => {
      const li = document.createElement('li');
      li.textContent = `${item.name}: ${item.quantity} ชิ้น`;
      stockList.appendChild(li);
    });
  </script>

</body>

</html>