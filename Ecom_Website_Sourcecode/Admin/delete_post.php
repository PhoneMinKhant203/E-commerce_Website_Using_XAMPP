<?php
include('../Admin/config/dbconnect.php'); // Include your database connection

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Check if the post ID exists in the database before attempting to delete
    $checkQuery = "SELECT COUNT(*) FROM posts WHERE post_id = :post_id"; // Check existence based on post_id
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':post_id', $post_id);
    $checkStmt->execute();
    
    if ($checkStmt->fetchColumn() > 0) {
        // Prepare and execute the delete query
        $query = "DELETE FROM posts WHERE post_id = :post_id"; // Use post_id in the query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':post_id', $post_id);

        if ($stmt->execute()) {
            // Redirect to the posts list page after successful deletion
            header("Location: admin_posting.php?delete=success");
            exit();
        } else {
            // Handle error if the delete operation fails
            echo "Error deleting the post.";
        }
    } else {
        // Notify if the post ID is not found
        echo "Post not found.";
    }
} else {
    // Redirect if no ID is provided
    header("Location: admin_posting.php");
    exit();
}
?>
