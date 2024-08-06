<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/userClass.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Fetch users
$userStmt = $user->read();
$userCount = $userStmt->rowCount();

include 'header.php';
?>

<main class="container mt-5">
    <h2 class="text-center">Registered Users</h2>

    <?php if ($userCount > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $userStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            No users found.
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>