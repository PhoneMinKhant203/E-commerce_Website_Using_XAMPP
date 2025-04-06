<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch all feedback from the database
$sql = "SELECT * FROM feedback ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h4>Feedback List</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($feedbacks)): ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Date Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($feedbacks as $feedback): ?>
                                    <tr>
                                        <td><?= $feedback['id'] ?></td>
                                        <td><?= $feedback['name'] ?></td>
                                        <td><?= $feedback['email'] ?></td>
                                        <td style="max-width: 300px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; white-space: normal;"><?= $feedback['message'] ?></td>
                                        <td><?= $feedback['created_at'] ?></td>
                                        <td>
                                            <a href="delete_feedback.php?id=<?= $feedback['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p>No feedback found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
