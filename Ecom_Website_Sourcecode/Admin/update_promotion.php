<?php
include('../Admin/config/dbconnect.php');

if (isset($_POST['update_promotion'])) {
    $promotion_id = $_POST['promotion_id'];
    $promotion_percentage = $_POST['promotion_percentage'];
    $promotion_price = $_POST['promotion_price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Update the promotion
    try {
        $query = "UPDATE promotions 
                  SET promotion_percentage = :promotion_percentage, promotion_price = :promotion_price, start_date = :start_date, end_date = :end_date 
                  WHERE promotion_id = :promotion_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':promotion_percentage', $promotion_percentage);
        $stmt->bindParam(':promotion_price', $promotion_price);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: promotion.php?update=success");
        exit();
    } catch (Exception $e) {
        echo "Error updating promotion: " . $e->getMessage();
        exit();
    }
}
?>
