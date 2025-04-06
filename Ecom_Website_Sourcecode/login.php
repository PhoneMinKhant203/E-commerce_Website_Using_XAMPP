<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alfredbrysonecom";

try {
    $connect = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Failed to connect: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $connect->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if password matches
        if ($password === $user['password']) {
            $_SESSION['loggedin'] = true; 
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['role_as'] = $user['role_as']; // Save role in session

            // Redirect based on role
            if ($user['role_as'] == 1) { // Admin
                echo "<script>
                        alert('Login successful. Redirecting to admin dashboard...');
                        window.location.href = 'Admin/index.php';
                      </script>";
            } else { // Customer
                echo "<script>
                        alert('Login successful. Redirecting to shop...');
                        window.location.href = 'shop.php';
                      </script>";
            }
            exit;
        } else {
            // Incorrect password
            echo "<script>
                    alert('Incorrect password. Please try again.');
                    window.location.href = 'signup.php'; 
                  </script>";
        }
    } else {
        // No account found
        echo "<script>
                alert('No account found with that email. Please sign up.');
                window.location.href = 'signup.php'; 
              </script>";
    }
}
?>
