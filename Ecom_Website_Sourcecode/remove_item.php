<?php
session_start();
include('Admin/config/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the cart_item_id from the POST request
    $cart_item_id = $_POST['cart_item_id'];

    // SQL query to remove the cart item
    $query = "DELETE FROM cart_items WHERE id = :cart_item_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect back to the cart page after successful deletion
        header("Location: cart.php");
        exit();
    } else {
        echo "Failed to remove item from the cart.";
    }
}
