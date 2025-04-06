<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch all users from the database
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <h4>User List</h4>
                        <a href="add_users.php" class="btn btn-dark float-end">Add User</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($users)): ?>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td><?= $user['name'] ?></td>
                                                <td><?= $user['email'] ?></td>
                                                <td><?= $user['role_as'] == 1 ? 'Admin' : 'User' ?></td>
                                                <td>
                                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-dark">Edit</a>
                                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>


<?php include('includes/footer.php'); ?>