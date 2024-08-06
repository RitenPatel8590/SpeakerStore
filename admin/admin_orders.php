<?php
session_start();
include_once '../classes/dbclass.php';
include_once '../classes/orderClass.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

// Fetch orders
$orderStmt = $order->read();
$orderCount = $orderStmt->rowCount();

include 'header.php';
?>

<main class="container mt-5">
    <h2 class="text-center">Orders</h2>

    <?php if ($orderCount > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php while ($row = $orderStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            No orders found.
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>