<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');
if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Fetch transaction details
    $transaction_sql = "SELECT transactions.*, users.*, payment_type.payment_type 
                        FROM transactions 
                        JOIN users ON transactions.user_id = users.id 
                        JOIN payment_type ON transactions.payment_type_id = payment_type.payment_type_id
                        WHERE transactions.transaction_id = :transaction_id";
    $stmt = $pdo->prepare($transaction_sql);
    $stmt->execute(['transaction_id' => $transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch products related to the transaction
    $order_items_sql = "SELECT order_items.*, products.product_name, products.product_image
                        FROM order_items 
                        JOIN products ON order_items.product_id = products.product_id
                        WHERE order_items.order_id = :transaction_id";
    $stmt = $pdo->prepare($order_items_sql);
    $stmt->execute(['transaction_id' => $transaction_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Invalid Transaction ID.";
    exit;
}
?>

<div class="main-container d-flex">
    <aside class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </aside>

    <div class="content-container container">
                <div class="account_Order_detail_Container">
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="back_shopping">
                                <a href="order.php">Back to Order History</a>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3>Order Details for Transaction #<?= htmlspecialchars($transaction['transaction_id']) ?></h3>
                                </div>
                                <div class="card-body">
                                    <h5>Shipping Address:</h5>
                                    <p><?= htmlspecialchars($transaction['shipping_address']) ?></p>

                                    <h5>Payment Method: <?= htmlspecialchars($transaction['payment_type']) ?> Card</h5> 

                                    <h4>Details</h4>

                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Photo</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $total = 0; ?>
                                                <?php foreach ($order_items as $item): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                        <td>
                                                            <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="Product Image" style="width: 80px; height: 100px;">
                                                        </td>
                                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                    </tr>
                                                    <?php $total += $item['price'] * $item['quantity']; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div> <br>

                                    <h5>Total: $<?= number_format($total, 2) ?></h5>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    
</div>

<?php include('includes/footer.php'); ?>