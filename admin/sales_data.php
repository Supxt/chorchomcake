<?php
include_once('../dbconnect.php');

$range = $_GET['range'] ?? 'day';
$category = $_GET['category'] ?? 'ทั้งหมด';

$endDate = date('Y-m-d');
if ($range === 'day') {
  $startDate = $endDate;
} elseif ($range === 'week') {
  $startDate = date('Y-m-d', strtotime('-6 days'));
} elseif ($range === 'year') {
  $startDate = date('Y-01-01');
} else {
  $startDate = '2025-01-01'; // fallback default
}

// Base query
$sql = "SELECT DATE(o.created_at) AS sale_date, SUM(od.total) AS daily_sales
        FROM order_details od
        JOIN orders o ON od.order_id = o.order_id
        JOIN product p ON od.p_id = p.p_id
        WHERE o.created_at >= '$startDate 00:00:00' AND o.created_at <= '$endDate 23:59:59'";


// Optional filter by category
if ($category !== 'ทั้งหมด') {
  $sql .= " AND p.category_id = (
              SELECT category_id FROM category WHERE category_name = '$category' LIMIT 1
            )";
}

$sql .= " GROUP BY DATE(o.created_at)
          ORDER BY sale_date ASC";


$result = mysqli_query($conn, $sql);

$testSql = "
  SELECT 
    od.o_id,
    od.order_id,
    o.order_no,
    o.created_at,
    od.p_id,
    p.p_name AS product_name,
    p.category_id,
    od.product_code,
    od.o_qty,
    od.product_price,
    od.total,
    p.image
  FROM 
    order_details od
  JOIN 
    orders o ON od.order_id = o.order_id
  JOIN 
    product p ON od.p_id = p.p_id
  ORDER BY 
    o.created_at DESC
";
$testResult = mysqli_query($conn, $testSql);

$testData = [];
while ($row = mysqli_fetch_assoc($testResult)) {
  $testData[] = $row;
}


$logParams = [
    'startDate' => $startDate,
    'endDate' => $endDate,
    'category' => $category,
    'range' => $range,
    'query' => $sql
  ];
  
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

header('Content-Type: application/json');

$response = [
    'data' => $data,
    'log' => $logParams,
    'test' => $testData
  ];
  
  echo json_encode($response);
