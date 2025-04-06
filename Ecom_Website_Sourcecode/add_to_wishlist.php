<?php
session_start();
include('Admin/config/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You need to log in to add items to your wishlist.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get product_id from the request body
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;

if ($product_id === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product.'
    ]);
    exit;
}

// Check if the product is already in the wishlist
$stmt = $pdo->prepare("SELECT * FROM wishlists WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$wishlist_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($wishlist_item) {
    echo json_encode([
        'status' => 'info',
        'message' => 'Product is already in your wishlist.'
    ]);
    exit;
}

// Insert product into the wishlist
$stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id, created_at) VALUES (?, ?, NOW())");
$success = $stmt->execute([$user_id, $product_id]);

if ($success) {
    // Optionally, return the updated wishlist count
    $stmt = $pdo->prepare("SELECT COUNT(*) AS wishlist_count FROM wishlists WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wishlist_count = (int) $stmt->fetch(PDO::FETCH_ASSOC)['wishlist_count'];

    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to wishlist!',
        'wishlist_count' => $wishlist_count
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to wishlist.'
    ]);
}


?>
