<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch all posts from the database
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <h4>Posts List</h4>
                        <a href="admin_post.php" class="btn btn-dark float-end">Upload Post</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($posts)): ?>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Content</th>
                                            <th>Image</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $post): ?>
                                            <tr>
                                                <td><?= $post['post_id'] ?></td>
                                                <td><?= htmlspecialchars($post['title']) ?></td>
                                                <td style="max-width: 300px;">
                                                    <div style="max-height: 200px; overflow-y: auto; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; white-space: normal;">
                                                        <?= htmlspecialchars($post['content']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <img src="../Admin/image/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" width="100">
                                                </td>
                                                <td><?= $post['created_at'] ?></td>
                                                <td>
                                                    <a href="view_post.php?id=<?= $post['post_id'] ?>" class="btn btn-dark">View</a>
                                                    <a href="edit_post.php?id=<?= $post['post_id'] ?>" class="btn btn-dark">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                            <p>No posts found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>