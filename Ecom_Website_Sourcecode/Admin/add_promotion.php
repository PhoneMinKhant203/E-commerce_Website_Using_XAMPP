<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');
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
                        <h4>Apply Promotion</h4>
                        <a href="promotion.php" class="btn btn-danger float-end">Back</a>
                    </div>
                    <div class="card-body">
                        <form action="apply_promotion_code.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="product_id">Select Product</label>
                                    <select name="product_id" class="form-control" id="product_id" onchange="updateOriginalPrice()">
                                        <option value="">Select a Product</option> <!-- Default option -->
                                        <?php
                                        try {
                                            // Fetch products from the database
                                            $query = "SELECT product_id, product_name, product_price FROM products"; // Ensure product_price is selected
                                            $stmt = $pdo->prepare($query);
                                            $stmt->execute();
                                            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            // Check if products were returned
                                            if ($stmt->rowCount() > 0) {
                                                foreach ($products as $product) {
                                                    echo '<option value="' . htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') . '">';
                                                    echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                                                    echo '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No Products Available</option>';
                                            }
                                        } catch (Exception $e) {
                                            echo '<option value="">Error fetching products</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <input type="hidden" name="original_price" id="original_price">

                                <div class="col-md-6">
                                    <label for="">Promotion Percentage (%)</label>
                                    <input type="text" name="promotion_percentage" placeholder="Enter Promotion Percentage" class="form-control" oninput="calculatePromotionPrice()">
                                </div>

                                <div class="col-md-6">
                                    <br><label for="">Promotion Price $</label>
                                    <input type="text" name="promotion_price" class="form-control" readonly>
                                </div>

                                <div class="col-md-6">
                                    <br><label for="">Start Date</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <br><label for="">End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <br><button type="submit" class="btn btn-dark" name="apply_promotion_btn">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculatePromotionPrice() {
        const promotionPercentage = parseFloat(document.querySelector('input[name="promotion_percentage"]').value);
        const originalPrice = parseFloat(document.getElementById('original_price').value);

        if (!isNaN(promotionPercentage) && !isNaN(originalPrice) && promotionPercentage >= 0 && promotionPercentage <= 100) {
            const discountAmount = (promotionPercentage / 100) * originalPrice;
            const promotionPrice = originalPrice - discountAmount;
            document.querySelector('input[name="promotion_price"]').value = promotionPrice.toFixed(2);
        } else {
            document.querySelector('input[name="promotion_price"]').value = ''; // Clear if invalid
        }
    }

    function updateOriginalPrice() {
        const productId = document.getElementById('product_id').value;

        const products = <?php echo json_encode($products); ?>; // Store products in a JavaScript variable

        const selectedProduct = products.find(product => product.product_id == productId);
        if (selectedProduct) {
            document.getElementById('original_price').value = selectedProduct.product_price; // Set the original price
            calculatePromotionPrice(); // Update promotion price immediately
        } else {
            document.getElementById('original_price').value = ''; // Clear if no product is selected
        }
    }
</script>

<?php include('includes/footer.php'); ?>