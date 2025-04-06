<?php
session_start();
include('Admin/config/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: signup.php'); // Redirect if not logged in
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch all transactions for the logged-in user
$sql = "
    SELECT 
        t.transaction_id,
        t.transaction_amount,
        t.transaction_status,
        t.created_at,
        c.total_price
    FROM transactions t
    JOIN cart c ON t.cart_id = c.id
    WHERE t.user_id = :user_id
    ORDER BY t.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order History</title>
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


    <div class="account_hero_big">

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


        <div class="account-details-container">
            <div class="content-container container">
                <div class="account_Order_Container">
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Your Orders</h4>
                                </div>
                                <?php if (!empty($transactions)): ?>
                                    <div class="card-body">
                                        <!-- Add scrollable area for table -->
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 150px; text-align: center;">Transaction ID</th>
                                                        <th style="width: 150px; text-align: center;">Date</th>
                                                        <th style="width: 150px; text-align: center;">Status</th>
                                                        <th style="width: 150px; text-align: center;">Total</th>
                                                        <th style="width: 150px; text-align: center;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($transactions as $transaction): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                                                            <td><?= date('Y-m-d H:i:s', strtotime($transaction['created_at'])) ?></td>
                                                            <td><?= ucfirst(htmlspecialchars($transaction['transaction_status'])) ?></td>
                                                            <td>$<?= number_format($transaction['total_price'], 2) ?></td>
                                                            <td>
                                                                <a href="account_order_details.php?transaction_id=<?= $transaction['transaction_id'] ?>" class="order_button">View</a>
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


</body>

</html>