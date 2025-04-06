<?php
session_start();
include('Admin/config/dbconnect.php');

if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];

  // Fetch user details from the database
  $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :userId");
  $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user) {
    $name = htmlspecialchars($user['name']); // Sanitizing output to prevent XSS
    $email = htmlspecialchars($user['email']);
  } else {
    // If user data is not found, set default values or redirect as needed
    $name = "Unknown User";
    $email = "No email found";
  }
} else {
  // If user is not logged in, redirect them to login page
  header("Location: signup.php");
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account</title>
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

  <div class="account_hero">

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

    <div class="account_session">

      <div class="Account_container">
        <div class="Account_card">
          <a href="account_detail.php" class="Account_card_link">
            <div class="card_icon">
              <i class="ri-account-circle-line"></i>
            </div>
            <div class="card_text">
              <h2>Account</h2>
              <p>Edit your name or change your password</p>
            </div>
            <i class="ri-arrow-right-s-line"></i>
          </a>
        </div>

        <div class="Account_card">
          <a href="address_details.php" class="Account_card_link">
            <div class="card_icon">
              <i class="ri-home-4-line"></i>
            </div>
            <div class="card_text">
              <h2>Address</h2>
              <p>Setting your account Billing or Shipping address</p>
            </div>
            <i class="ri-arrow-right-s-line"></i>
          </a>
        </div>
      </div>

      <div class="Account_details">
        <h2>Personal Details:</h2>
        <ul>
          <li><strong>User Name:</strong> <?php echo $name; ?></li>
          <li><strong>Email:</strong> <?php echo $email; ?></li>
        </ul>
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