<?php
include('../Admin/config/dbconnect.php');

if (isset($_POST['add_user_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role_as = $_POST['role_as'];

    try {
        $sql = "INSERT INTO users (name, email, password, role_as) VALUES (:name, :email, :password, :role_as)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role_as', $role_as);

        if ($stmt->execute()) {
            echo "<script>
                    alert('User added successfully!');
                    window.location.href = 'users.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to add user.');
                    window.location.href = 'add_user.php';
                  </script>";
        }
    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.location.href = 'add_user.php';
              </script>";
    }
}
?>
