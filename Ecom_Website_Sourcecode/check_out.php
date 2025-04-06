<?php
session_start();
include('Admin/config/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php", true, 303);
    exit();
}

$user_id = $_SESSION['user_id'];
$total_price = isset($_GET['total_price']) ? floatval($_GET['total_price']) : 0;

// Fetch user data
try {
    $query = "SELECT name, email, phone, billing_address, shipping_address FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: signup.php", true, 303);
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage(), 3, 'errors.log');
    die("An error occurred, please try again later.");
}

// Handle form submission for transaction
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize form inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'billing_address', FILTER_SANITIZE_STRING); // Billing address
    $shipping_name = filter_input(INPUT_POST, 'shipping_name', FILTER_SANITIZE_STRING);
    $shipping_email = filter_input(INPUT_POST, 'shipping_email', FILTER_SANITIZE_EMAIL);
    $shipping_phone = filter_input(INPUT_POST, 'shipping_phone', FILTER_SANITIZE_STRING);
    $shipping_address = filter_input(INPUT_POST, 'shipping_address', FILTER_SANITIZE_STRING); // Shipping address
    $different_address = filter_input(INPUT_POST, 'different_address', FILTER_SANITIZE_NUMBER_INT); // Checkbox value
    $payment_type_id = filter_input(INPUT_POST, 'payment_type_id', FILTER_SANITIZE_NUMBER_INT);

    // Determine the address to store in the transaction
    if ($different_address == 1) {
        $transaction_address = $shipping_address;
    } else {
        $transaction_address = $address;
    }

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Fetch the active cart ID
        $cart_query = "SELECT id FROM cart WHERE user_id = :user_id AND status = 'active'";
        $cart_stmt = $pdo->prepare($cart_query);
        $cart_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $cart_stmt->execute();
        $cart = $cart_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            throw new Exception("Cart not found for user.");
        }

        $cart_id = $cart['id'];

        // Update the cart's total price and set its status to "checked_out"
        $updateCartQuery = "UPDATE cart SET total_price = :total_price, status = 'checked_out' WHERE id = :cart_id";
        $updateCartStmt = $pdo->prepare($updateCartQuery);
        $updateCartStmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
        $updateCartStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $updateCartStmt->execute();

        // Insert into the transactions table
        $insertTransactionQuery = "
            INSERT INTO transactions (cart_id, user_id, payment_type_id, transaction_amount, transaction_status, shipping_address)
            VALUES (:cart_id, :user_id, :payment_type_id, :transaction_amount, 'pending', :shipping_address)
        ";

        $transactionStmt = $pdo->prepare($insertTransactionQuery);
        $transactionStmt->execute([
            'cart_id' => $cart_id,
            'user_id' => $user_id,
            'payment_type_id' => $payment_type_id,
            'transaction_amount' => $total_price,
            'shipping_address' => $transaction_address
        ]);

        // Get the last inserted transaction ID
        $transactionId = $pdo->lastInsertId();

        // Insert each cart item into order_items and update product stock
        $cartItemsQuery = "SELECT * FROM cart_items WHERE cart_id = :cart_id";
        $cartItemsStmt = $pdo->prepare($cartItemsQuery);
        $cartItemsStmt->execute(['cart_id' => $cart_id]);
        $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);



        foreach ($cartItems as $item) {
            // Insert into order_items table
            $insertOrderItemsQuery = "
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)
            ";
            $orderItemsStmt = $pdo->prepare($insertOrderItemsQuery);
            $orderItemsStmt->execute([
                'order_id' => $transactionId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price_at_time']
            ]);

            // Fetch current product stock
            $productStockQuery = "SELECT product_stock FROM products WHERE product_id = :product_id";
            $productStockStmt = $pdo->prepare($productStockQuery);
            $productStockStmt->execute(['product_id' => $item['product_id']]);
            $product = $productStockStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product not found.");
            }

            $currentStock = $product['product_stock'];
            $newStock = $currentStock - $item['quantity'];

            if ($newStock < 0) {
                throw new Exception("Not enough stock for product ID: " . $item['product_id']);
            }

            // Update product stock
            $updateStockQuery = "UPDATE products SET product_stock = :new_stock WHERE product_id = :product_id";
            $updateStockStmt = $pdo->prepare($updateStockQuery);
            $updateStockStmt->execute([
                'new_stock' => $newStock,
                'product_id' => $item['product_id']
            ]);
        }

        // Clear the cart items after successful transaction
        $clearCartQuery = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $clearCartStmt = $pdo->prepare($clearCartQuery);
        $clearCartStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $clearCartStmt->execute();

        // Mark the cart as inactive
        $updateCartStatusQuery = "UPDATE cart SET status = 'completed' WHERE id = :cart_id";
        $updateCartStatusStmt = $pdo->prepare($updateCartStatusQuery);
        $updateCartStatusStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $updateCartStatusStmt->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect to a confirmation page
        header("Location: shop.php", true, 303);
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $pdo->rollBack();
        error_log("Error processing transaction: " . $e->getMessage(), 3, 'errors.log');
        echo "An error occurred, please try again later.";
    }
}
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
            <a href="index.html" class="logo">
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

    <div class="check_out_hero">
        <div class="checkout-form">
            <div class="back_shopping">
                <a href="cart.php">Back to Cart</a>
            </div> <br>
            <h2><i>Billing Info</i></h2>
            <form action="" method="POST">
                <ul>
                    <!-- Billing Info Fields -->
                    <li>
                        <h4>Name</h4>
                        <input type="text" id="name" name="name" class="checkout_input" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </li>
                    <li>
                        <h4>Email</h4>
                        <input type="email" id="email" name="email" class="checkout_input" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </li>
                    <li>
                        <h4>Phone Number</h4>
                        <input type="text" id="phone" name="phone" class="checkout_input" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </li>
                    <li>
                        <h4>Billing Address</h4>
                        <input type="text" id="billing_address" name="billing_address" class="checkout_input" value="<?= htmlspecialchars($user['billing_address']) ?>" required>
                    </li>

                    <!-- Shipping Info -->
                    <li>
                        <h2 class="shipping_info_txt"><i>Shipping Info</i></h2>
                        <h4><input type="checkbox" class="shipping_checkbox" id="different_address_checkbox">Ship to a different address? </h4>
                        <input type="hidden" name="different_address" id="different_address" value="0">

                    </li>
                </ul>

                <ul id="shipping_address_section">
                    <!-- Additional shipping address fields -->
                    <li>
                        <h4>Name</h4>
                        <input type="text" id="shipping_name" name="shipping_name" class="checkout_input" value="<?= htmlspecialchars($user['name']) ?>">
                    </li>
                    <li>
                        <h4>Email</h4>
                        <input type="email" id="shipping_email" name="shipping_email" class="checkout_input" value="<?= htmlspecialchars($user['email']) ?>">
                    </li>
                    <li>
                        <h4>Phone Number</h4>
                        <input type="text" id="shipping_phone" name="shipping_phone" class="checkout_input" value="<?= htmlspecialchars($user['phone']) ?>">
                    </li>
                    <li>
                        <h4>Shipping Address</h4>
                        <input type="text" id="shipping_address" name="shipping_address" class="checkout_input" value="<?= htmlspecialchars($user['shipping_address']) ?>">
                    </li>
                </ul>

                <ul>
                    <!-- Order Summary -->
                    <li>
                        <h2 class="shipping_info_txt"><i>Payment Method</i></h2>
                        <h4>Total Price: $<?= number_format($total_price, 2) ?></h4>
                    </li>

                    <!-- Payment Details -->
                    <li>
                        <div class="ficons">
                            <!-- Visa -->
                            <img src="Images/visa.png" alt="Visa" id="visa" class="payment-icon" data-payment-type="1" onclick="selectPaymentMethod(1)">
                            <!-- MasterCard -->
                            <img src="Images/master.png" alt="MasterCard" id="master" class="payment-icon" data-payment-type="2" onclick="selectPaymentMethod(2)">
                        </div>
                        <!-- Hidden input to store selected payment_type_id -->
                        <input type="hidden" name="payment_type_id" id="payment_type_id" value="1"> <!-- Default is Visa -->
                    </li>
                    <br>


                    <li>
                        <h4>Name on Card</h4>
                        <input type="text" id="card_name" name="card_name" class="checkout_input" required>
                    </li>
                    <li>
                        <h4>Card Number</h4>
                        <input type="text" id="card_number" name="card_number" class="checkout_input" required>
                    </li>
                    <li>
                        <div style="display: flex; gap: 20px; align-items: center;">
                            <div>
                                <h4>Expired Date</h4>
                                <input type="date" id="expiry_date" name="expiry_date" class="card_expired_date" style="border-radius: 12px; width:140px;" required>
                            </div>
                            <div>
                                <h4>Security Code</h4>
                                <input type="text" name="security_code" id="security_code" class="security_code" style="border-radius: 12px; width:140px;" required>
                            </div>
                        </div>
                    </li> <br>
                    <li>
                        <h4>Zip/Postal Code</h4>
                        <input type="text" id="postal_code" name="postal_code" class="checkout_input" required>
                    </li>
                </ul>

                <!-- Submit Button -->
                <button type="submit" class="buy_btn">Buy Now</button>
            </form>
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
        // Get the checkbox and the shipping address section
        const checkbox = document.getElementById('different_address_checkbox');
        const shippingAddressSection = document.getElementById('shipping_address_section');
        const differentAddressInput = document.getElementById('different_address');

        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                shippingAddressSection.style.display = 'block'; // Show shipping address section
                differentAddressInput.value = '1'; // Set hidden input to 1
            } else {
                shippingAddressSection.style.display = 'none'; // Hide shipping address section
                differentAddressInput.value = '0'; // Set hidden input to 0
            }
        });
    </script>

    <script>
        function selectPaymentMethod(paymentTypeId) {
            // Update the hidden input value with the selected payment type ID
            document.getElementById('payment_type_id').value = paymentTypeId;

            // Remove the 'selected' class from all payment icons
            const icons = document.querySelectorAll('.payment-icon');
            icons.forEach(icon => {
                icon.classList.remove('selected');
            });

            // Add the 'selected' class to the clicked icon
            const selectedIcon = document.querySelector(`img[data-payment-type="${paymentTypeId}"]`);
            selectedIcon.classList.add('selected');
        }
    </script>

    <script>
        function showThankYouMessage() {

            alert("Thank you for your purchase!");


            return true;
        }
    </script>




</body>

</html>