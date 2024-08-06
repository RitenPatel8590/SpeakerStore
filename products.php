<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/productClass.php';
include 'header1.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// Handle filters
$filters = [];
if (isset($_GET['category']) && $_GET['category'] != '') {
    $filters['category_id'] = $_GET['category'];
}
if (isset($_GET['min_price']) && $_GET['min_price'] != '') {
    $filters['price >= '] = $_GET['min_price'];
}
if (isset($_GET['max_price']) && $_GET['max_price'] != '') {
    $filters['price <= '] = $_GET['max_price'];
}

$stmt = $product->read($filters);
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/products.css">
</head>

<body>
    <main class="container mt-5">
        <h2 class="text-center">All Products</h2>
        <form action="products.php" method="get" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php
                        $categories = $product->getCategories();
                        while ($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="min_price" placeholder="Min Price" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="number" name="max_price" placeholder="Max Price" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </form>
        <div class="row">
            <?php
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card product-item'>";
                    echo "<img src='./" . htmlspecialchars($image_url) . "' class='card-img-top' alt='" . htmlspecialchars($product_name) . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($product_name) . "</h5>";
                    echo "<p class='card-text'>" . htmlspecialchars($description) . "</p>";
                    echo "<p class='card-text'>Price: $" . htmlspecialchars($price) . "</p>";

                    // Quantity input field
                    echo "<form method='POST' action='add_to_cart.php'>";
                    echo "<div class='form-group'>";
                    echo "<label for='quantity{$product_id}'>Quantity:</label>";
                    echo "<input type='number' id='quantity{$product_id}' name='quantity' class='form-control' value='1' min='1'>";
                    echo "</div>";

                    // Hidden field to pass product ID
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>";

                    // Centered buttons
                    echo "<div class='text-center'>";
                    echo "<button type='submit' class='btn btn-primary'>Add to Cart</button>";
                    echo "<a href='product_details.php?id=" . htmlspecialchars($product_id) . "' class='btn btn-secondary ml-2'>More Details</a>";
                    echo "</div>";
                    echo "</form>";

                    echo "</div></div></div>";
                }
            } else {
                echo "<p class='text-center'>No products found.</p>";
            }
            ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/products.js"></script>

    <?php
    // Include footer from the user directory
    include 'user/footer.php';
    ?>
</body>

</html>