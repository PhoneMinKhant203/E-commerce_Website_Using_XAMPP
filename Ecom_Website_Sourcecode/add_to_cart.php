<?php
session_start();
include('Admin/config/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo "<script>
                alert('Please log in first.');
                window.location.href = 'signup.php';
          </script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Get the quantity from the form, default is 1

// Check if the cart exists for the user
$stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    // If no active cart exists, create one
    $stmt = $pdo->prepare("INSERT INTO cart (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['id'];
}

// Check if there is an active promotion for this product
$stmt = $pdo->prepare("
    SELECT promotion_price 
    FROM promotions 
    WHERE product_id = ? 
    AND start_date <= NOW() 
    AND end_date >= NOW()
    LIMIT 1
");
$stmt->execute([$product_id]);
$promotion = $stmt->fetch(PDO::FETCH_ASSOC);

if ($promotion) {
    // If a promotion exists, use the promotion price
    $price_at_time = $promotion['promotion_price'];
} else {
    // Otherwise, use the original price from the product table
    $stmt = $pdo->prepare("SELECT product_price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $price_at_time = $product['product_price'];
}

// Check if the product already exists in the cart
$stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$cart_id, $product_id]);
$cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart_item) {
    // If the product is already in the cart, update the quantity
    $new_quantity = $cart_item['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $cart_item['id']]);
} else {
    // Otherwise, insert a new item into the cart with the current price
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity, $price_at_time]);
}

// Redirect the user back to the previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
