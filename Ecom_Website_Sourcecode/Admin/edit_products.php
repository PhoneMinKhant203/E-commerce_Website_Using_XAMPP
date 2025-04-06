<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');
include('../Admin/data.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get product data from the database
    $sql = "SELECT * FROM products WHERE product_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if product exists
    if (!$product) {
        echo "<p>Product not found.</p>";
        exit;
    }
} else {
    echo "<p>No product ID provided.</p>";
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
                        <h4>Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <!-- The form action will be directed to update_code.php -->
                        <form action="update_code.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate();">
                            <div class="row">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                                <div class="col-md-6">
                                    <label for="product_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="product_name" id="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="product_category" class="form-label">Category</label>
                                    <input type="text" name="product_category" class="form-control" id="product_category" value="<?= htmlspecialchars($product['product_category']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="product_price" class="form-label">Price $</label>
                                    <input type="text" name="product_price" class="form-control" id="product_price" value="<?= htmlspecialchars($product['product_price']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="product_stock" class="form-label">Stock</label>
                                    <input type="text" name="product_stock" class="form-control" id="product_stock" value="<?= htmlspecialchars($product['product_stock']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="product_image" class="form-label">Image</label>
                                    <!-- Display current image -->
                                    <?php if (!empty($product['product_image'])): ?>
                                        <img src="<?= htmlspecialchars($product['product_image']) ?>" alt="Product Image" style="max-width: 200px;"><br><br>
                                    <?php endif; ?>
                                    <label for="">New Image (leave blank if not changing)</label>
                                    <input type="file" name="product_image" class="form-control" id="product_image">
                                </div>

                                <div class="col-md-12">
                                    <label for="product_description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="product_description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
                                </div>

                    

                                <div class="col-md-12">
                                    <br><button type="submit" name="submit" class="btn btn-dark">Update Product</button>

                                </div>

                            </div>
                        </form>
                        <a href="products.php">
                                        <button class="btn btn-danger">Back</button>
                                    </a>

                        <script>
                            function confirmUpdate() {
                                // Get the product name from the form input
                                var productName = document.getElementById('product_name').value;

                                // Show a confirmation dialog
                                var confirmation = confirm("Are you sure you want to update the product: " + productName + "?");

                                // Return true to submit the form if "Yes" is clicked, false to cancel submission if "Cancel" is clicked
                                return confirmation;
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
