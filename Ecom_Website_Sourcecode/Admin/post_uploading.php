<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../Admin/config/dbconnect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if post_title, post_content, and post_image are set
    if (isset($_POST['post_title'], $_POST['post_content'], $_FILES['post_image'])) {
        $post_title = $_POST['post_title'];
        $post_content = $_POST['post_content'];
        $post_image = $_FILES['post_image'];

        // Handle image upload with absolute path to "image" directory
        $target_dir = "D:/xampp/htdocs/PhoneMinKhant_Ecom_Website/Admin/image/";
        $target_file = $target_dir . basename($post_image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image and file size (limit 2MB)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types) && $post_image["size"] <= 2000000) {
            if (move_uploaded_file($post_image["tmp_name"], $target_file)) {
                // Insert post data into the database, setting created_at and updated_at timestamps
                try {
                    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$post_title, $post_content, basename($post_image["name"])]);

                    // Success message and redirection
                    echo "<script>alert('The post has been uploaded successfully.'); window.location.href = 'admin_post.php';</script>";
                    exit;
                } catch (Exception $e) {
                    echo "<script>alert('Error saving post to database: " . addslashes($e->getMessage()) . "');</script>";
                }
            } else {
                echo "<script>alert('There was an error uploading the file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type or file too large. Only JPG, JPEG, PNG & GIF files under 2MB are allowed.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all fields and upload an image.');</script>";
    }
}
?>
