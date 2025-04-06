<?php 
session_start();
include('../Admin/config/dbconnect.php');

try {
    if (isset($_FILES['image'])) {
        $target = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $targetDir = "image/" . $target;
        
        if (!file_exists('image/')) {
            mkdir('image/', 0777, true);
        }
        
        if (!move_uploaded_file($tmp, $targetDir)) {
            throw new Exception('Failed to move the uploaded file.');
        }
    }
    
    if (isset($_POST['add_products_btn'])) {
        $product_name = $_POST['product_name'];
        $product_category = $_POST['product_category'];
        $price = $_POST['product_price'];
        $stock = $_POST['product_stock'];
        $popularity = isset($_POST['popularity']) ? '1' : '0'; 

        $sql = "INSERT INTO products (product_name, product_category, product_price, product_stock, popularity, product_image) 
                VALUES (:product_name, :product_category, :price, :stock, :popularity, :product_img)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':product_category', $product_category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':popularity', $popularity);
        $stmt->bindParam(':product_img', $targetDir);

        if ($stmt->execute()) {
            // Display an alert box for success and redirect to add_products.php
            echo "<script>
                    alert('Product added successfully!');
                    window.location.href = 'add_products.php';
                  </script>";
        } else {
            // Display an alert box for failure and redirect to add_products.php
            echo "<script>
                    alert('Failed to add product.');
                    window.location.href = 'add_products.php';
                  </script>";
        } 
        
    }
} catch (Exception $e) {
    // Display an alert box for the error and redirect to add_products.php
    echo "<script>
            alert('Cannot insert data: " . addslashes($e->getMessage()) . "');
            window.location.href = 'add_products.php';
          </script>";
}

?>
