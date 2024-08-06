<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/productClass.php';

include 'header1.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$stmt = $product->getAllProducts();
$num = $stmt->rowCount();
?>

<main class="container mt-5">
    <h2 class="text-center mb-4">Discover Our Premium Speakers</h2>
    <div class="row">
        <?php
        if ($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card h-100 shadow-sm product-card'>";
                echo "<img src='./" . htmlspecialchars($image_url) . "' class='card-img-top product-image' alt='" . htmlspecialchars($product_name) . "'>";
                echo "<div class='card-body d-flex flex-column'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($product_name) . "</h5>";
                echo "<p class='card-text flex-grow-1'>" . htmlspecialchars($description) . "</p>";
                echo "<p class='card-text font-weight-bold'>$" . htmlspecialchars($price) . "</p>";

                echo "<form method='POST' action='add_to_cart.php' class='mt-auto'>";
                echo "<div class='form-group'>";
                echo "<label for='quantity{$product_id}'>Quantity:</label>";
                echo "<input type='number' id='quantity{$product_id}' name='quantity' class='form-control' value='1' min='1'>";
                echo "</div>";

                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>";

                echo "<div class='btn-group d-flex' role='group'>";
                echo "<button type='submit' class='btn btn-primary flex-grow-1 add-to-cart-btn'>Add to Cart</button>";
                echo "<a href='product_details.php?id=" . htmlspecialchars($product_id) . "' class='btn btn-outline-secondary flex-grow-1'>Details</a>";
                echo "</div>";
                echo "</form>";

                echo "</div></div></div>";
            }
        } else {
            echo "<div class='col-12'><p class='text-center alert alert-info'>No products available at the moment. Please check back later.</p></div>";
        }
        ?>
    </div>
</main>

<style>
    .product-card {
        transition: transform 0.3s ease-in-out;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-image {
        height: 200px;
        object-fit: cover;
    }
</style>

<?php
include 'footer.php';
?>