<?php
session_start();

$invoiceData = $_SESSION['invoice_data'] ?? null;
if (!$invoiceData) {
  echo "<script>alert('ไม่พบข้อมูลใบสั่งซื้อ'); window.location.href='index.php';</script>";
  exit;
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบแจ้งหนี้ / ใบสั่งซื้อ</title>
  <link rel="stylesheet" href="styles/payment.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Sarabun', 'Prompt', sans-serif;
            color: #333;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        
        .page {
            width: 210mm;
            height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: #fff;
                padding: 0;
            }
            
            .page {
                width: 210mm;
                height: 297mm;
                padding: 20mm;
                box-shadow: none;
                margin: 0;
            }
            
            @page {
                size: A4;
                margin: 0;
            }
        }
        .invoice-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .customer-details {
            text-align: left;
        }
        .invoice-details {
            text-align: right;
        }
        .addresses {
            display: flex;
            margin-bottom: 30px;
        }
        .address-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 0 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f7f7f7;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            text-align: right;
        }
        .total-row {
            margin: 5px 0;
        }
        .            grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #8B4513;
            margin-top: 15px;
        }
        .notes-section {
            margin-top: 30px;
            border-top: 1px dashed #ddd;
            padding-top: 15px;
        }
        button:hover {
            background-color: #45a049 !important;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="invoice-header">
            ใบแจ้งหนี้ / ใบสั่งซื้อ
        </div>

    <div class="customer-info">
        <div class="customer-details">
            <div><strong>ลูกค้า:</strong> <span id="customer-name">ชื่อลูกค้า</span></div>
            <div><strong>Email:</strong> <span id="customer-email">อีเมลลูกค้า</span></div>
            <div><strong>โทร:</strong> <span id="customer-phone">เบอร์โทรลูกค้า</span></div>
        </div>
        <div class="invoice-details">
            <div><strong>เลขที่ใบสั่งซื้อ:</strong> <span id="invoice-number">-</span></div>
            <div><strong>วันที่:</strong> <span id="invoice-date">วว/ดด/ปปปป</span></div>
            <div><strong>วันที่รับสินค้า:</strong> <span id="receive-date">วว/ดด/ปปปป</span></div>
        </div>
    </div>

    <div class="addresses">
        <div class="address-box">
            <h3>ที่อยู่จัดส่ง:</h3>
            <div id="shipping-address">ที่อยู่จัดส่งของลูกค้า</div>
        </div>
        <div class="address-box">
            <h3>ที่อยู่ร้าน:</h3>
            <div id="store-address">ที่อยู่ของร้านค้า</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>รหัสสินค้า</th>
                <th>รายละเอียดสินค้า</th>
                <th>จำนวน</th>
                <th>ราคา/หน่วย</th>
                <th>ราคารวม</th>
            </tr>
        </thead>
        <tbody id="items-table">
            <tr>
                <td>SKU00001</td>
                <td>ชื่อสินค้า</td>
                <td>1</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row"><strong>รวมเป็นเงิน (ก่อน Vat7%):</strong> <span id="beforeVat">0.00</span> บาท</div>
        <div class="total-row"><strong>ภาษีมูลค่าเพิ่ม:</strong> <span id="vat">0.00</span> บาท</div>
        <div class="grand-total"><strong>จำนวนเงินทั้งสิ้น:</strong> <span id="grand-total">0.00</span> บาท</div>
    </div>
    
  

    <div class="section">
      <h2>เลือกช่องทางการชำระเงิน</h2>
      <div class="payment-methods">
        <label><strong>โอนเงินผ่านบัญชีธนาคาร</strong></label>

        <div style="display: flex; align-items: center; gap: 15px; background-color: #fefefe; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
          <img src="image/bbl.jpg" alt="ธนาคารกรุงเทพ" style="width: 100px; height: auto;">
          <div>
            <p><strong>ธนาคารกรุงเทพ</strong></p>
            <p>เลขที่บัญชี: <strong>904-7110-13-6</strong></p>
            <p>ชื่อบัญชี: บัณฑิตา คงโนนกอก</p>
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 15px; background-color: #fefefe; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
          <img src="image/kbank.jfif" alt="ธนาคารกสิกรไทย" style="width: 100px; height: auto;">
          <div>
            <p><strong>ธนาคารกสิกรไทย</strong></p>
            <p>เลขที่บัญชี: <strong>072-2882-14-8</strong></p>
            <p>ชื่อบัญชี: บัณฑิตา คงโนนกอก</p>
          </div>
        </div>
      </div>
    </div>

    <div class="notes-section">
        <h3>หมายเหตุ:</h3>
        <p id="invoice-notes"></p>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
            พิมพ์ใบแจ้งหนี้
        </button>
    </div>

    
    </div> <!-- Close page div -->

    <script>
        // Simple function to populate the invoice with data
        function populateInvoice(data) {
            // Customer info
            document.getElementById('customer-name').textContent = data.customerName;
            document.getElementById('customer-email').textContent = data.customerEmail;
            document.getElementById('customer-phone').textContent = data.customerPhone;
            
            // Invoice details
            document.getElementById('invoice-number').textContent = data.invoiceNumber;
            document.getElementById('invoice-date').textContent = data.invoiceDate;
            document.getElementById('receive-date').textContent = data.receiveDate;
            //receive-date
            
            // Addresses
            document.getElementById('shipping-address').textContent = data.shippingAddress;
            document.getElementById('store-address').textContent = data.storeAddress;
            
            // Items
            const itemsTable = document.getElementById('items-table');
            itemsTable.innerHTML = '';
            
            let subtotal = 0;
            
            data.items.forEach(item => {
                const row = document.createElement('tr');
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                row.innerHTML = `
                    <td>${item.sku}</td>
                    <td>${item.description}</td>
                    <td>${item.quantity}</td>
                    <td class="text-right">${item.price.toFixed(2).toLocaleString('th-TH')}</td>
                    <td class="text-right">${itemTotal.toFixed(2).toLocaleString('th-TH')}</td>
                `;
                
                itemsTable.appendChild(row);
            });
            
            // Totals
            const beforeVat = subtotal * 100/107;
            const vat = subtotal - beforeVat; 
            const grandTotal = subtotal;
            
            document.getElementById('beforeVat').textContent = beforeVat.toFixed(2).toLocaleString('th-TH');
            document.getElementById('vat').textContent = vat.toFixed(2).toLocaleString('th-TH');
            document.getElementById('grand-total').textContent = grandTotal.toFixed(2).toLocaleString('th-TH');
            
            // Add notes if available
            if (data.notes) {
                document.getElementById('invoice-notes').textContent = data.notes;
            }
        }
        
       
        
        const invoiceDataFromPHP = <?= json_encode($invoiceData) ?>;
        console.log({ invoiceDataFromPHP });
        window.onload = function () {
            populateInvoice(invoiceDataFromPHP);
        };
    </script>
</body>
</html>