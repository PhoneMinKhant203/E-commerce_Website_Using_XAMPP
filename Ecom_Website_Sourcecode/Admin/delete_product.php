<?php
// Include the database connection
include('../Admin/config/dbconnect.php');

if (isset($_GET['id'])) {
    // Get the product ID from the URL
    $id = $_GET['id'];

    // Get the product image path from the database so that we can delete the image file
    $sql = "SELECT product_image FROM products WHERE product_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $productImage = $stmt->fetchColumn();

    // Delete the product record from the database
    $deleteSql = "DELETE FROM products WHERE product_id = :id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Check if the deletion is successful
    if ($deleteStmt->execute()) {
        // If the product has an image, delete the image from the server
        if ($productImage && file_exists($productImage)) {
            unlink($productImage);
        }

        // Redirect back to the product table with a success message
        header("Location: products.php?delete=success");
        exit();
    } else {
        // Redirect back to the product table with an error message
        header("Location: products.php?delete=error");
        exit();
    }
} else {
    // If no ID is provided, redirect back to the product table
    header("Location: products.php");
    exit();
}
?>
