<?php
include('../Admin/config/dbconnect.php');

if (isset($_POST['submit'])) {
    $id = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $category = $_POST['product_category'];
    $price = $_POST['product_price'];
    $stock = $_POST['product_stock'];
    $description = $_POST['description'];
    $popularity = isset($_POST['popularity']) ? 1 : 0;
    $productImage = '';

    // First, retrieve the current image path from the database
    $sql = "SELECT product_image FROM products WHERE product_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $currentImage = $stmt->fetchColumn();

    // Handle file upload if a new image is provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $uploadDir = 'image/';
        $fileName = basename($_FILES['product_image']['name']);
        $uploadFile = $uploadDir . $fileName;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
            // Delete the old image if a new one is uploaded
            if (!empty($currentImage) && file_exists('../' . $currentImage)) {
                unlink('../' . $currentImage);
            }
            $productImage = 'image/' . $fileName;
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        // Keep the existing image if no new image is uploaded
        $productImage = $currentImage;
    }

    // Update the product in the database
    // Update the product in the database
    $sql = "UPDATE products 
SET product_name = :name, 
    product_category = :category, 
    product_price = :price, 
    product_stock = :stock, 
    product_image = :image, 
    popularity = :popularity,
    description = :description
WHERE product_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $productName);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':image', $productImage);
    $stmt->bindParam(':popularity', $popularity, PDO::PARAM_INT);
    $stmt->bindParam(':description', $_POST['description']); // Bind the description
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);


    if ($stmt->execute()) {
        header("Location: products.php?update=success");
    } else {
        echo "Error updating product.";
    }
}
