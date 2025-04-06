<?php
include('../Admin/config/dbconnect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_title = $_POST['post_title'];
    $post_content = $_POST['post_content'];
    $post_image = $_FILES['post_image'];

    // Handle the image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($post_image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($post_image["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check file size (for example, limit to 2MB)
    if ($post_image["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Check if everything is ok to upload
    if ($uploadOk === 1) {
        if (move_uploaded_file($post_image["tmp_name"], $target_file)) {
            // Store the post in the database
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
            $stmt->execute([$post_title, $post_content, $target_file]);

            echo "The post has been uploaded successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title> Alfred Bryson Ecommerce </title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link id="pagestyle" href="assets/css/material-dashboard.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .form-control {
            border: 1px solid #b3a1a1 !important;
            padding: 8px 10px;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include('includes/navbar.php'); ?>

        <div class="main-container d-flex">
            <aside class="sidebar">
                <?php include('includes/sidebar.php'); ?>
            </aside>

            <div class="content-container container">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Create a New Post</h4>
                            </div>
                            <div class="card-body">
                                <!-- Admin Post Upload Form -->
                                <form action="post_uploading.php" method="POST" enctype="multipart/form-data">
                                    <!-- Post Title -->
                                    <div>
                                        <label for="post_title">Post Title:</label>
                                        <input type="text" name="post_title" id="post_title" class="form-control" required>
                                    </div>

                                    <!-- Plain Textarea for Post Content -->
                                    <div>
                                        <br> <label for="post_content">Post Content:</label>
                                        <textarea name="post_content" id="post_content" class="form-control" required></textarea>
                                    </div>

                                    <!-- Image Upload -->
                                    <div>
                                        <br><label for="post_image">Upload Image:</label>
                                        <input type="file" name="post_image" id="post_image" class="form-control" accept="image/*" required>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-dark" name="add_products_btn">Save</button>
                                        <a href="admin_posting.php" class="btn btn-danger float-end">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/perfect-scrollbar.min.js"></script>

</body>

</html>