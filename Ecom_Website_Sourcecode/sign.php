<?php
include("insertuser.php");

// Start output buffering
ob_start();

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password']; // Use plain text password

try {
    // Check if the username or email already exists
    $check_sql = "SELECT * FROM `users` WHERE `name` = :name OR `email` = :email";
    $stmt = $connect->prepare($check_sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // JavaScript alert for duplicate user and redirect to signup
        echo "<script>
                alert('Error: Username or Email already exists. Please try again.');
                window.location.href = 'signup.php';
              </script>";
        exit; 
    } else {
        // Insert new user into database
        $sql = "INSERT INTO `users`(`name`, `email`, `password`) VALUES (:name, :email, :password)";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password); // Store plain text password
        $stmt->execute();
        
        // Success message and redirect to sign-in page
        echo "<script>
                alert('Account Registration Successful! Welcome, " . htmlspecialchars($name) . "! Please sign in to your account.');
                window.location.href = 'signup.php'; // Redirect to sign-in page after successful registration
              </script>";
        exit; 
    }
} catch (Exception $e) {
    // Handle any errors and show an alert message
    echo "<script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = 'signup.php'; // Redirect back to signup on error
          </script>";
    exit; 
}

ob_end_flush();
?>
