<?php
session_start();
include_once '../classes/dbclass.php';
include_once '../classes/productClass.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// Check if product ID is set in the query string
if (!isset($_GET['id'])) {
    header("Location: admin_panel.php");
    exit();
}

$product->product_id = $_GET['id'];

if ($product->delete()) {
    $message = "Product deleted successfully.";
} else {
    $message = "Failed to delete product.";
}

echo "<script>
        alert('$message');
        window.location.href = 'admin_panel.php';
      </script>";
exit();
?>