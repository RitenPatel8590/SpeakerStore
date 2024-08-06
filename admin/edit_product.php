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
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$product_id) {
    header("Location: admin_panel.php");
    exit();
}

$product->product_id = $product_id;

// Fetch product details
$product->readOne();
if (empty($product->product_name)) {
    header("Location: admin_panel.php");
    exit();
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    if (!isset($_POST['confirm'])) {
        // Display confirmation message
        include 'header.php';
        ?>
        <main class="container mt-5">
            <div class="alert alert-warning">
                Are you sure you want to update the product?
                <form method="POST" action="edit_product.php?id=<?php echo htmlspecialchars($product->product_id); ?>"
                    enctype="multipart/form-data">
                    <?php
                    foreach ($_POST as $key => $value) {
                        if ($key !== 'update_product') {
                            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                        }
                    }
                    if (!empty($_FILES['image']['name'])) {
                        echo '<input type="hidden" name="image_name" value="' . htmlspecialchars(basename($_FILES['image']['name'])) . '">';
                        echo '<input type="hidden" name="image_tmp_name" value="' . htmlspecialchars($_FILES['image']['tmp_name']) . '">';
                    }
                    ?>
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" name="update_product" class="btn btn-primary">Yes</button>
                    <a href="edit_product.php?id=<?php echo htmlspecialchars($product->product_id); ?>"
                        class="btn btn-secondary">No</a>
                </form>
            </div>
        </main>
        <?php
        include 'footer.php';
        exit();
    } else {
        // If confirmed, update product
        $product->product_name = $_POST['product_name'];
        $product->description = $_POST['description'];
        $product->price = $_POST['price'];
        $product->category_id = $_POST['category_id'];

        // Handle file upload
        if (!empty($_POST['image_tmp_name']) && !empty($_POST['image_name'])) {
            $target_file = '../images/' . $_POST['image_name'];
            if (move_uploaded_file($_POST['image_tmp_name'], $target_file)) {
                $product->image_url = 'images/' . $_POST['image_name'];
            } else {
                $message = "Failed to upload image.";
            }
        } else {
            // Use the existing image URL if no new image is uploaded
            $product->image_url = $_POST['existing_image_url'];
        }

        // Update product details
        if ($product->update()) {
            $message = "Product updated successfully!";
            header("Location: admin_panel.php");
            exit();
        } else {
            $message = "Failed to update product.";
        }
    }
}

$categoryStmt = $product->getCategories();

include 'header.php';
?>

<main class="container mt-5">
    <h2 class="text-center">Edit Product</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="edit_product.php?id=<?php echo htmlspecialchars($product->product_id); ?>"
        enctype="multipart/form-data">
        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" class="form-control"
                value="<?php echo htmlspecialchars($product->product_name); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description"
                class="form-control"><?php echo htmlspecialchars($product->description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01"
                value="<?php echo htmlspecialchars($product->price); ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <?php while ($row = $categoryStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['category_id']; ?>" <?php echo ($row['category_id'] == $product->category_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" class="form-control">
            <input type="hidden" name="existing_image_url" value="<?php echo htmlspecialchars($product->image_url); ?>">
        </div>
        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
    </form>
</main>

<?php include 'footer.php'; ?>