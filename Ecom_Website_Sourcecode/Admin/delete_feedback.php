<?php
include('../Admin/config/dbconnect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the delete statement
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the delete statement
    if ($stmt->execute()) {
        echo "<script>
                alert('Feedback deleted successfully.');
                window.location.href = 'admin_feedback.php';
              </script>";
    } else {
        echo "<script>
                alert('Error occurred while deleting feedback.');
                window.location.href = 'admin_feedback.php';
              </script>";
    }
} else {
    // If no ID is provided, redirect back to the feedback list
    header("Location: admin_feedback.php");
    exit();
}
?>
