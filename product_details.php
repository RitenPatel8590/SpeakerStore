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
    <link rel="stylesheet" href="styles.css">
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
                <form method='POST' action='add_to_cart.php' class='mt-auto'>
                    <input type='hidden' name='product_id' value="<?php echo htmlspecialchars($product->product_id) ?>">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1">
                    </div>

                    <!-- Buttons -->
                    <div class="text-center">
                        <button class="btn btn-primary add-to-cart-btn" type='submit'
                            data-product-id="<?php echo htmlspecialchars($product->product_id); ?>">Add to Cart</button>
                        <a href="index.php" class="btn btn-secondary back-btn ml-2">Back to Products</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts.js"></script>
</body>

</html>