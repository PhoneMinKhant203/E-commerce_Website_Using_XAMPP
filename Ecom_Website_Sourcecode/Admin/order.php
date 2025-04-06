<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch all transactions and related user and payment data from the database
$sql = "
    SELECT 
        t.transaction_id,
        t.user_id,
        t.transaction_amount,
        t.shipping_address,
        t.transaction_status,
        t.payment_type_id,
        t.created_at,
        u.name AS user_name,
        p.payment_type
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    JOIN payment_type p ON t.payment_type_id = p.payment_type_id
    ORDER BY t.created_at DESC
";
$stmt = $pdo->query($sql);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update transaction status (if the form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    $updateQuery = "UPDATE transactions SET transaction_status = :status WHERE transaction_id = :transaction_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        'status' => $status,
        'transaction_id' => $transaction_id
    ]);

    // Refresh the page to show updated status
    header("Location: order.php", true, 303);
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
                        <h4>Orders List</h4>
                    </div>
                    <?php if (!empty($transactions)): ?>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>User ID</th>
                                            <th>Payment Type</th>
                                            <th>Transaction Amount</th>
                                            <th>Shipping Address</th>
                                            <th style="width: 102px;">Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                                                <td><?= htmlspecialchars($transaction['user_id']) ?></td>
                                                <td><?= htmlspecialchars($transaction['payment_type']) ?></td>
                                                <td>$<?= number_format($transaction['transaction_amount'], 2) ?></td>
                                                <td style="max-width: 200px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; white-space: normal;"><?= htmlspecialchars($transaction['shipping_address']) ?></td>
                                                
                                                <td>
                                                <a href="order_detail.php?transaction_id=<?= $transaction['transaction_id'] ?>"></a>
                                                    <form action="order.php" method="POST">
                                                        <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                                                        <select name="status" class="form-select">
                                                            <option value="pending" <?= $transaction['transaction_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="completed" <?= $transaction['transaction_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                            <option value="failed" <?= $transaction['transaction_status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                                                        </select>
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-dark">Update</button>
                                                    </form>      
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-body">
                            <p>No orders found.</p>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
