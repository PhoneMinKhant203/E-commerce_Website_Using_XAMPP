<?php
session_start();
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

  <div class="big-metro-site-container">

    <div class="about-client">
      <div class="left-side-contain">
        <h1>Who We Are</h1>
        <p>At Alfred D. Bryson, we believe that a watch is more than just a tool for telling time; it’s a statement of style and sophistication. Each timepiece in our collection is meticulously crafted with precision, blending tradition with innovation to create watches that resonate with discerning individuals.</p>

        <p>Our journey began with a passion for horology and a commitment to excellence. We curate only the finest luxury watches from renowned brands, ensuring our customers enjoy an unparalleled shopping experience. Whether you’re searching for a classic design or a contemporary masterpiece, our selection caters to every taste and occasion.</p>

        <div class="take-your-btn">
          <h3><a href="signup.php" style="color: #fff;">TAKE A YOUR</a></h3>
          <i class="ri-arrow-right-s-line"></i>
        </div>
      </div>

      <div class="right-side-contain">
        <img src="Images/aboute client.jpg" alt="">
      </div>
    </div>

    <div class="shop-from-us">
      <div class="shop-text">
        <h1>Why Shop From Us</h1>
      </div>

      <div class="shop-service-box">
        <!-- box 1 -->
        <div class="box">
          <i class="ri-truck-line"></i>
          <h2>FREE SHIPPING</h2>
          <p>Enjoy FREE SHIPPING on all orders!</p>
        </div>

        <!-- box 2 -->
        <div class="box">
          <i class="ri-customer-service-2-fill"></i>
          <h2>24/7 SUPPORT</h2>
          <p>We're here for you 24/7! </p>
        </div>

        <!-- box 3 -->
        <div class="box">
          <i class="ri-thumb-up-line"></i>
          <h2>Good Quality</h2>
          <p>Uncompromising Quality Assurance.</p>
        </div>

      </div>
    </div>

    <div class="contact-section">
      <div class="contain">
        <h1>We Deliver Genuine Products</h1>
        <p>At Alfred D. Bryson, authenticity is our promise. We are dedicated to providing only genuine luxury watches from reputable brands, ensuring that every piece you purchase is an authentic representation of craftsmanship and style. Our rigorous quality checks guarantee that you receive products that meet our high standards of excellence. With us, you can shop with confidence, knowing that each watch is backed by our commitment to integrity and quality.</p>
        <a href="contact.php"><button class="contact-btn">CONTACT US</button></a>
      </div>
    </div>

    <div class="blog-brand-section">
      <div class="blog-text">
        <p>Once we ordered some </p>
      </div>
    </div>

    <div class="charity-sec">
      <div class="contain_text">
        <h2>Give Back With Us</h2>
        <p>We’re pledging 1% of all revenue to 4 partner charity organizations. You’ll be able to directly participate in this initiative at checkout, where you can choose which cause you'd like us to donate to!</p>


        <a href="charity.php">
          <button class="causes">
            Our Causes
          </button>
        </a>




      </div>
    </div>


  </div>

  <hr>

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

</body>

</html>