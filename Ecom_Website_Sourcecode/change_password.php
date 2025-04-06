<?php
session_start();
include('Admin/config/dbconnect.php'); // Include your database connection

// Fetch user ID from session (assuming the user is logged in)
$user_id = $_SESSION['user_id']; 

// Initialize an empty variable to hold the message
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input values
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database using PDO
    $query = "SELECT password FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $db_password = $user['password'];

        // Check if the entered current password matches the one in the database
        if ($current_password === $db_password) {
            // Check if the new password matches the confirm password
            if ($new_password === $confirm_password) {
                // Update the password in the database (no hashing for simplicity)
                $update_query = "UPDATE users SET password = :new_password WHERE id = :user_id";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->bindParam(':new_password', $new_password);
                $update_stmt->bindParam(':user_id', $user_id);
                
                if ($update_stmt->execute()) {
                    $message = "Password updated successfully.";
                    
                    // Display the message and redirect
                    echo "<script>
                            alert('$message');
                            window.location.href = 'account_detail.php';
                          </script>";
                    exit(); // Ensure no further code is executed
                } else {
                    $message = "Error updating password.";
                }
            } else {
                $message = "New password and confirm password do not match.";
            }
        } else {
            $message = "The current password you entered is incorrect.";
        }
    } else {
        $message = "User not found.";
    }

    // Output the error message using JavaScript alert
    echo "<script>alert('$message');</script>";
}
?>
