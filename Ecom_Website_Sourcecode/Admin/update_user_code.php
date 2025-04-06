<?php
include('../Admin/config/dbconnect.php');

if (isset($_POST['update_user_btn'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_as = $_POST['role_as'];

    try {
        // Update user query
        if (!empty($password)) {
            // If password is not empty, hash it and include it in the update
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = :name, email = :email, password = :password, role_as = :role_as WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':password', $password);
        } else {
            // If password is empty, don't update the password
            $sql = "UPDATE users SET name = :name, email = :email, role_as = :role_as WHERE id = :id";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role_as', $role_as);

        if ($stmt->execute()) {
            echo "<script>
                    alert('User updated successfully!');
                    window.location.href = 'users.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to update user.');
                    window.location.href = 'edit_user.php?id=$user_id';
                  </script>";
        }
    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.location.href = 'edit_user.php?id=$user_id';
              </script>";
    }
}
?>
