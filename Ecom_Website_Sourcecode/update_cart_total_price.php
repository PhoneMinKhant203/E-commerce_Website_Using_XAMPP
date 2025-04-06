<?php
session_start();
include('Admin/config/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the total price is set
if (isset($_POST['total_price'])) {
    $total_price = $_POST['total_price'];

    try {
        // Update the total price in the active cart
        $query = "
            UPDATE cart 
            SET total_price = :total_price 
            WHERE user_id = :user_id AND status = 'active'
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);  // Using string because total_price is decimal
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Total price updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cart not found or already updated']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Total price not set']);
}
?>
