<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Check if the promotion ID is provided via GET
if (isset($_GET['id'])) {
    $promotion_id = $_GET['id'];

    // Fetch the current promotion and product details
    try {
        $query = "SELECT promotions.promotion_id, promotions.promotion_percentage, promotions.promotion_price, promotions.start_date, promotions.end_date, 
                         promotions.product_id, products.product_name, products.product_price, products.product_image 
                  FROM promotions 
                  INNER JOIN products ON promotions.product_id = products.product_id 
                  WHERE promotions.promotion_id = :promotion_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT);
        $stmt->execute();
        $promotions = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$promotions) {
            echo "Promotion not found.";
            exit;
        }

        // Fetch all products for dynamic price update
        $productQuery = "SELECT product_id, product_name, product_price FROM products";
        $productStmt = $pdo->query($productQuery);
        $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error fetching promotion data: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid request.";
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
                        <h4>Edit Promotion</h4>
                    </div>
                    <div class="card-body">
                        <!-- The form action will be directed to update_code.php -->
                        <form action="update_promotion.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate();">
                            <div class="row">
                                <input type="hidden" name="promotion_id" value="<?= $promotions['promotion_id'] ?>">

                                <div class="col-md-6">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <select id="product_id" name="product_id" class="form-control" onchange="updateOriginalPrice()">
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['product_id'] ?>"
                                                <?= $promotions['product_id'] == $product['product_id'] ? 'selected' : '' ?>>
                                                <?= $product['product_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="promotion_percentage" class="form-label">Promotion Percentage (%)</label>
                                    <input type="number" name="promotion_percentage" class="form-control" value="<?= $promotions['promotion_percentage'] ?>" oninput="calculatePromotionPrice()" required>
                                </div>

                                <div class="col-md-6">
                                    <br><label for="promotion_price" class="form-label">Promotion Price ($)</label>
                                    <input type="text" id="promotion_price" name="promotion_price" class="form-control" value="<?= number_format($promotions['promotion_price']) ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <br><label for="original_price" class="form-label">Original Price ($)</label>
                                    <input type="text" id="original_price" class="form-control" value="<?= number_format($promotions['product_price'], 2) ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <br><label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $promotions['start_date'] ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <br><label for="end_date" class="form-label">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $promotions['end_date'] ?>" required>
                                </div>

                                <div class="mb-3">
                                    <br><label for="product_image" class="form-label">Product Image</label><br>
                                    <img src="<?= $promotions['product_image'] ?>" alt="Product Image" style="max-width: 200px;"><br>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" name="update_promotion" class="btn btn-dark">Update</button>
                                    <a href="promotion.php" class="btn btn-danger">Cancel</a>
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

<!-- JavaScript function to confirm update -->
<script>
    function confirmUpdate() {
        return confirm("Are you sure you want to update this promotion?");
    }

    function calculatePromotionPrice() {
        const promotionPercentage = parseFloat(document.querySelector('input[name="promotion_percentage"]').value);

        // Remove commas from the original price before parsing it to a float
        const originalPriceRaw = document.getElementById('original_price').value;
        const originalPrice = parseFloat(originalPriceRaw.replace(/,/g, ''));

        if (!isNaN(promotionPercentage) && !isNaN(originalPrice) && promotionPercentage >= 0 && promotionPercentage <= 100) {
            const discountAmount = (promotionPercentage / 100) * originalPrice;
            const promotionPrice = originalPrice - discountAmount;
            document.querySelector('input[name="promotion_price"]').value = promotionPrice.toFixed(2);
        } else {
            document.querySelector('input[name="promotion_price"]').value = ''; // Clear if invalid
        }
    }



    // Add an event listener to update the promotion price when the percentage is changed
    document.querySelector('input[name="promotion_percentage"]').addEventListener('input', calculatePromotionPrice);
</script>