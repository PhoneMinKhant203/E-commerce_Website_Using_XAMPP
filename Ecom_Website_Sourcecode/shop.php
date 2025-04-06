<?php
session_start();
include('Admin/config/dbconnect.php');
include('Admin/data.php');

// Initialize cart count
$cart_count = 0;
$wishlist_count = 0; // Initialize wishlist count

// Assuming the user is logged in and their user ID is stored in the session
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

  // Query to count the number of items in the wishlist
  $stmt = $pdo->prepare("SELECT COUNT(*) AS wishlist_count FROM wishlists WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $wishlist_result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($wishlist_result) {
    $wishlist_count = (int)$wishlist_result['wishlist_count']; // Get the count of items in the wishlist
  }
}

// Initialize an empty products array
$products = [];

// Check if a search term is submitted
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
  $search_term = htmlspecialchars($_GET['search_term']);

  // Prepare SQL query to search for products by name or category
  $stmt = $pdo->prepare("
        SELECT product_id, product_name, product_image, product_price,product_stock 
        FROM products 
        WHERE (product_name LIKE ? OR product_category LIKE ?)");

  $search_term_param = "%$search_term%";  // Add wildcards for partial matches
  $stmt->execute([$search_term_param, $search_term_param]);
} else {
  // Default query to fetch products along with their promotions
  $sql = "
SELECT p.product_id, 
       p.product_name, 
       p.product_category, 
       p.product_price, 
       p.product_stock, 
       p.product_image,
       pr.promotion_percentage, 
       pr.promotion_price, 
       pr.start_date, 
       pr.end_date,
       COUNT(oi.product_id) AS product_count -- Count product orders for popularity
FROM products p
LEFT JOIN promotions pr 
  ON p.product_id = pr.product_id 
  AND pr.start_date <= NOW() 
  AND pr.end_date >= NOW()
LEFT JOIN order_items oi 
  ON p.product_id = oi.product_id -- Join order_items to count popularity
";

  // Check if sort/brand options are set via GET request
  if (isset($_GET['sort_brand'])) {
    $sortBrandOption = $_GET['sort_brand'];

    // Modify the query based on the selected option
    switch ($sortBrandOption) {
      case 'Latest':
        $sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";
        break;
      case 'Popularity':
        $sql .= " GROUP BY p.product_id ORDER BY product_count DESC"; // Order by popularity
        break;
      case 'PriceLowToHigh':
        $sql .= " GROUP BY p.product_id ORDER BY p.product_price ASC";
        break;
      case 'PriceHighToLow':
        $sql .= " GROUP BY p.product_id ORDER BY p.product_price DESC";
        break;
      case 'Rolex':
        $sql .= " WHERE p.product_category = 'Rolex' GROUP BY p.product_id";
        break;
      case 'Omega':
        $sql .= " WHERE p.product_category = 'Omega' GROUP BY p.product_id";
        break;
      case 'PatekPhilippe':
        $sql .= " WHERE p.product_category = 'Patek Philippe' GROUP BY p.product_id";
        break;
      case 'AudemarsPiguet':
        $sql .= " WHERE p.product_category = 'Audemars Piguet' GROUP BY p.product_id";
        break;
      default:
        $sql .= " GROUP BY p.product_id"; // Default grouping if no sorting is selected
    }
  } else {
    // If no sorting option is selected, default to grouping products
    $sql .= " GROUP BY p.product_id";
  }

  $stmt = $pdo->query($sql);
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shop</title>
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
    /* Chat Icon Styles */
    .chat-icon {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #000;
      color: white;
      padding: 15px;
      border-radius: 50%;
      cursor: pointer;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Chat Window Styles */
    .chat-popup {
      display: none;
      position: fixed;
      bottom: 80px;
      right: 20px;
      border: 3px solid #007bff;
      z-index: 9;
      background-color: white;
      width: 300px;
      height: 400px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .chat-header {
      background-color: #000;
      color: white;
      padding: 10px;
      text-align: center;
      border-radius: 10px 10px 0 0;
    }

    .chat-body {
      padding: 7px;
      height: calc(100% - 116px);
      width: 290px;
      color: #000;
      overflow-y: auto;
    }

    .chat-footer {
      padding: 10px;
      background-color: #f1f1f1;
      border-top: 1px solid #ddd;
      height: 50px;
      border-radius: 7px;
    }

    .chat-footer input {
      width: 230px;
      padding: 8px;
      border-radius: 5px;
      margin-left: -30px;
      border: 1px solid #ccc;
    }

    .chat-footer button {
      padding: 5px;
      margin-top: -33px;
      margin-left: 240px;
      background-color: #000;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
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


  <!-- Shop Container -->
  <div class="shop_container">
    <div class="shop_product_container">
      <div class="shop_header_contain">
        <div class="searching_watch">
          <form action="shop.php" method="GET">
            <input type="text" class="shop_search" name="search_term" placeholder="Put Watch Name">
            <button type="submit" class="shop_search_btn">Search</button>
          </form>
        </div>

        <div class="shop_filter">
          <form method="GET" id="sortForm">
            <select name="sort_brand" id="shop_filter_select" onchange="document.getElementById('sortForm').submit();">
              <option value="All" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'All' ? 'selected' : '' ?>>All Products</option>
              <option value="Latest" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'Latest' ? 'selected' : '' ?>>Latest</option>
              <option value="Popularity" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'Popularity' ? 'selected' : '' ?>>Popularity</option>
              <option value="PriceLowToHigh" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'PriceLowToHigh' ? 'selected' : '' ?>>Price: Low to High</option>
              <option value="PriceHighToLow" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'PriceHighToLow' ? 'selected' : '' ?>>Price: High to Low</option>
              <option value="Rolex" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'Rolex' ? 'selected' : '' ?>>Brand: Rolex</option>
              <option value="Omega" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'Omega' ? 'selected' : '' ?>>Brand: Omega</option>
              <option value="PatekPhilippe" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'PatekPhilippe' ? 'selected' : '' ?>>Brand: Patek Philippe</option>
              <option value="AudemarsPiguet" <?= isset($_GET['sort_brand']) && $_GET['sort_brand'] == 'AudemarsPiguet' ? 'selected' : '' ?>>Brand: Audemars Piguet</option>
            </select>
          </form>

          <!-- Wishlist count and cart display -->
          <a href="wishlist.php" class="cart_icon">
            <i class="ri-heart-line"></i>
            <?php if ($wishlist_count > 0): ?>
              <span class="wishlist_count"><?= $wishlist_count ?></span>
            <?php endif; ?>
          </a>

          <a href="cart.php" class="cart_icon">
            <i class="ri-shopping-cart-line"></i>
            <?php if ($cart_count > 0): ?>
              <span class="cart_count"><?= $cart_count ?></span>
            <?php endif; ?>
          </a>
        </div>
      </div>

      <div class="shop_products">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <?php
            // Set a fallback value if product_stock is not set
            $product_stock = isset($product['product_stock']) ? (int)$product['product_stock'] : 0;
            ?>
            <div class="product <?= !empty($product['promotion_price']) ? 'promotion' : '' ?>">
              <div class="product_img">
                <a href="product_detail.php?product_id=<?= htmlspecialchars($product['product_id']) ?>">
                  <img src="<?= 'Admin/' . htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="width: 290px; height: 360px;">
                </a>
              </div>

              <p class="shop_product_name" style="color: #000;"><?= htmlspecialchars($product['product_name']) ?></p>

              <?php if (!empty($product['promotion_price'])): ?>
                <p class="shop_product_price">
                  <span class="old-price" style="text-decoration: line-through;">$<?= htmlspecialchars($product['product_price']) ?></span>
                  <span class="price">$<?= htmlspecialchars($product['promotion_price']) ?></span>
                  <?php if ($product_stock > 0): ?>
                    <?= htmlspecialchars($product_stock) ?> Units Left
                  <?php else: ?>
                    <span style="color: red;">Out of stock</span>
                  <?php endif; ?>
                </p>
                <p class="promotion_percentage">Save <?= htmlspecialchars($product['promotion_percentage']) ?>%</p>
              <?php else: ?>
                <p class="shop_product_price">
                  <span class="price">$<?= htmlspecialchars($product['product_price']) ?></span>
                  <?php if ($product_stock > 0): ?>
                    <?= htmlspecialchars($product_stock) ?> Units Left
                  <?php else: ?>
                    <span style="color: red;">Out of stock</span>
                  <?php endif; ?>
                </p>
              <?php endif; ?>

              <!-- Wishlist and Cart Icons -->
              <div class="shop_product_icon">
                <i class="ri-heart-line add-to-wishlist-button"></i>
                <form method="POST" action="add_to_cart.php">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                  <?php if ($product_stock > 0): ?>
                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                      <i class="ri-shopping-cart-line" id="add-cart"></i>
                    </button>
                  <?php else: ?>
                    <button type="button" class="out-of-stock" style="background: none; border: none; cursor: not-allowed; color: grey;">
                      <i class="ri-shopping-cart-line" id="add-cart"></i>
                    </button>
                  <?php endif; ?>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No products found</p>
        <?php endif; ?>
      </div>



      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Select all out-of-stock buttons
          const outOfStockButtons = document.querySelectorAll('.out-of-stock');

          // Add click event listener to each out-of-stock button
          outOfStockButtons.forEach(button => {
            button.addEventListener('click', function() {
              alert("This product is currently out of stock, so you can't add it to the cart.");
            });
          });
        });
      </script>



    </div>
  </div>


  <!-- Chat Icon -->
  <div class="chat-icon" onclick="toggleChat()">
    💬
  </div>

  <!-- Chat Popup -->
  <div class="chat-popup" id="chatPopup">
    <div class="chat-header">
      <h4>Chatbot</h4>
      <button onclick="toggleChat()" style="float: right; background-color: transparent; border: none; color: white;">&times;</button>
    </div>
    <div class="chat-body" id="chatBody">
      <p><strong>Bot:</strong> Hi! How can I help you?</p>
    </div>
    <div class="chat-footer">
      <form id="chatForm" onsubmit="sendMessage(); return false;">
        <input type="text" id="userInput" placeholder="Type your question..." required>
        <button type="submit" style="background-color: #000; color: white; border: none; border-radius: 5px; cursor: pointer;">
          &#10148;
        </button>

      </form>
    </div>
  </div>

  <script>
    // Toggle Chat Window
    function toggleChat() {
      var chatPopup = document.getElementById("chatPopup");
      chatPopup.style.display = (chatPopup.style.display === "block") ? "none" : "block";
    }

    // Predefined FAQ
    const faq = {
      "hi": "Hi! How can I help you?",
      "hello": "Hi! How can I help you?",
      "hey": "Hi! How can I help you?",
      "good morning": "Hi! How can I help you?",
      "good afternoon": "Hi! How can I help you?",
      "good evening": "Hi! How can I help you?",
      "What are your store hours?": "Our store hours are Monday to Sunday, 24 hours.",
      "What is your return policy?": "You can return items within 14 days of receiving the product, provided it is in brand-new, unworn condition with all original packaging.",
      "Do you offer international shipping?": "Yes, we offer international shipping for all of our luxury watches.",
      "How can I track my order?": "You can track your order using the tracking link sent to your email after the purchase.",
      "What payment methods do you accept?": "We accept credit/debit cards (Visa, Mastercard), bank transfer, and PayPal.",
      "Are your watches authentic?": "Yes, all of our watches are 100% authentic and sourced directly from authorized dealers and reputable partners.",
      "Do you provide a warranty on your watches?": "Yes, all watches come with the original manufacturer warranty, covering defects in craftsmanship or materials.",
      "Can I return a custom or personalized watch?": "Custom or personalized watches cannot be returned, as per our policy.",
      "What happens if my watch is damaged during shipping?": "All items are fully insured until they reach your delivery address. If there's visible damage, please contact us immediately.",
      "How long will my refund take?": "Once your return is approved, refunds are processed within 7-10 business days to the original payment method.",
      "Do you offer repairs?": "Yes, we facilitate repairs during the warranty period through the authorized service center. After the warranty, repair services are at your expense.",
      "What should I do if my product is delayed?": "Delays can occur due to customs clearance or courier issues. Please contact us if the estimated delivery timeframe is exceeded.",
      "Where are you located?": "We are an online store and do not have a physical retail location.",
      "Can I modify my order after placing it?": "Once an order is confirmed, we may not be able to modify it. Please contact customer service for assistance.",
      "What brands do you offer?": "We offer luxury watches from brands like Rolex, Omega, Patek Philippe, and Audemars Piguet."
    };


    // Send Message
    function sendMessage() {
      var userInput = document.getElementById("userInput").value;
      var chatBody = document.getElementById("chatBody");

      // Display user's message
      chatBody.innerHTML += `<p><strong>You:</strong> ${userInput}</p>`;

      // Get response from the bot
      var response = "Please contact our phone number for assistance.";
      for (var question in faq) {
        if (userInput.toLowerCase().includes(question.toLowerCase())) {
          response = faq[question];
          break;
        }
      }

      // Display bot's response
      chatBody.innerHTML += `<p><strong>Bot:</strong> ${response}</p>`;

      // Scroll chat to the bottom
      chatBody.scrollTop = chatBody.scrollHeight;

      // Clear input
      document.getElementById("userInput").value = '';
    }
  </script>




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
  <script>
    document.querySelectorAll('.add-to-wishlist-button').forEach(heartIcon => {
      heartIcon.addEventListener('click', function(e) {
        e.preventDefault();

        const productElement = this.closest('.product');
        const productId = productElement.querySelector('a').href.split('=')[1];

        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              product_id: productId
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              alert(data.message);
              document.querySelector('.wishlist_count').innerText = data.wishlist_count;
            } else {
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      });
    });
  </script>




</body>

</html>