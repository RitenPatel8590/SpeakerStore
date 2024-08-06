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
    $product->product_name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->category_id = $_POST['category_id'];

    // Handle file upload
    if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] != '') {
        $image_name = basename($_FILES['image']['name']);
        $target_file = '../images/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $product->image_url = 'images/' . $image_name;
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