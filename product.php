<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="animated.css">
</head>

<body>
    <?php include 'header1.php'; ?>

    <?php
    // Check if product ID is set in the query string and is valid
    $product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$product_id) {
        header("Location: index.php");
        exit();
    }

    // Set product ID and fetch product details
    $product->product_id = $product_id;

    $product->readOne();

    // Check if the product exists
    if (!$product->product_name) {
        header("Location: index.php");
        exit();
    }
    ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="./<?php echo htmlspecialchars($product->image_url); ?>" class="img-fluid product-image"
                    alt="<?php echo htmlspecialchars($product->product_name); ?>">
            </div>
            <div class="col-md-6">
                <h3 class="product-title"><?php echo htmlspecialchars($product->product_name); ?></h3>
                <p class="product-description"><?php echo htmlspecialchars($product->description); ?></p>
                <p class="product-price font-weight-bold">Price: $<?php echo htmlspecialchars($product->price); ?></p>

                <!-- Quantity input field -->
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1">
                </div>

                <!-- Buttons -->
                <div class="text-center">
                    <a href="add_to_cart.php?id=<?php echo htmlspecialchars($product->product_id); ?>&quantity="
                        class="btn btn-primary add-to-cart-btn">Add to Cart</a>
                    <a href="index.php" class="btn btn-secondary back-btn ml-2">Back to Products</a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>

</html>