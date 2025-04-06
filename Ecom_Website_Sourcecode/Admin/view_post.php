<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Check if the post ID is provided via GET
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Fetch the post details from the database
    $sql = "SELECT * FROM posts WHERE post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the post is found
    if ($post):
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
                        <h4>View Post</h4>
                        <a href="admin_posting.php" class="btn btn-danger float-end">Back to Posts List</a>
                    </div>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($post['title']); ?></h5>
                        <p><?= htmlspecialchars($post['content']); ?></p>

                        <?php if (!empty($post['image'])): ?>
                            <div class="post-image">
                                <img src="../Admin/image/<?= htmlspecialchars($post['image']); ?>" alt="Post Image" class="img-fluid" style="max-width: 100%; height: auto;">
                            </div>
                        <?php else: ?>
                            <p>No image available.</p>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    else:
        // If no post found with the given ID
        echo "<p>Post not found.</p>";
    endif;
} else {
    // If the post ID is not provided, redirect to posts list
    header("Location: posts_list.php");
    exit();
}

include('includes/footer.php');
?>
