<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/cartClass.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$user_id = $_SESSION['user_id'];

// Handle cart actions via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];

    switch ($action) {
        case 'increment':
            $cart->incrementQuantity($product_id, $user_id);
            break;
        case 'decrement':
            $cart->decrementQuantity($product_id, $user_id);
            break;
        case 'remove':
            $cart->removeItem($product_id, $user_id);
            break;
    }

    // Get updated cart content
    ob_start();
    include 'cartContent.php';
    $cartContent = ob_get_clean();
    echo json_encode(['html' => $cartContent]);
    exit();
}

$cart_items = $cart->getCartItems($user_id);
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - BoomBox Speakers</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        .cart-item {
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f8f9fa;
        }

        .quantity-control {
            width: 120px;
        }
    </style>
</head>

<body>
    <?php include 'header1.php'; ?>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Your Shopping Cart</h2>
        <div id="cart-content">
            <?php include 'cartContent.php'; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function () {
            // Attach click event handlers dynamically to the document
            $(document).on('click', '.quantity-control a, .btn-danger', function (e) {
                e.preventDefault();
                const action = $(this).data('action');
                const productId = $(this).data('product-id');

                $.ajax({
                    url: 'cart.php',
                    method: 'POST',
                    data: { action: action, product_id: productId },
                    dataType: 'json',
                    success: function (response) {
                        $('#cart-content').html(response.html);
                    }
                });
            });
        });
    </script>
</body>

</html>