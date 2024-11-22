<?php
session_start();
include 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure the 'uploads' folder exists
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the folder if it doesn't exist
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_file = $upload_dir . basename($_FILES['image']['name']);
        
        // Check if the image file is uploaded successfully
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file; // Save the image path
        } else {
            echo "Error: Failed to upload the file.";
            exit;
        }
    }

    // Insert post data into the database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $content, $image_path);

    if ($stmt->execute()) {
        echo "Post submitted successfully. Awaiting approval.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Post</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create a New Post</h2>
    <form action="submit_post.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Submit Post</button>
    </form>
</div>

<script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
