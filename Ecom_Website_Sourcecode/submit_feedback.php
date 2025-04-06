<?php
// Database connection
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "alfredbrysonecom";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data and sanitize it
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $message = htmlspecialchars(trim($_POST['message']));

        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");

        // Bind the values
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        // Execute the statement and show the alert
        if ($stmt->execute()) {
            // Use JavaScript for success alert
            echo "<script>alert('Feedback submitted successfully!'); window.location.href='index.php';</script>";
        } else {
            // Use JavaScript for failure alert
            echo "<script>alert('Failed to submit feedback. Please try again.'); window.location.href='index.php';</script>";
        }
    }
} catch (Exception $e) {
    // Display error alert
    echo "<script>alert('Failed to connect to the database: " . $e->getMessage() . "'); window.location.href='index.php';</script>";
}
?>
