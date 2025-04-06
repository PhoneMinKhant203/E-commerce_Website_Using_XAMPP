<?php
session_start();
include('Admin/config/dbconnect.php');

// Query to fetch only products with active promotions
$sql = "
SELECT p.product_id, 
       p.product_name, 
       p.product_price, 
       p.product_stock, 
       p.product_image,
       pr.promotion_percentage, 
       pr.promotion_price, 
       pr.start_date, 
       pr.end_date
FROM products p
JOIN promotions pr 
ON p.product_id = pr.product_id 
WHERE pr.start_date <= NOW() 
AND pr.end_date >= NOW() 
AND pr.promotion_price IS NOT NULL";

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the latest post
$sql = "SELECT post_id, title FROM posts ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->query($sql);
$latest_post = $stmt->fetch(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alfred D. Bryson Watch Store</title>
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

  <script src="logout.js"></script>
</head>

<body>
  <div class="Account_bg_container">
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

    <!-- hero -->
    <section class="hero">
      <div class="hero-text">
        <h5>#Luxury Watches</h5>
        <h1>
          Elegance in Every Second,<br />
          Excellence in Every Watch
        </h1>
        <p>
          At Alfred D. Bryson, a watch is more than a timepiece—it's a symbol
          of style, precision, and heritage. Each watch is crafted to blend
          classic sophistication with modern innovation, offering a timeless
          experience. Discover the art of watchmaking, where every second
          celebrates excellence.
        </p>

        <div class="main-hero">
          <a href="signup.php" class="btn">Order Now</a>
        </div>
      </div>

      <div class="hero-img">
        <img src="Images/homepage_img.png" alt="" />
      </div>
    </section>
    <div class="icons">
      <a href="#"><i class="ri-facebook-fill"></i></a>
      <a href="#"><i class="ri-youtube-fill"></i></a>
      <a href="#"><i class="ri-telegram-2-fill"></i></a>
    </div>
  </div>



  <!-- collection section -->

  <div class="collection">
    <div class="collection-big-card all-collection">
      <img src="Images/collectionWatch_one.jpg" alt="" />
      <div class="collection-text">
        <p></p>
        <h1>AUDEMARS PIGUET</h1>
      </div>
    </div>
    <!-- small cards container -->
    <div class="small-collection-card all-collection">
      <!-- card one -->
      <div class="collection-small-card all-collection">
        <img src="Images/promotion_8.avif" alt="" class="patek" />
        <div class="collection-small-card-text patek-change">
          <h1>PATEK PHILIPPE</h1>
        </div>
      </div>

      <!-- card two -->
      <div class="collection-small-card all-collection">
        <img src="Images/promotion_7.avif" alt="" class="rolex" />
        <div class="collection-small-card-text change-text">
          <h1>ROLEX</h1>
        </div>
      </div>
    </div>
    <!-- Big Card -->
    <div class="collection-big-card all-collection last-cell-size">
      <img src="Images/promotion_4.avif" alt="" class="omega" />
      <div class="collection-text change-last-cell">
        <p></p>
        <h1>OMEGA</h1>
      </div>
    </div>
  </div>

  <!-- Product Section -->
  <div class="product-heading-text">
    <h1>Promotion of the Week !!</h1>
  </div>

  <div class="shop_products scrollable-section">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
        <div class="product promotion">
          <div class="product_img">
            <a href="product_detail.php?product_id=<?= htmlspecialchars($product['product_id']) ?>">
              <img src="<?= 'Admin/' . htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="width: 290px; height: 360px;">
            </a>
          </div>

          <p class="shop_product_name" style="color: #000;"><?= htmlspecialchars($product['product_name']) ?></p>

          <!-- Show promotion prices and stock -->
          <p class="shop_product_price">
            <span class="old-price" style="text-decoration: line-through;">$<?= htmlspecialchars($product['product_price']) ?></span>
            <span class="price">$<?= htmlspecialchars($product['promotion_price']) ?></span>
            <?= htmlspecialchars($product['product_stock']) ?> Units Left
          </p>
          <p class="promotion_percentage">Save <?= htmlspecialchars($product['promotion_percentage']) ?>%</p>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No promotion products found</p>
    <?php endif; ?>
  </div>

  <div class="Post-heading">
    <h3>Inspirational Posts</h3>
  </div>

  <div class="post-blogs">
    <!-- Loop through each post and display -->
    <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
        <div class="post">
          <!-- Make the whole post clickable -->
          <a href="view_post.php?post_id=<?= $post['post_id'] ?>"> <!-- Change 'id' to 'post_id' -->
            <!-- Display the post image -->
            <?php if (!empty($post['image'])): ?>
              <img src="Admin/image/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" />
            <?php else: ?>
              <img src="Images/default_image.jpg" alt="Default Image" /> <!-- fallback if no image -->
            <?php endif; ?>

            <!-- Display the post title -->
            <h2><?= htmlspecialchars($post['title']) ?></h2>

            <!-- Display the post date -->
            <span class="post-date"><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No posts available at the moment.</p>
    <?php endif; ?>
  </div>



  <!-- Feedback Section -->
  <div class="feedback-sec">
    <div class="contain-email">
      <h2>Feedback!!</h2>
      <p>
        It only takes a second to help us to serve your better needs.
      </p>

      <form method="POST" action="submit_feedback.php"> <!-- Set action to your PHP file -->
        <div class="email-submit-btn">
          <input type="text" name="name" placeholder="Your Name" required> <br><br>
          <input type="email" name="email" placeholder="Your Email Address" required><br><br>
          <textarea name="message" id="feedbackMessage" cols="29" rows="7" placeholder="Message" required></textarea>
        </div>
        <button type="submit">Submit</button>
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


</body>

</html>