<?php
session_start();

// Check if logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include the database connection
include 'config.php';

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Fetch posts that are pending approval
$sql = "SELECT * FROM posts WHERE STATUS = 'pending' ORDER BY created_at DESC";
$result = $conn->query($sql);

// Approve or Reject Post
if (isset($_GET['action']) && isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $update_sql = "UPDATE posts SET STATUS = 'approved' WHERE id = ?";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE posts SET STATUS = 'rejected' WHERE id = ?";
    }
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

// Delete Post
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $delete_sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>

    <!-- Logout Button -->
    <a href="?logout=true" class="btn btn-danger mb-3">Logout</a>

    <!-- Table to Display Pending Posts -->
    <h3>Pending Posts</h3>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <?php echo isset($row['STATUS']) ? ucfirst($row['STATUS']) : 'N/A'; ?>
                        </td>
                        <td>
                            <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success">Approve</a>
                            <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-warning">Reject</a>
                            <a href="?delete=true&id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending posts to review.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
