<?php
session_start();
include('Admin/config/dbconnect.php');

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query to count the total number of items in the cart
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart_items ci 
                           JOIN cart c ON ci.cart_id = c.id 
                           WHERE c.user_id = ? AND c.status = 'active'");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the count of items in the cart
    if ($result) {
        $cart_count = (int)$result['total_items'];
    }
}

echo $cart_count; // Return the cart count
?>
