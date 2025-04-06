<?php
session_start();
include('Admin/config/dbconnect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to remove items from your wishlist.');
            window.location.href='signup.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if product_id is set in the POST request
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Prepare the SQL statement to delete the product from the wishlist
    $query = "DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

    // Execute the query and check if the deletion was successful
    if ($stmt->execute()) {
        echo "<script>
                alert('Product removed from your wishlist successfully.');
                window.location.href='wishlist.php'; // Redirect back to the wishlist
              </script>";
    } else {
        echo "<script>
                alert('Error removing product from wishlist. Please try again.');
                window.location.href='wishlist.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request.');
            window.location.href='wishlist.php';
          </script>";
}
?>
