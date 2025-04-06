<?php
session_start();
include('Admin/config/dbconnect.php'); // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You are not logged in!'); window.location.href = 'login.php';</script>";
    exit;
}

$userId = $_SESSION['user_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values from the form
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Basic validation (optional)
    if (empty($name) || empty($email) || empty($phone)) {
        echo "<script>alert('All fields are required!'); window.location.href = 'account_detail.php';</script>";
    } else {
        // Update the user's profile data in the database
        $query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([$name, $email, $phone, $userId]);

        // Check if the update was successful
        if ($success) {
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'account_detail.php';</script>";
        } else {
            echo "<script>alert('Failed to update profile. Please try again.'); window.location.href = 'account_detail.php';</script>";
        }
    }
}
?>
