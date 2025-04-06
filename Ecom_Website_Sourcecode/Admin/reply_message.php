<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');

// Get the message ID from the URL
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the message details
$sql = "SELECT m.message_id, m.user_id, u.name as user_name, u.email as user_email, m.message_text, m.created_at 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        WHERE m.message_id = :message_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':message_id' => $message_id]);
$messages = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$messages) {
    echo "Message not found.";
    exit;
}


// Handle form submission for replying
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply_text = trim($_POST['reply_text']);

    if (empty($reply_text)) {
        $error_message = "Reply text cannot be empty!";
    } else {
        // Insert the reply into the messages table or a new replies table
        $reply_sql = "INSERT INTO replies (message_id, user_id, reply_text, created_at) 
                      VALUES (:message_id, :user_id, :reply_text, NOW())";
        $reply_stmt = $pdo->prepare($reply_sql);
        $reply_stmt->execute([
            ':message_id' => $messages['message_id'],
            ':user_id' => $messages['user_id'],
            ':reply_text' => $reply_text
        ]);

        $success_message = "Reply sent successfully!";
    }
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
                        <h4>Reply to Message</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                        <?php elseif (isset($success_message)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header">
                                <h5>Original Message</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?= htmlspecialchars($messages['user_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($messages['user_email']) ?></p>
                                <p><strong>Message:</strong> <?= htmlspecialchars($messages['message_text']) ?></p>
                                <p><strong>Date Submitted:</strong> <?= htmlspecialchars($messages['created_at']) ?></p>
                            </div>
                        </div>

                        <form method="post" class="mt-4">
                            <div class="form-group">
                                <label for="reply_text">Your Reply</label>
                                <textarea name="reply_text" id="reply_text" class="form-control" rows="5" required></textarea>
                            </div>
                            <br><button type="submit" class="btn btn-primary">Send Reply</button>
                            <a href="admin_message.php" class="btn btn-danger float-end">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include('includes/footer.php'); ?>