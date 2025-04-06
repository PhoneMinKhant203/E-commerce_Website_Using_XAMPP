<?php
session_start();
include('Admin/config/dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to view your cart.');
            window.location.href='signup.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch items in the user's cart
$query = "
    SELECT 
        products.product_name, 
        products.product_image, 
        products.product_price, 
        cart_items.quantity, 
        cart_items.price_at_time,
        cart_items.id AS cart_item_id
    FROM cart
    JOIN cart_items ON cart.id = cart_items.cart_id
    JOIN products ON cart_items.product_id = products.product_id
    WHERE cart.user_id = :user_id AND cart.status = 'active'
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_price = 0;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us</title>
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

    <div class="cart_hero">
        <div class="cart-container">
            <h2>Your Shopping Cart</h2>
            <div class="back_shopping">
                <a href="shop.php">Back to Shopping</a>
            </div>
            <br>
            <div class="card-container-table">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <td>Products</td>
                            <td>Price</td>
                            <td>Quantity</td>
                            <td>Subtotal</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cart_items)): ?>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td class="product-column">
                                        <?php
                                        $imagePath = htmlspecialchars($item['product_image']);
                                        $fullImagePath = 'Admin/' . $imagePath;
                                        ?>
                                        <img src="<?php echo $fullImagePath; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="max-width: 300px;">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </td>
                                    <td class="price-column">
                                        <?php if ($item['price_at_time'] < $item['product_price']): ?>
                                            <span style="text-decoration: line-through;">
                                                <?php echo number_format($item['product_price']); ?> USD
                                            </span>
                                            <br>
                                            <?php echo number_format($item['price_at_time']); ?> USD
                                        <?php else: ?>
                                            <?php echo number_format($item['price_at_time']); ?> USD
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="hidden" name="cart_item_id[]" value="<?php echo $item['cart_item_id']; ?>">
                                        <input type="number" class="quantity-input" data-price="<?php echo $item['price_at_time']; ?>" data-item-id="<?php echo $item['cart_item_id']; ?>" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" style="width: 60px;">
                                    </td>
                                    <td class="subtotal-column">
                                        <?php
                                        $subtotal = $item['price_at_time'] * $item['quantity'];
                                        echo number_format($subtotal);
                                        ?> USD
                                    </td>
                                    <td>
                                        <form action="remove_item.php" method="POST">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                            <button type="submit" class="remove-btn">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php $total_price += $subtotal; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Your cart is empty.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="total-container">
                <h4>Total: <span id="total-price"><?php echo number_format($total_price, 2); ?> USD</span></h4>
                <?php if (!empty($cart_items)): ?>
                    <button id="checkout-btn" type="button" class="buy_btn">Check Out</button>

                <?php endif; ?>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');

            // Handle quantity changes and update subtotals and total price dynamically
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const cartItemId = this.getAttribute('data-item-id');
                    const newQuantity = this.value;
                    const price = parseFloat(this.getAttribute('data-price'));

                    // Send AJAX request to check product stock
                    checkStock(cartItemId, newQuantity, price, this);
                });
            });

            // Function to check product stock before updating quantity
            function checkStock(cartItemId, newQuantity, price, inputField) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'check_stock.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);

                        if (response.success && newQuantity <= response.stock) {
                            // Update subtotal and total price if stock is sufficient
                            const subtotalElement = inputField.closest('tr').querySelector('.subtotal-column');
                            const newSubtotal = price * newQuantity;
                            subtotalElement.textContent = newSubtotal.toFixed(2) + ' USD';

                            // Update total price
                            updateTotalPrice();

                            // Send AJAX request to update quantity in the database
                            updateQuantity(cartItemId, newQuantity);
                        } else {
                            // If stock is insufficient, show an alert and reset to the available stock
                            alert('This Product has only ' + response.stock + ' stock available');
                            inputField.value = response.stock;
                        }
                    } else {
                        console.log('Error checking stock');
                    }
                };
                xhr.send('cart_item_id=' + cartItemId + '&quantity=' + newQuantity);
            }

            // Function to send an AJAX request to update cart item quantity in the database
            function updateQuantity(cartItemId, newQuantity) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_cart_quantity.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Quantity updated successfully');
                    } else {
                        console.log('Error updating quantity');
                    }
                };
                xhr.send('cart_item_id=' + cartItemId + '&quantity=' + newQuantity);
            }

            // Function to calculate and update the total price dynamically
            function updateTotalPrice() {
                let totalPrice = 0;
                const subtotals = document.querySelectorAll('.subtotal-column');

                subtotals.forEach(subtotal => {
                    const value = parseFloat(subtotal.textContent.replace(/[^0-9.-]+/g, ""));
                    totalPrice += value;
                });

                document.getElementById('total-price').textContent = totalPrice.toFixed(2) + ' USD';
            }

            // Handle checkout button click event
            document.getElementById('checkout-btn').addEventListener('click', function() {
                const totalPrice = document.getElementById('total-price').textContent.replace(/[^0-9.-]+/g, "");

                // AJAX request to update the total price in the database
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_cart_total_price.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Total price updated successfully');
                        // Redirect to the checkout page after storing the total price
                        window.location.href = 'check_out.php?total_price=' + totalPrice;
                    } else {
                        console.log('Error updating total price');
                    }
                };
                xhr.send('total_price=' + totalPrice);
            });
        });
    </script>




</body>

</html>