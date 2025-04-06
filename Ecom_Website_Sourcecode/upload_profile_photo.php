<?php
session_start();
include('Admin/config/dbconnect.php'); // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

function uploadProfilePhoto($userId)
{
    global $pdo;
    $message = "";

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileTmp = $_FILES['profile_photo']['tmp_name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed) && $fileSize <= 2097152) {
            $profileData = file_get_contents($fileTmp);
            $query = "UPDATE users SET profile = ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute([$profileData, $userId])) {
                return "Profile photo updated successfully!";
            } else {
                return "Failed to update profile photo in the database.";
            }
        } else {
            return "Invalid file type or size. Please upload a JPG, JPEG, PNG, or GIF file (max 2MB).";
        }
    } else {
        return "No file uploaded or there was an error during the upload.";
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = uploadProfilePhoto($userId);

    // Prepare JavaScript for alert and redirection
    echo "<script type='text/javascript'>
            alert('" . addslashes($message) . "');";
    if (strpos($message, 'successfully') !== false) {
        echo "setTimeout(function() {
                window.location.href = 'account_detail.php';
            }, 0);";
    }
    echo "</script>";
}
