<?php
session_start();
include('Admin/config/dbconnect.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: signup.php");
        exit();
    }

    // Get form data
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message_text = $_POST['message_text'];

    // Insert message into messages table
    $stmt = $pdo->prepare("INSERT INTO messages (message_text, created_at, user_id) VALUES (?, NOW(), ?)");
    $stmt->execute([$message_text, $user_id]);

    // Redirect to a thank you page or back to contact page
    header("Location: contact.php?status=success");
    exit();
}
?>
