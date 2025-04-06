<?php
include('../Admin/config/dbconnect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('User deleted successfully!');
                    window.location.href = 'users.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to delete user.');
                    window.location.href = 'users.php';
                  </script>";
        }
    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.location.href = 'users.php';
              </script>";
    }
}
?>
