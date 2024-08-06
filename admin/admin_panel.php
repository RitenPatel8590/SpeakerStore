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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    // Validate form inputs
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category_id = trim($_POST['category_id']);

    // Check for empty fields
    if (empty($name) || empty($price) || empty($category_id)) {
        $message = "Please fill in all required fields.";
    } elseif (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
        $message = "Please enter a valid price.";
    } else {
        // Handle file upload
        $targetDir = "../images/";
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        $uploadOk = 1;

        // Check if file is a valid image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $message = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (max 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $message .= " Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $product->product_name = $name;
                $product->description = $description;
                $product->price = $price;
                $product->category_id = $category_id;
                $product->image_url = "images/" . basename($_FILES['image']['name']);

                if ($product->create()) {
                    $message = "Product added successfully!";
                } else {
                    $message = "Failed to add product.";
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Fetch products
$productStmt = $product->read();
$productCount = $productStmt->rowCount();

// Fetch categories for product insertion
$categoryStmt = $product->getCategories();

include 'header.php';
?>

<main class="container mt-5">
    <h2 class="text-center">Add New Product</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="admin_panel.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <?php while ($row = $categoryStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($row['category_id']); ?>">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
    </form>

    <h2 class="text-center mt-5">Products</h2>
    <?php if ($productCount > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $productStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td>
                            <img src="<?php echo "../" . htmlspecialchars($row['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($row['product_name']); ?>" class='img-thumbnail'>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?php echo htmlspecialchars($row['product_id']); ?>"
                                class="btn btn-warning btn-sm mb-1">Edit</a>
                            <a href="delete_product.php?id=<?php echo htmlspecialchars($row['product_id']); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            No products found.
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>