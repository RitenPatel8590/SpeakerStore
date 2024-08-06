<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/favicon.png" type="image/x-icon">
    <style>
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid white;
            /* Underline for the active page */
            background-color: #343a40;
            /* Highlight background for the active page */
        }

        .header-title {
            color: white;
            margin: 0;
            /* Remove default margin */
        }
    </style>
</head>

<body>
    <?php
    // Determine which page is active
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <header class="bg-dark text-white p-3">
        <div class="container d-flex align-items-center">
            <!-- Logo and Title -->
            <a href="admin_panel.php" class="d-flex align-items-center">
                <img src="../images/CompanyLogo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
                <h1 class="header-title">Admin Panel</h1>
            </a>
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mt-3">
            <div class="container">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'admin_panel.php') ? 'active' : ''; ?>"
                            href="admin_panel.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'admin_orders.php') ? 'active' : ''; ?>"
                            href="admin_orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'admin_users.php') ? 'active' : ''; ?>"
                            href="admin_users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</body>

</html>