<?php
session_start();
include('Admin/config/dbconnect.php');
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query to count the total number of items in the cart
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart_items ci 
                             JOIN cart c ON ci.cart_id = c.id 
                             WHERE c.user_id = ? AND c.status = 'active'");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the count of items in the cart
    if ($result) {
        $cart_count = (int)$result['total_items'];
    }
}

// Check if product_id is set in the URL
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Fetch the selected product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();

    if (!$product) {
        echo "Product not found.";
        exit;
    }

    // Get the available stock of the product
    $available_quantity = $product['product_stock'];

    // Check for an active promotion for the product
    $stmt_promotion = $pdo->prepare("
        SELECT promotion_price, promotion_percentage 
        FROM promotions 
        WHERE product_id = :product_id 
        AND start_date <= NOW() 
        AND end_date >= NOW()
        LIMIT 1
    ");
    $stmt_promotion->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_promotion->execute();
    $promotion = $stmt_promotion->fetch();

    // Fetch similar products based on the category
    $category = $product['product_category'];
    $stmt_similar = $pdo->prepare("SELECT * FROM products WHERE product_category = :category AND product_id != :product_id LIMIT 9");
    $stmt_similar->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt_similar->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_similar->execute();
    $similar_products = $stmt_similar->fetchAll();
} else {
    echo "Product ID is missing.";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Details</title>
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

    <div class="productDetail-bg">

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

    <div class="product_detail_bar">
        <div class="back_shopping">
            <a href="shop.php">Back to Shopping</a>
        </div>
    </div>





    <!-- Shop Container -->

    <div class="product_container">
        <div class="left_image">
            <!-- Display the product image -->
            <img src="<?= 'Admin/' . htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
        </div>

        <div class="right_Form">
            <form action="add_to_cart.php" method="post">
                <!-- Display the product name -->
                <h1><?= htmlspecialchars($product['product_name']) ?></h1>

                <!-- Display the product brand/category -->
                <p>#<?= htmlspecialchars($product['product_category']) ?></p>

                <!-- Display the product price and promotion if available -->
                <?php if ($promotion): ?>
                    <p class="shop_product_price">
                        <span class="old-price" style="text-decoration: line-through;">$<?= htmlspecialchars($product['product_price']) ?></span>
                        <span style="color: black;">$<?= htmlspecialchars($promotion['promotion_price']) ?></span>
                        <small>(<?= htmlspecialchars($promotion['promotion_percentage']) ?>% off)</small>
                    </p>
                <?php else: ?>
                    <p>$<?= htmlspecialchars($product['product_price']) ?></p>
                <?php endif; ?>

                <!-- Quantity input and Add to Cart button -->
                <div class="quantity_input">
                    <!-- Set the max attribute to limit the quantity to the available stock -->
                    <input type="number" name="quantity" class="quantity" value="1" min="1" max="<?= $available_quantity ?>" required>

                    <!-- Hidden field for the product_id -->
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">

                    <button type="submit">Add to Cart</button>
                    <a href="cart.php" class="cart_icon">
                        <i class="ri-shopping-cart-line"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart_count"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Display the product description -->
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </form>
        </div>
    </div>


    <!-- Similar Product Recommendation -->

    <h1 class="similarTxt">Products you may like</h1> <br><br>

    <div class="similar-products-container">
        <div class="similar-products-small-container">
            <div class="similar-products">
                <button class="arrow left-arrow">&#10094;</button> <!-- Left arrow -->
                <div class="product-slider">
                    <?php foreach ($similar_products as $similar_product): ?>
                        <div class="product-card">
                            <a href="product_detail.php?product_id=<?= $similar_product['product_id'] ?>">
                                <img src="<?= 'Admin/' . htmlspecialchars($similar_product['product_image']) ?>" alt="<?= htmlspecialchars($similar_product['product_name']) ?>">
                                <p><?= htmlspecialchars($similar_product['product_name']) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="arrow right-arrow">&#10095;</button> <!-- Right arrow -->
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
        const leftArrow = document.querySelector('.left-arrow');
        const rightArrow = document.querySelector('.right-arrow');
        const productSlider = document.querySelector('.product-slider');

        leftArrow.addEventListener('click', () => {
            productSlider.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });

        rightArrow.addEventListener('click', () => {
            productSlider.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
    </script>

    <script>
        document.querySelectorAll('.add-to-cart-button').forEach(cartIcon => {
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default behavior of the button/link
                const productElement = this.closest('.product');
                const productId = productElement.querySelector('a').href.split('=')[1];

                fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })
                    .then(response => response.text())
                    .then(message => {

                        alert(message);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    </script>

</body>

</html>