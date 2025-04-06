<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Fetch all messages from the database, joining with users to get names and emails
$sql = "SELECT m.message_id, u.name as user_name, u.email as user_email, m.message_text, m.created_at 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        ORDER BY m.created_at DESC";

$stmt = $pdo->query($sql);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <h4>Messages List</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($messages)): ?>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Date Submitted</th>
                                            <th>Replies</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $message): ?>
                                            <tr>
                                                <td><?= $message['message_id'] ?></td>
                                                <td><?= htmlspecialchars($message['user_name']) ?></td>
                                                <td><?= htmlspecialchars($message['user_email']) ?></td>
                                                <td style="max-width: 300px; max-height: 100px; overflow-y: scroll; word-wrap: break-word; white-space: normal;"><?= htmlspecialchars($message['message_text']) ?></td>
                                                <td><?= htmlspecialchars($message['created_at']) ?></td>
                                                <td style="max-width: 300px; max-height: 30px; overflow-y: scroll; word-wrap: break-word; white-space: normal;">
                                                    <?php
                                                    // Fetch replies for the current message
                                                    $reply_sql = "SELECT r.reply_text, r.created_at 
                                                        FROM replies r 
                                                        WHERE r.message_id = :message_id 
                                                        ORDER BY r.created_at ASC";
                                                    $reply_stmt = $pdo->prepare($reply_sql);
                                                    $reply_stmt->execute([':message_id' => $message['message_id']]);
                                                    $replies = $reply_stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    if (!empty($replies)):
                                                        foreach ($replies as $reply): ?>
                                                            <p><strong>Reply:</strong> <?= htmlspecialchars($reply['reply_text']) ?><br>
                                                                <small><strong>Date:</strong> <?= htmlspecialchars($reply['created_at']) ?></small>
                                                            </p>
                                                        <?php endforeach;
                                                    else: ?>
                                                        <p>No replies yet.</p>
                                                    <?php endif; ?>
                                                </td>


                                                <td>
                                                    <a href="reply_message.php?id=<?= $message['message_id'] ?>" class="btn btn-primary">Reply</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No messages found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>