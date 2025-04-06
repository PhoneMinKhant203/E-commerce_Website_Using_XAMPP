<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'Admin/config/dbconnect.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Retrieve the user's password from the database
        $password = $user['password'];

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // Configure PHPMailer with your SMTP settings
        try {
            $mail->isSMTP();                              // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';         // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                     // Enable SMTP authentication
            $mail->Username   = 'phoneminkhant1030501@gmail.com';   // SMTP username
            $mail->Password   = 'ccrs ehjm zvgz shul';    // SMTP password
            $mail->SMTPSecure = 'tls';                    // Use 'tls' for encryption
            $mail->Port       = 587;                      // TLS port

            // Optional: Disable SSL certificate verification
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('phoneminkhant1030501@gmail.com', 'Alfred D. Bryson Luxury Watch Store');
            $mail->addAddress($email); // Send to the user's email

            // Content
            $mail->isHTML(true);                         // Set email format to HTML
            $mail->Subject = 'Your Password for Alfred D. Bryson Luxury Watch Store';
            $mail->Body    = "Dear {$user['name']},<br><br>Your password is: <strong>{$password}</strong><br><br>If you did not request this email, please ignore it.<br><br>Best regards,<br>Alfred D. Bryson Luxury Watch Store";

            $mail->send();
            echo "<script>alert('Your password has been sent to your email.'); window.location.href='signup.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Error: {$mail->ErrorInfo}');</script>";
        }

    } else {
        // If email is not found in the database
        echo "<script>alert('Your account is not registered yet.'); window.location.href='signup.php';</script>";
    }
}
?>
