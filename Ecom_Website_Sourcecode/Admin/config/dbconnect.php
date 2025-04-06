
<?php
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "alfredbrysonecom";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // Corrected variable name
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}
?>
