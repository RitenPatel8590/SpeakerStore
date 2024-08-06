<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/cartClass.php';

// Initialize database connection and Cart class
$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Get product ID and quantity from POST parameters
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?? 1;

if ($product_id && $quantity && $quantity > 0) {
    // Add product to cart
    if ($cart->addToCart($user_id, $product_id, $quantity)) {
        header("Location: cart.php");
        exit();
    } else {
        echo "Failed to add to cart. Please try again.";
    }
} else {
    echo "Invalid product ID or quantity.";
}
?>