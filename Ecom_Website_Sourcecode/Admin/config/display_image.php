<?php
include('dbconnect.php'); // Include your database connection

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        // Prepare the SQL query to retrieve the image blob and its mime type
        $query = "SELECT product_image, mime_type FROM products WHERE product_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Set the content type to the mime type from the database
            header("Content-Type: " . $row['mime_type']);
            echo $row['product_image']; // Output the binary image data
        } else {
            echo "Image not found.";
        }
    } catch (Exception $e) {
        echo "Failed to retrieve image: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
