<?php
include('../Admin/config/dbconnect.php');

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Required files
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if (isset($_POST['apply_promotion_btn'])) {
    $product_id = $_POST['product_id'];
    $promotion_percentage = $_POST['promotion_percentage'];
    $promotion_price = $_POST['promotion_price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate inputs
    if (!empty($product_id) && !empty($promotion_percentage) && !empty($promotion_price) && !empty($start_date) && !empty($end_date)) {
        try {
            // Insert promotion data into the promotions table
            $query = "INSERT INTO promotions (product_id, promotion_percentage, promotion_price, start_date, end_date) 
                      VALUES (:product_id, :promotion_percentage, :promotion_price, :start_date, :end_date)";
            $stmt = $pdo->prepare($query);

            // Bind parameters to the query
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':promotion_percentage', $promotion_percentage);
            $stmt->bindParam(':promotion_price', $promotion_price);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);

            // Execute the query
            if ($stmt->execute()) {
                // Fetch users who have this product in their wishlist
                $userQuery = "SELECT users.email, p.product_name FROM wishlists 
                              JOIN users ON wishlists.user_id = users.id 
                              JOIN products p ON wishlists.product_id = p.product_id 
                              WHERE wishlists.product_id = :product_id";
                $userStmt = $pdo->prepare($userQuery);
                $userStmt->bindParam(':product_id', $product_id);
                $userStmt->execute();
                $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

                // Prepare email details
                $subject = "Your Wishlist Item is Now On Sale!";
                
                // Loop through each user and send the email
                foreach ($users as $user) {
                    $to_email = $user['email'];
                    $product_name = $user['product_name'];

                    // Create an instance of PHPMailer
                    $mail = new PHPMailer(true);

                    try {
                        // Server settings
                        $mail->isSMTP();                              // Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';       // Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                     // Enable SMTP authentication
                        $mail->Username   = 'your@gamil.com'; // SMTP username
                        $mail->Password   = 'app Password';   // SMTP password
                        $mail->SMTPSecure = 'tls';                    // Use 'tls' for encryption
                        $mail->Port       = 587;                      // TLS port

                        // Optional: Disable SSL certificate verification for local testing
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        // Recipients
                        $mail->setFrom('your@gamil.com', 'Alfred D. Bryson Store'); // Sender Email and name
                        $mail->addAddress($to_email); // Add a recipient email  
                        $mail->addReplyTo('your@gamil.com', 'Alfred D. Bryson Store'); // Reply to sender email

                        // Content
                        $mail->isHTML(true); // Set email format to HTML
                        $mail->Subject = $subject; // Email subject
                        $mail->Body = "
                        <h1>Your Wishlist Item is Now On Sale!</h1>
                        <p>Dear User,</p>
                        <p>We wanted to let you know that the product <strong>\"{$product_name}\"</strong> on your wishlist is now on promotion!</p>
                        <p><strong>Promotion starts on:</strong> {$start_date}</p>
                        <p><strong>Promotion ends on:</strong> {$end_date}</p>
                        <p><strong>Discount Percentage:</strong> {$promotion_percentage}%</p>
                        <p><strong>New Price:</strong> \${$promotion_price}</p>
                        <p>Don't miss out on this great deal!</p>
                        <p>Best Regards,<br>Alfred D. Bryson Store</p>";

                        // Send the email
                        $mail->send();

                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                }

                header("Location: promotion.php?apply=success");
                exit();
            } else {
                header("Location: promotion.php?apply=error");
                exit();
            }
        } catch (Exception $e) {
            echo "Error applying promotion: " . $e->getMessage();
        }
    } else {
        // Redirect if form validation fails
        header("Location: promotion.php?apply=validation_error");
        exit();
    }
} else {
    // If the form was not submitted, redirect back to the promotion page
    header("Location: promotion.php");
    exit();
}
