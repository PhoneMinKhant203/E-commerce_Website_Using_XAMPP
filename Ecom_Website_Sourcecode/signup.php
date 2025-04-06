<?php
session_start();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up & Sign In</title>
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

</head>

<body>

  <div class="sign-bg">

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

    <div class="sign-container">

      <div class="container" id="container">
        <div class="form-container sign-up-container">
          <form action="sign.php" method="post">
            <h1>Create Account</h1>

            <div class="infield">
              <input type="text" placeholder="Name" name="name" required />
              <label></label>
            </div>
            <div class="infield">
              <input type="email" placeholder="Email" name="email" required />
              <label></label>
            </div>
            <div class="infield">
              <input type="password" placeholder="Password" name="password"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                title="Password must be at least 8 characters long, with at least one uppercase letter, one lowercase letter, one number, and one special character."
                required />
              <label></label>
            </div>

            <br>

            <!-- Terms and Conditions Checkbox -->
            <div class="Terms_Condition">
              <div class="terms_Text">
                <input type="checkbox" id="terms" class="terms" required />
                <div class="terms_right_text">
                  I agree to the <a href="terms_and_conditions.php" target="_blank">Terms and Conditions</a>
                </div>
              </div>
            </div>

            <button type="submit">Sign Up</button>
          </form>

        </div>
        <div class="form-container sign-in-container">
          <form action="login.php" method="post">
            <h1>Sign in</h1>

            <div class="infield">
              <input type="email" placeholder="Email" name="email" required />
              <label></label>
            </div>
            <div class="infield">
              <input type="password" placeholder="Password" name="password" required />
              <label></label>
            </div>

            <!-- Forgot Password Link -->
            <a href="#" class="forgot" onclick="toggleForgotPasswordForm()">Forgot your password?</a>

            <!-- Submit Button for Sign In -->
            <button type="submit">Sign In</button>
          </form>
          <!-- Forgot Password Form (Hidden by Default) -->
          <form action="forgot_password.php" method="POST" id="forgot-password-form" style="display:none; margin-top:160px;">
            <h1>Forgot Password</h1>
            <p>Enter your registered email to reset your password</p>

            <div class="infield">
              <input type="email" placeholder="Enter your email" name="email" required />
              <label></label>
            </div>

            <!-- Submit Button for Forgot Password -->
            <button type="submit" name="submit">Submit</button>

            <!-- Back to Sign In Link -->
            <a href="signup.php" onclick="toggleSignInForm()">Back to Sign In</a>
          </form>

        </div>
        <div class="overlay-container" id="overlayCon">
          <div class="overlay">
            <div class="overlay-panel overlay-left">
              <h1>Welcome Back!</h1>
              <p>To keep connected with us please login with your personal info</p>
              <button>Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
              <h1>Hello, Friend!</h1>
              <p>Enter your personal details and start journey with us</p>
              <button>Sign Up</button>
            </div>
          </div>
          <button id="overlayBtn"></button>
        </div>
      </div>

    </div>



    <!-- java script link -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>


    <script>
      const container = document.getElementById('container');
      const overlayCon = document.getElementById('overlayCon');
      const overlayBtn = document.getElementById('overlayBtn');

      overlayBtn.addEventListener('click', () => {
        container.classList.toggle('right-panel-active');
        overlayBtn.classList.remove('btnScaled');

        window.requestAnimationFrame(() => {
          overlayBtn.classList.add('btnScaled');
        });
      });


      document.querySelectorAll('.overlay-panel button').forEach(button => {
        button.addEventListener('click', (e) => {
          if (e.target.innerText === 'Sign Up') {
            container.classList.add('right-panel-active');
          } else {
            container.classList.remove('right-panel-active');
          }
        });
      });
    </script>

    <script>
      function validateForm() {
        const terms = document.getElementById("terms");
        if (!terms.checked) {
          alert("You must agree to the terms and conditions to sign up.");
          return false;
        }
        return true;
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
      function toggleForgotPasswordForm() {
        document.getElementById('forgot-password-form').style.display = 'block';
        document.querySelector('.sign-in-container form').style.display = 'none'; // Hide sign-in form
      }

      function toggleSignInForm() {
        document.getElementById('forgot-password-form').style.display = 'none'; // Hide forgot password form
        document.querySelector('.sign-in-container form').style.display = 'block'; // Show sign-in form
      }
    </script>



    <script src="script.js"></script>
</body>

</html>