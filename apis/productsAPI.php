<?php
header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'classes/productClass.php';

$product = new Product($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = $product->getAllProducts();
    echo json_encode($products);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle creating a new product
}