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

    <style>
        .charity_container {
            display: flex;
            align-items: center;
            padding: 20px;
            max-width: 1200px;
            margin: auto;
            background-color: #f8f8f8;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 100px;
        }

        .image-section {
            flex: 1;
            padding-right: 20px;
        }

        .image-section img {
            width: 100%;
            border-radius: 10px;
        }

        .text-section {
            flex: 1;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .text-section h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .text-section h3 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .text-section p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 15px;
        }

        .text-section img {
            width: 50px;
            margin-top: 15px;
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
    <a href="about.php" style="margin-left: 200px; margin-top:20px;">Back</a>
    <div class="big_chairty_container">


        <div class="charity_container">

            <!-- Left Section - Image -->
            <div class="image-section">
                <img src="Images/charity_1.png" alt="Surfrider Foundation">
            </div>

            <!-- Right Section - Text -->
            <div class="text-section">
                <h2>Surfrider Foundation:</h2>
                <h3>Dedicated to the protection and enjoyment of the world’s oceans, waves and beaches, for all people, through a powerful activist network.</h3>
                <p>It’s been nearly three years since we first partnered with our friends at Surfrider Foundation. Their contagious excitement for protecting and preserving our oceans and coastlines, along with their proven track record to create visible change through huge legislative victories (over 500 and counting!) and thousands of beach clean ups, has inspired us to keep seeking more responsible design practices as a brand. Earlier this year, our team met up with members from their Los Angeles chapter to clean over 300 pieces of microplastic pollution from our local Venice Beach shoreline.</p>
                <img src="Images/UN.png" alt="1% for the Planet Logo">
            </div>
        </div>

        <div class="charity_container">
            <div class="text-section">
                <h2>Women’s Earth Alliance:</h2>
                <h3>Catalyzes women-led, grassroots solutions to protect our environment and strengthen communities from the inside out.</h3>
                <p>In some of the most environmentally threatened places in the world, WEA leaders are defending forests and rivers, saving threatened indigenous seeds, launching sustainable farms, conserving coral reefs, and protecting land rights. When we first learned of their elaborate planetary initiatives over two years ago, with projects spanning across 24 countries, 25 thousand women leaders, and 17 million people reached, we were absolutely blown away. We’re beyond stoked to continue being a small part of their immense global impact.</p>
                <img src="Images/UN.png">
            </div>

            <div class="image-section">
                <img src="Images/charity_2.png" alt="Surfrider Foundation">
            </div>
        </div>

        <div class="charity_container">
            <!-- Left Section - Image -->
            <div class="image-section">
                <img src="Images/charity_3.png" alt="Surfrider Foundation">
            </div>

            <!-- Right Section - Text -->
            <div class="text-section">
                <h2>Step Up:</h2>
                <h3>Delivers support to people experiencing serious mental health conditions and chronic homelessness to help them recover, stabilize, and integrate into the community.</h3>
                <p>Step Up connects those in California and Southeastern USA experiencing serious mental health conditions and chronic homelessness to permanent supportive housing, rich wraparound support, and workforce development. In 2021 alone, Step Up coordinated services for more than 3,900 unduplicated clients, with over 1,850 individuals being housed through Step Up’s Housing First programs. Mental health support and well-being is a subject matter of incredible importance to our team. This new partnership with Step Up is one we’re both honored to be a part of and passionate about.

                </p>
                <img src="Images/UN.png" alt="1% for the Planet Logo">
            </div>
        </div>

        <div class="charity_container">
            <div class="text-section">
                <h2>LA Food Bank:</h2>
                <h3>Mobilizes resources to fight hunger in our community.</h3>
                <p>Since 1973, the Los Angeles Regional Food Bank has distributed more than 1.9 billion pounds of food across our local community. They not only work to ensure that children who rely on school meals are provided access to healthy food over the weekends, but spearhead educational campaigns and public policy change to help solve the food insecurity crisis from the inside out. Partnering with an organization who allows us to directly impact our home city and neighbors was a huge priority for us when exploring initiatives, and we’re honored to be a part of their crucial work.</p>
                <img src="Images/UN.png" alt="1% for the Planet Logo">
            </div>

            <div class="image-section">
                <img src="Images/charity_4.png" alt="Surfrider Foundation">
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