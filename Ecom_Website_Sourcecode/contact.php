<?php
include('Admin/config/dbconnect.php');
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get user's email if logged in
$user_email = '';
if ($is_logged_in) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_email = $stmt->fetchColumn();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM feedback ORDER BY created_at DESC");
    $stmt->execute();

    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching feedback: " . $e->getMessage());
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us</title>
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
    <style>
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
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

    <div class="contact_container">
        <h1>Contact Us</h1>
        <p>Feel free to contact us via phone, email, or through our online contact form. We strive to respond promptly, ensuring you receive the support you need. Thank you for choosing Alfred D. Bryson, where your luxury watch journey begins!</p>

        <div class="contact-info">
            <div>
                <h3>Address</h3>
                <p>4671 Sugar Camp Road, Owatonna, Minnesota, 55060</p>
            </div>
            <div>
                <h3>Phone</h3>
                <p>+01 420 285 285</p>
            </div>
            <div>
                <h3>Email</h3>
                <p>customercare@alfred.org</p>
            </div>
        </div>


        <h2>Send Us a Message</h2>
        <form action="send_message_contact.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>

            <!-- Email field with pre-fill for logged-in users -->
            <input type="email" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($user_email); ?>" <?php echo $is_logged_in ? 'readonly' : 'required'; ?>>

            <textarea name="message_text" rows="7" placeholder="Your Message" required></textarea>

            <!-- Button changes based on login status -->
            <br><button type="submit"><?php echo $is_logged_in ? 'Send Message' : 'Login to Send Message'; ?></button>
        </form>



    </div>

    <div class="feedback_container">
        <h1>User Feedback</h1>
        <div class="feedback-carousel">
            <button class="arrow left-arrow" onclick="scrollLeft()">&#9664;</button>
            <div class="feedbacks">
                <?php if ($feedbacks): ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-item">
                            <div class="feedback-name" style="color: #000;"><?php echo htmlspecialchars($feedback['name']); ?></div>
                            <div class="feedback-message"><?php echo nl2br(htmlspecialchars($feedback['message'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="feedback-item">
                        <div class="feedback-message">No feedback available.</div>
                    </div>
                <?php endif; ?>
            </div>
            <button class="arrow right-arrow" onclick="scrollRight()">&#9654;</button>
        </div>
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
        document.addEventListener('DOMContentLoaded', () => {
            const scrollContainer = document.querySelector('.feedbacks');
            const feedbackItems = document.querySelectorAll('.feedback-item');
            const feedbackWidth = feedbackItems[0]?.offsetWidth || 0;

            // Event listeners
            document.querySelector('.left-arrow').addEventListener('click', () => {
                scrollLeft(scrollContainer, feedbackWidth);
            });
            document.querySelector('.right-arrow').addEventListener('click', () => {
                scrollRight(scrollContainer, feedbackWidth);
            });
        });

        function scrollLeft(container, itemWidth) {
            const currentScroll = container.scrollLeft;
            console.log('Current Scroll Left:', currentScroll);

            if (currentScroll > 0) {
                container.scrollBy({
                    left: -itemWidth,
                    behavior: 'smooth'
                });
            }
        }

        function scrollRight(container, itemWidth) {
            const currentScroll = container.scrollLeft;
            const maxScrollRight = container.scrollWidth - container.clientWidth;
            console.log('Current Scroll Right:', currentScroll);

            if (currentScroll < maxScrollRight) {
                container.scrollBy({
                    left: itemWidth,
                    behavior: 'smooth'
                });
            }
        }
    </script>



</body>

</html>