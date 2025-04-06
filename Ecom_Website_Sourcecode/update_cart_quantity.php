<?php
session_start();
include('Admin/config/dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit('User not logged in');
}

if (isset($_POST['cart_item_id'], $_POST['quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];
    $quantity = $_POST['quantity'];

    // Update the quantity for the cart item
    $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :cart_item_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo 'Success';
    } else {
        echo 'Error';
    }
} else {
    echo 'Invalid request';
}
?>
