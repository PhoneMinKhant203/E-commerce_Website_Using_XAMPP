<?php
include('../Admin/config/dbconnect.php');

if (isset($_GET['id'])) {
    $promotion_id = $_GET['id'];

    // Validate that the ID is numeric
    if (!is_numeric($promotion_id)) {
        header("Location: promotion.php?delete=invalid_id");
        exit();
    }

    try {
        // Prepare the SQL statement to delete the promotion
        $query = "DELETE FROM promotions WHERE promotion_id = :promotion_id";
        $stmt = $pdo->prepare($query);

        // Bind the ID parameter
        $stmt->bindParam(':promotion_id', $promotion_id);

        // Execute the deletion
        if ($stmt->execute()) {
            header("Location: promotion.php?delete=success");
            exit();
        } else {
            header("Location: promotion.php?delete=error");
            exit();
        }
    } catch (Exception $e) {
        // Log error message (optional)
        error_log("Error deleting promotion: " . $e->getMessage());
        header("Location: promotion.php?delete=error");
        exit();
    }
} else {
    // If no ID is provided, redirect back to promotions page
    header("Location: promotion.php?delete=no_id");
    exit();
}
?>
