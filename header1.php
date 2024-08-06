<?php
include_once 'classes/dbclass.php';
include_once 'classes/productClass.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$stmt = $product->read();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/favicon.png" type="image/x-icon">
    <title>BoomBox Speakers - Premium Audio Experience</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            font-size: 1.1rem;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('images/CompanyLogo.png') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
        }
    </style>
    <link rel="stylesheet" href="css/animated.css">

</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="./images/CompanyLogo.png" alt="Company-Logo" style="height: 50px; margin-right: 15px;">
                    BoomBox Speakers


                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="cart.php"><i
                                    class="fas fa-shopping-cart mr-1"></i>Cart</a></li>
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

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4">Experience Premium Sound</h1>
            <p class="lead">Discover the perfect balance of clarity, power, and design with BoomBox Speakers.</p>
            <a href="products.php" class="btn btn-primary btn-lg mt-3">Shop Now</a>
        </div>
    </section>