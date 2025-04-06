<?php 
include('includes/header.php'); 
include('../Admin/config/dbconnect.php');

// Check if the ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details from the database
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('User not found!'); window.location.href = 'users.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('No user ID provided!'); window.location.href = 'users.php';</script>";
    exit;
}
?>

<div class="main-container d-flex">
  <aside class="sidebar">
    <?php include('includes/sidebar.php'); ?>
  </aside>

  <div class="content-container container">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User</h4>
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </div>
                <div class="card-body">
                    <form action="update_user_code.php" method="POST" onsubmit="return confirmUpdate();">
                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Name</label>
                            <input type="text" name="name" value="<?= $user['name']; ?>" placeholder="Enter User Name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Email</label>
                            <input type="email" name="email" value="<?= $user['email']; ?>" placeholder="Enter User Email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">New Password (leave blank if not changing)</label>
                            <input type="password" name="password" placeholder="Enter New Password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="">Role</label>
                            <select name="role_as" class="form-control">
                                <option value="0" <?= $user['role_as'] == '0' ? 'selected' : '' ?>>User</option>
                                <option value="1" <?= $user['role_as'] == '1' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-dark" name="update_user_btn">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Include confirmation script -->
<script>
function confirmUpdate() {
    return confirm("Are you sure you want to update this user?");
}
</script>

<?php include('includes/footer.php'); ?>
