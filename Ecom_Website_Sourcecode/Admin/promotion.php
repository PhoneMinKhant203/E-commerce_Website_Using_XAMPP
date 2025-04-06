<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch the promotion and product data using PDO
try {
    $query = "SELECT promotions.promotion_id, promotions.promotion_percentage, promotions.promotion_price, promotions.start_date, promotions.end_date, products.product_name, products.product_image 
              FROM promotions 
              INNER JOIN products ON promotions.product_id = products.product_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
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
                        <h4>Promotion Table</h4>
                        <a href="add_promotion.php" class="btn btn-dark float-end">Apply Promotion</a>
                    </div>
                    <?php if (!empty($promotions)): ?>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; max-width:1190px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Promotion ID</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Product Image</th>
                                            <th scope="col">Percentage</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($promotions as $promotion): ?>
                                            <tr>
                                                <td><?= $promotion['promotion_id'] ?></td>
                                                <td><?= $promotion['product_name'] ?></td>
                                                <td><img src="<?= $promotion['product_image'] ?>" alt="Product Image" style="width: 80px; height: 100px;"></td>
                                                <td><?= $promotion['promotion_percentage'] ?>%</td>
                                                <td>$<?= number_format($promotion['promotion_price'], 2) ?></td>
                                                <td><?= $promotion['start_date'] ?></td>
                                                <td><?= $promotion['end_date'] ?></td>
                                                <td>
                                                    <a href="edit_promotion.php?id=<?= $promotion['promotion_id'] ?>" class="btn btn-dark">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="delete_promotion.php?id=<?= $promotion['promotion_id'] ?>" class="btn btn-danger" onclick="return confirmDelete('<?= $promotion['product_name'] ?>');">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-body">
                            <p>No promotions available.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- JavaScript function to confirm deletion -->
<script>
    function confirmDelete(productName) {
        return confirm("Are you sure you want to delete the promotion applied on: " + productName + "?");
    }

    // Show a success alert box if the promotion was successfully deleted
    <?php if (isset($_GET['delete']) && $_GET['delete'] == 'success'): ?>
        alert('Promotion deleted successfully!');
    <?php endif; ?>

    // Show an error alert box if there was an issue deleting the promotion
    <?php if (isset($_GET['delete']) && $_GET['delete'] == 'error'): ?>
        alert('Error deleting the promotion!');
    <?php endif; ?>
</script>
