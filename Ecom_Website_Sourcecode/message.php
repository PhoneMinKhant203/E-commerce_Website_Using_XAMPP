<?php
include('Admin/config/dbconnect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first!');</script>";
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user ID

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the message text
    $message_text = isset($_POST['message_text']) ? trim($_POST['message_text']) : '';

    // Check if the message text is empty
    if (empty($message_text)) {
        $_SESSION['alert'] = 'Message text cannot be empty!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Insert the user's message into the database
    try {
        $sql = "INSERT INTO messages (user_id, message_text, created_at) VALUES (:user_id, :message_text, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['alert'] = 'Message sent successfully!';
        } else {
            $_SESSION['alert'] = 'Failed to send message.';
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = 'Error: ' . $e->getMessage();
    }

    // Redirect to the same page
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch messages and replies for the logged-in user
try {
    // Fetch user's messages
    $message_sql = "SELECT m.message_id, m.message_text, m.created_at FROM messages m WHERE m.user_id = :user_id ORDER BY m.created_at DESC";
    $message_stmt = $pdo->prepare($message_sql);
    $message_stmt->execute([':user_id' => $user_id]);
    $messages = $message_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch replies for each message
    $replies = [];
    foreach ($messages as $message) {
        $reply_sql = "SELECT r.reply_text, r.created_at FROM replies r WHERE r.message_id = :message_id ORDER BY r.created_at ASC";
        $reply_stmt = $pdo->prepare($reply_sql);
        $reply_stmt->execute([':message_id' => $message['message_id']]);
        $replies[$message['message_id']] = $reply_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $_SESSION['alert'] = 'Error: ' . $e->getMessage();
}

// Display alert message
if (isset($_SESSION['alert'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['alert']) . "');</script>";
    unset($_SESSION['alert']); // Clear the alert after displaying it
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sending Message</title>
    <link rel="stylesheet" href="style.css" />

    <!-- remix box icon link -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet" />

    <!-- google font link -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet" />

    <!-- Swiper Slider CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.css">
    <script src="logout.js"></script>
</head>

<body>
    <div class="account-bg">

        <!-- header -->
        <header>
            <a href="index.php" class="logo">
                <img src="Images/Logo.png" alt="" />
            </a>

            <ul class="navigation">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="shop.php">Product</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>


            <a href="#" class="myAccount" onclick="checkLogin()">
                <i class="ri-account-circle-fill"></i>
            </a>


            <div class="right-content">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <a href="logout.php" class="nav-btn" onclick="return confirmLogout()">Sign Out</a>
                <?php else: ?>

                    <a href="signup.php" class="nav-btn">Sign In</a>
                <?php endif; ?>
                <i class="ri-menu-line" id="menu-icon"></i>
            </div>
        </header>

    </div>

    <div class="account_message_reply">

        <div class="account_left_sidebar">

            <ul class="account_Sidebar">
                <li class="side_Items">
                    <a class="side_navlink" href="my_account.php">
                        <div class="side_Icon">
                            <i class="ri-dashboard-3-fill"></i>
                        </div>
                        <span class="side_Items_Text">Dashboard</span>
                    </a>
                </li>

                <li class="side_Items">
                    <a class="side_navlink" href="account_order.php">
                        <div class="side_Icon">
                            <i class="ri-shopping-basket-line"></i>
                        </div>
                        <span class="side_Items_Text">Orders</span>
                    </a>
                </li>

                <li class="side_Items">
                    <a class="side_navlink" href="address_details.php">
                        <div class="side_Icon">
                            <i class="ri-home-smile-2-line"></i>
                        </div>
                        <span class="side_Items_Text">Address</span>
                    </a>
                </li>

                <li class="side_Items">
                    <a class="side_navlink" href="account_detail.php">
                        <div class="side_Icon">
                            <i class="ri-account-circle-line"></i>
                        </div>
                        <span class="side_Items_Text">Account Details</span>
                    </a>
                </li>

                <li class="side_Items">

                    <a class="side_navlink" href="message.php">
                        <div class="side_Icon">
                            <i class="ri-chat-1-fill"></i>
                        </div>
                        <span class="side_Items_Text">Messages</span>
                    </a>
                </li>


            </ul>

        </div>

        <button class="toggle_sidebar" style="background-color: #c9c9c9;" onclick="toggleSidebar()">
            <i class="ri-menu-3-line"></i> <!-- Icon for toggling -->
        </button>

        <script>
            function toggleSidebar() {
                const sidebar = document.querySelector('.account_left_sidebar');
                sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
            }
        </script>




        <div class="messages_list">
            <h3>Your Messages and Replies</h3>
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message">
                        <h4>Message: <?= htmlspecialchars($message['message_text']) ?></h4>
                        <p><strong>Date Sent:</strong> <?= htmlspecialchars($message['created_at']) ?></p>

                        <!-- Display replies for the message -->
                        <?php if (!empty($replies[$message['message_id']])): ?>
                            <div class="replies" style="max-height: 150px; overflow-y: auto;">
                                <h5>Replies from Admin:</h5>
                                <?php foreach ($replies[$message['message_id']] as $reply): ?>
                                    <p><strong>Reply:</strong> <?= htmlspecialchars($reply['reply_text']) ?><br>
                                        <small><strong>Date:</strong> <?= htmlspecialchars($reply['created_at']) ?></small>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No replies yet.</p>
                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div>

        <!-- Message Form -->
        <form id="messageForm" action="send_message.php" class="reply_message_form" method="POST">
            <h2>Your Message</h2>
            <br>
            <textarea id="message_text" name="message_text" rows="4" cols="50" required></textarea><br><br>
            <input type="submit" value="Send Message">
        </form>




        
    </div>

    <!-- Footer -->

    <footer>
        <div class="hero-footer">
            <div class="Address">
                <img src="Images/Logo.png" alt="" class="flogo">

                <div class="info">
                    <i class="ri-map-pin-2-fill"></i>
                    <h3>No 151, Newyork City, Rive Housing</h3>
                </div>

                <div class="info">
                    <i class="ri-phone-fill"></i>
                    <h3>+123 456 7890</h3>
                </div>

                <div class="info">
                    <i class="ri-mail-line"></i>
                    <h3>info@alfred.org</h3>
                </div>
            </div>

            <div class="information">
                <h1>Information</h1>

                <a href="about.php">About Us</a>
                <a href="view_post.php?post_id=<?= htmlspecialchars($latest_post['post_id']) ?>">Latest Post</a>
                <a href="terms_and_conditions.php">Terms and Conditions</a>
                <a href="#">Advertising</a>
                <a href="contact.php">Contact Us</a>
            </div>

            <div class="information">
                <h1>My Account</h1>

                <a href="my_account.php">My Accounts</a>
                <a href="signup.php">Login/Register</a>
                <a href="cart.php">Cart</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="account_order.php">Order History</a>
            </div>

            <div class="information">
                <h1>Help & Support</h1>

                <a href="how_to_shop.php">How to Shop</a>
                <a href="payment.php">Payment</a>
                <a href="return.php">Returns</a>
                <a href="delivery.php">Delivery</a>
                <a href="privacy.php">Privacy & Cookie Policy</a>
            </div>
        </div>
        <hr>

        <div class="last-section">
            <h3>© Copyright Metro 1014. by <span>Alfred D. Bryson</span></h3>
            <div class="ficons">
                <i class="ri-facebook-fill"></i>
                <i class="ri-linkedin-box-fill"></i>
                <i class="ri-youtube-fill"></i>
                <i class="ri-pinterest-fill"></i>

            </div>

            <div class="ficons">
                <img src="Images/visa.png" alt="">
                <img src="Images/master.png" alt="">
            </div>

        </div>
    </footer>

    <!-- java script link -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>

    <script src="script.js"></script>

    <script>
        function confirmLogout() {
            var confirmation = confirm("Are you sure you want to log out?");
            if (confirmation) {

                window.location.href = "../../logout.php";
            } else {

                return false;
            }
        }
    </script>


    <script>
        function checkLogin() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'my_account.php'; // Redirect to account page
            <?php else: ?>
                alert('Please sign in to access your account.'); // Alert if not signed in
                window.location.href = 'signup.php'; // Redirect to sign-in page
            <?php endif; ?>
        }
    </script>


    <script>
        function clearTextarea() {
            document.getElementById('message_text').value = ''; // Clear the textarea after submission
        }
    </script>




</body>

</html>