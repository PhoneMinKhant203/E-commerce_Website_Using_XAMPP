<?php include('includes/header.php'); ?>

<div class="main-container d-flex">
  <aside class="sidebar">
    <?php include('includes/sidebar.php'); ?>
  </aside>

  <div class="content-container container">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Add User</h4>
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </div>
                <div class="card-body">
                    <form action="add_user_code.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Name</label>
                            <input type="text" name="name" placeholder="Enter User Name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Email</label>
                            <input type="email" name="email" placeholder="Enter User Email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Password</label>
                            <input type="password" name="password" placeholder="Enter User Password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Role</label>
                            <select name="role_as" class="form-control">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-dark" name="add_user_btn">Save</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include('includes/footer.php'); ?>
