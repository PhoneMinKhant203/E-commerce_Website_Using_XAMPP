<?php
include('Admin/config/dbconnect.php');

if (isset($_POST['cart_item_id']) && isset($_POST['quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];
    $quantity = $_POST['quantity'];

    // Get the product stock for the current cart item
    $query = "
        SELECT products.product_stock 
        FROM cart_items 
        JOIN products ON cart_items.product_id = products.product_id 
        WHERE cart_items.id = :cart_item_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stock = $result['product_stock'];

        if ($quantity <= $stock) {
            echo json_encode(['success' => true, 'stock' => $stock]);
        } else {
            echo json_encode(['success' => false, 'stock' => $stock]);
        }
    } else {
        echo json_encode(['success' => false, 'stock' => 0]);
    }
}
?>
