<?php

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Create an instance; passing `true` enables exceptions
if (isset($_POST["send"])) {

    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();                              // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';         // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                     // Enable SMTP authentication
    $mail->Username   = 'phoneminkhant1030501@gmail.com';   // SMTP username
    $mail->Password   = 'ccrs ehjm zvgz shul';    // SMTP password
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
    $mail->setFrom($_POST["email"], $_POST["name"]); // Sender Email and name
    $mail->addAddress('phoneminkhant1030501@gmail.com'); // Add a recipient email  
    $mail->addReplyTo($_POST["email"], $_POST["name"]); // Reply to sender email

    // Content
    $mail->isHTML(true);                         // Set email format to HTML
    $mail->Subject = $_POST["subject"];          // Email subject
    $mail->Body    = $_POST["message"];          // Email message

    // Success message alert
    $mail->send();
    echo "
    <script> 
     alert('Message was sent successfully!');
     document.location.href = 'sendmessage.php';
    </script>
    ";
}
