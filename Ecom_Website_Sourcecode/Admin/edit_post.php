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

        // Handle form submission for updating the post
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_title = $_POST['post_title'];
            $post_content = $_POST['post_content'];
            $post_image = $_FILES['post_image'];

            // Check if the admin uploaded a new image
            if (!empty($post_image['name'])) {
                $target_dir = "../Admin/image/";
                $target_file = $target_dir . basename($post_image["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $uploadOk = 1;

                // Check if the uploaded file is an image
                $check = getimagesize($post_image["tmp_name"]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "<script>alert('File is not an image.');</script>";
                    $uploadOk = 0;
                }

                // Limit file types to JPEG, PNG, and GIF
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if ($uploadOk && !in_array($imageFileType, $allowed_types)) {
                    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                    $uploadOk = 0;
                }

                // Check file size (limit to 2MB)
                if ($uploadOk && $post_image["size"] > 2000000) {
                    echo "<script>alert('Sorry, your file is too large. Maximum file size is 2MB.');</script>";
                    $uploadOk = 0;
                }

                // If image upload checks pass, upload the file
                if ($uploadOk && move_uploaded_file($post_image["tmp_name"], $target_file)) {
                    $image = basename($post_image["name"]); // New image file name
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your image.');</script>";
                }
            } else {
                // If no new image is uploaded, keep the existing image
                $image = $post['image'];
            }

            // Update the post in the database
            $sql_update = "UPDATE posts SET title = ?, content = ?, image = ?, updated_at = NOW() WHERE post_id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$post_title, $post_content, $image, $post_id]);

            // Success message and redirect
            echo "<script>alert('Post updated successfully.'); window.location.href = 'view_post.php?id=$post_id';</script>";
            exit();
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
                                <h4>Edit Post</h4>
                                <a href="javascript:void(0);"
                                    class="btn btn-danger mt-3"
                                    style="margin-left: 1090px;"
                                    onclick="confirmDelete(<?= $post_id ?>)">Delete</a>

                            </div>
                            <div class="card-body">
                                <form action="edit_post.php?id=<?= $post_id ?>" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="post_title">Title:</label>
                                        <input type="text" name="post_title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="post_content">Content:</label>
                                        <textarea name="post_content" class="form-control" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="post_image">Current Image:</label>
                                        <?php if (!empty($post['image'])): ?>
                                            <div>
                                                <img src="../Admin/image/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="img-thumbnail" style="max-width: 200px;">
                                            </div>
                                        <?php else: ?>
                                            <p>No image available.</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="post_image">Upload New Image (optional):</label>
                                        <input type="file" name="post_image" class="form-control-file">
                                    </div>

                                    <button type="submit" class="btn btn-dark mt-3">Update Post</button>
                                    <a href="view_post.php?id=<?= $post_id ?>" class="btn btn-danger mt-3">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function confirmDelete(postId) {
                    if (confirm("Are you sure you want to delete this post? This action cannot be undone.")) {
                        // Redirect to the delete script with the post ID
                        window.location.href = 'delete_post.php?id=' + postId;
                    }
                }
            </script>

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