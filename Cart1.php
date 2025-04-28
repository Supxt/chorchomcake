<?php
session_start();
include('./components/navbar.php');
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chorchomcake</title>

</head>

<body>
    <?php
    include('cart_view.php');
    ?>
</body>

</html>