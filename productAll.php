 <?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");

  include_once "dbconnect.php";
  include_once "productcard.php";

  $database = new Database();
  $db = $database->getConnection();

  $product = new Product($db);
  $stmt = $product->getAll();
  $num = $stmt->rowCount();

  if ($num > 0) {
    $products_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $product_item = array(
        "id" => $p_id,
        "name" => $p_name
      );
      array_push($products_arr, $product_item);
    }
    echo json_encode($products_arr);
  } else {
    echo json_encode(
      array("message" => "No products found.")
    );
  }
