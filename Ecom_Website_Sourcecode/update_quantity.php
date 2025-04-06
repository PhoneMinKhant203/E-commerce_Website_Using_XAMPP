<?php
session_start();
include('Admin/config/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_item_id = $_POST['cart_item_id'];
    $new_quantity = $_POST['quantity'];

    // Ensure the quantity is at least 1
    if ($new_quantity < 1) {
        $new_quantity = 1;
    }

    // Update the quantity in the database
    $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :cart_item_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
    $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
}
?>
