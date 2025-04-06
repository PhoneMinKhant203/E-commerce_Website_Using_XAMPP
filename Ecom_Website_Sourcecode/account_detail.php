<?php
session_start();
include('Admin/config/dbconnect.php');

function getUserData($userId)
{
    global $pdo; // Use the PDO connection
    $query = "SELECT name, email, phone, profile FROM users WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]); // Execute with the user ID as a parameter
    return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the user data as an associative array
}

$userData = null;
if (isset($_SESSION['user_id'])) {
    $userData = getUserData($_SESSION['user_id']);
}
?>

<?php
function uploadProfilePhoto($userId)
{
    global $pdo; // Use the database connection from your `dbconnect.php`
    $message = "";

    // Check if a file was uploaded
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileTmp = $_FILES['profile_photo']['tmp_name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check for allowed file extensions and size limit (e.g., 2MB)
        if (in_array($fileExt, $allowed) && $fileSize <= 2097152) {
            // Read the file as binary data
            $profileData = file_get_contents($fileTmp);

            // Update profile photo in the database as a BLOB
            $query = "UPDATE users SET profile = ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute([$profileData, $userId])) {
                $message = "Profile photo updated successfully!";
            } else {
                $message = "Failed to update profile photo in the database.";
            }
        } else {
            $message = "Invalid file type or size. Please upload a JPG, JPEG, PNG, or GIF file (max 2MB).";
        }
    } else {
        $message = "No file uploaded or there was an error during the upload.";
    }

    return $message; // Return the message for display
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Details</title>
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

    <div class="account_hero_big">

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


        <div class="account-details-container">
            <h2>Account Details</h2>

            <!-- Profile Image -->
            <div class="profile-section">

                <div class="user_profile">
                    <?php if (isset($userData['profile'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['profile']); ?>" alt="Profile Image" class="profile-image">
                    <?php else: ?>
                        <img src="Images/no_account.jpg" alt="Profile Image" class="profile-image">
                    <?php endif; ?>

                    <form action="upload_profile_photo.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_photo" class="update_profile_input" accept="image/*" required>
                        <button type="submit" class="image-changes-btn">Change Profile</button>
                    </form>
                </div>

                <div class="user-info">
                    <form action="update_profile.php" method="POST">
                        <ul>
                            <li>
                                <h4>Name</h4>
                                <input type="text" id="name" name="name" class="current_input" value="<?php echo isset($userData['name']) ? htmlspecialchars($userData['name']) : ''; ?>" required>
                            </li>
                            <li>
                                <h4>Email</h4>
                                <input type="email" id="email" name="email" class="current_input" value="<?php echo isset($userData['email']) ? htmlspecialchars($userData['email']) : ''; ?>" required>
                            </li>
                            <li>
                                <h4>Phone Number</h4>
                                <input type="text" id="phone" name="phone" class="current_input" value="<?php echo isset($userData['phone']) ? htmlspecialchars($userData['phone']) : ''; ?>" required>
                            </li>
                        </ul>
                        <button type="submit" class="profile-changes-btn">Update</button>
                    </form>
                </div>

            </div>

            <hr />

            <!-- Password Change Section -->
            <div class="password-change-section">
                <h3><u>Password Change</u></h3>
                <form action="change_password.php" method="POST" class="password_change_form">
                    <div class="password_item">
                        <h4>Current Password</h4>
                        <input type="password" name="current_password" class="current_input" placeholder="Enter current password" required>
                    </div>
                    <div class="password_item">
                        <h4>New Password</h4>
                        <input type="password" name="new_password" class="current_input" placeholder="Enter new password" required>
                    </div>
                    <div class="password_item">
                        <h4>Confirm New Password</h4>
                        <input type="password" name="confirm_password" class="current_input" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" class="save-changes-btn">Update</button>
                </form>
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