<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/userClass.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if ($_POST) {
    $user->username = $_POST['username'];
    $user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user->email = $_POST['email'];

    if ($user->register()) {
        echo "<div class='alert alert-success'>Registration successful. Please <a href='login.php'>login</a>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Unable to register. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your external CSS -->
</head>

<body>
    <?php include 'header.php'; ?> <!-- Include header -->

    <main class="container mt-5">
        <h2 class="text-center">Register to Checkout</h2>
        <div class="form-container mt-4">
            <form action="checkout.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?> <!-- Include footer -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>