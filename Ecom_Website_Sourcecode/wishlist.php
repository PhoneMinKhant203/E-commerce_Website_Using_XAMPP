<?php
session_start();
include('Admin/config/dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to view your wishlist.');
            window.location.href='signup.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch items in the user's wishlist with promotions
$query = "
    SELECT 
        products.product_id, -- Add this line to fetch product_id
        products.product_name, 
        products.product_image, 
        products.product_price,
        promotions.promotion_percentage,
        promotions.promotion_price,
        wishlists.created_at
    FROM wishlists
    JOIN products ON wishlists.product_id = products.product_id
    LEFT JOIN promotions ON products.product_id = promotions.product_id
        AND CURRENT_DATE BETWEEN promotions.start_date AND promotions.end_date
    WHERE wishlists.user_id = :user_id
";


$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Wishlist</title>
    <link rel="stylesheet" href="style.css" />

    <!-- remix box icon link -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />

    <!-- google font link -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />

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


            <a href="my_account.php" class="myAccount" onclick="checkLogin()">
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


    <div class="cart_hero">
        <div class="wishlist_hero">
            <div class="wishlist-container">
                <h2>Your Wishlist</h2>
                <div class="back_shopping">
                    <a href="shop.php">Back to Shopping</a>
                </div>
                <br>
                <div class="card-container-table">
                    <table class="wishlist-table">
                        <thead>
                            <tr>
                                <td>Products</td>
                                <td>Price</td>
                                <td>Date Added</td>
                                <td class="action-column" style="width: 20px;">Action</td> <!-- Added width here -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wishlist_items)): ?>
                                <?php foreach ($wishlist_items as $item): ?>
                                    <tr>
                                        <td class="product-column">
                                            <?php
                                            $imagePath = htmlspecialchars($item['product_image']);
                                            $fullImagePath = 'Admin/' . $imagePath;
                                            $product_id = $item['product_id'];
                                            ?>
                                            <a href="product_detail.php?product_id=<?php echo $product_id; ?>" style="text-decoration: none; color: inherit;">
                                                <img src="<?php echo $fullImagePath; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="max-width: 100px;">
                                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                            </a>
                                        </td>

                                        <td class="price-column">
                                            <?php
                                            // Determine if there is a promotion and calculate the price
                                            if (!empty($item['promotion_price'])) {
                                                echo '<span style="text-decoration: line-through;">' . number_format($item['product_price'], 2) . ' USD</span> ';
                                                echo '<span style="color: green;">' . number_format($item['promotion_price'], 2) . ' USD </span>';
                                            } elseif (!empty($item['promotion_percentage'])) {
                                                $discounted_price = $item['product_price'] * (1 - $item['promotion_percentage'] / 100);
                                                echo '<span style="text-decoration: line-through;">' . number_format($item['product_price'], 2) . ' USD</span> ';
                                                echo '<span style="color: green;">' . number_format($discounted_price, 2) . ' USD </span>';
                                            } else {
                                                echo number_format($item['product_price'], 2) . ' USD';
                                            }
                                            ?>
                                        </td>

                                        <td class="date-column">
                                            <?php echo date('F j, Y', strtotime($item['created_at'])); ?>
                                        </td>

                                        <td class="action-column" style=" text-align: center;">
                                            <form action="remove_from_wishlist.php" method="post">
                                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                                <button type="submit" onclick="return confirm('Are you sure you want to remove this item from your wishlist?');">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">Your wishlist is empty.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

    <script src="script.js"></script>
</body>

</html>