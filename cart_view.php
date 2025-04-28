<?php
session_start();
include('dbconnect.php');
include('./components/navbar.php');
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>รถเข็นของฉัน</title>
  <style>
    .cart-body {
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
    }

    .cart-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }

    .cart-title {
      text-align: center;
      margin-bottom: 30px;
    }

    .cart-empty {
      text-align: center;
      background: white;
      padding: 40px;
      border-radius: 12px;
    }

    .cart-header {
      background-color: #DAA994;
      color: white;
      padding: 10px 20px;
      border-radius: 12px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
    }

    .cart-header>div {
      flex: 1;
      text-align: center;
      font-weight: bold;
    }

    .cart-item {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 16px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;

    }

    .cart-item img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }

    .cart-item>div {
      flex: 1;
      text-align: center;
    }

    .item-total {
      font-weight: bold;
      color: #e53935;
    }

    .cart-footer {
      background-color: #DAA994;
      border-radius: 12px;
      padding: 16px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      font-size: 18px;
    }

    .checkout-button {
      background-color: #DAA994;
      color: white;
      padding: 12px 24px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      align-items: center;
      margin-top: 10px;
      margin-left: 800px;
    }

    .checkout-button:hover {
      background-color: rgb(114, 52, 52);
    }

    .qty-input {
      width: 130px;
    }
  </style>
</head>

<body class="cart-body">

  <div class="cart-container">
    <h1 class="cart-title">รถเข็นของฉัน</h1>

    <?php if (empty($cart)): ?>
      <div class="cart-empty">
        <strong>ไม่มีสินค้าในตะกร้า</strong>
      </div>
    <?php else: ?>
      <div class="cart-header">
        <div>รูปภาพ</div>
        <div>รหัสสินค้า</div>
        <div>ชื่อสินค้า</div>
        <div>ราคาต่อชิ้น</div>
        <div>จำนวน</div>
        <div>ราคารวม</div>
        <div></div>
      </div>

      <?php foreach ($cart as $product):
        $item_total = $product['price'] * $product['qty'];
        $total_price += $item_total;
      ?>
        <div class="cart-item">
          <div><img src="image/<?php echo $product['image']; ?>" alt="Product Image"></div>
          <div class="item-code"><?php echo $product['code']; ?></div>
          <div class="item-name"><?php echo $product['product_name']; ?></div>
          <div class="item-price"><?php echo number_format($product['price'], 2); ?> บาท</div>
          <input type="number" class="qty-input" data-price="<?php echo $product['price']; ?>" value="<?php echo $product['qty']; ?>" min="1">
          <div class="item-total"><?php echo number_format($item_total, 2); ?> บาท</div>
          <div class="item-remove">
            <a href="cart_remove.php?p_id=<?php echo $product['product_id']; ?>">🗑️</a>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="cart-footer">
        <strong>รวมทั้งสิ้น:</strong>
        <span><?php echo number_format($total_price, 2); ?> บาท</span>
      </div>

      <form action="checkout.php" method="get">
        <?php foreach ($cart as $product): ?>
          <input type="hidden" name="product_ids[]" value="<?php echo $product['product_id']; ?>">
          <input type="hidden" name="qtys[<?php echo $product['product_id']; ?>]" value="<?php echo $product['qty']; ?>" id="qty_<?php echo $product['product_id']; ?>">
        <?php endforeach; ?>
        <button type="submit" class="checkout-button">ตรวจสอบรายการสินค้า</button>
      </form>
    <?php endif; ?>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const qtyInputs = document.querySelectorAll('.qty-input');

      qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
          const newQty = parseInt(this.value);
          const price = parseFloat(this.dataset.price);
          const item = this.closest('.cart-item');
          const itemTotalElement = item.querySelector('.item-total');

          if (newQty > 0) {
            const itemTotal = price * newQty;
            itemTotalElement.textContent = itemTotal.toLocaleString('th-TH', {
              minimumFractionDigits: 2
            }) + ' บาท';

            // ✅ อัปเดตค่า hidden input ที่จะส่งไป checkout.php
            const productId = item.querySelector('.item-remove a').href.split('p_id=')[1];
            const hiddenInput = document.getElementById('qty_' + productId);
            if (hiddenInput) {
              hiddenInput.value = newQty;
            }

            updateCartTotal();
          }
        });
      });

      function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
          const input = item.querySelector('.qty-input');
          const qty = parseInt(input.value);
          const price = parseFloat(input.dataset.price);
          total += qty * price;
        });

        const totalElement = document.querySelector('.cart-footer span');
        totalElement.textContent = total.toLocaleString('th-TH', {
          minimumFractionDigits: 2
        }) + ' บาท';
      }
    });
  </script>

</body>

</html>