<?php
include('Admin/config/dbconnect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first!'); window.location.href='your_login_page.php';</script>"; // Redirect to login page
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

// Check if the message text is set and not empty
if (!isset($_POST['message_text']) || empty(trim($_POST['message_text']))) {
    $_SESSION['alert'] = 'Message text cannot be empty!';
    header('Location: message.php'); // Redirect to the form page
    exit;
}

$message_text = trim($_POST['message_text']);

try {
    $sql = "INSERT INTO messages (user_id, message_text, created_at) VALUES (:user_id, :message_text, NOW())";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $_SESSION['alert'] = 'Message sent successfully!';
    } else {
        $_SESSION['alert'] = 'Failed to send message.';
    }
} catch (PDOException $e) {
    $_SESSION['alert'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the form page
header('Location: message.php');
exit;


?>
