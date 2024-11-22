<?php
session_start();
include 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch approved posts
$result = $conn->query("SELECT posts.title, posts.content, posts.image_path, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.status='approved'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to the Dashboard</h2>
        <a href="submit_post.php" class="btn btn-primary">Submit a New Post</a>
        <hr>
        <h3>Approved Posts</h3>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <?php if (!empty($row['image_path'])) { ?>
                            <img src="uploads/<?php echo $row['image_path']; ?>" class="card-img-top" alt="Post Image">
                        <?php } ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                            <p class="card-text"><?php echo $row['content']; ?></p>
                            <p><small>By <?php echo $row['username']; ?></small></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
