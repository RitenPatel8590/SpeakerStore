<?php
// session_start();
include_once 'classes\dbClass.php';
include_once 'classes/productClass.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$stmt = $product->read();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eCommerce Home</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head> -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speaker's Group</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid white;

            background-color: #343a40;

        }

        .header-title {
            color: white;
            margin: 0;

        }
    </style>
</head>

<body>
    <header class="text-white p-3">
        <h1 class="text-center">Welcome to BoomBox Speakers</h1>
        <nav class="navbar navbar-expand-lg navbar-dark ">
            <div class="container">
                <a class="navbar-brand" href="index.php">Home</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>