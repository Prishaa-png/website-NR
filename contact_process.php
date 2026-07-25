<?php
// contact_process.php
// Requires PHPMailer — install via Composer (see setup notes at the bottom of this file)

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ---- CONFIG: fill these in with your SMTP provider's details ----
$smtpHost     = 'smtp.yourprovider.com';   // e.g. smtp.gmail.com, smtp.mailgun.org, smtp-relay.yourhost.com
$smtpUsername = 'your-smtp-username';
$smtpPassword = 'your-smtp-password';
$smtpPort     = 587;                       // 587 = TLS (recommended), 465 = SSL
$smtpEncryption = PHPMailer::ENCRYPTION_STARTTLS; // use ENCRYPTION_SMTPS for port 465

$to       = 'demo@site.com';               // <-- YOUR email, where submissions go
$fromAddr = 'no-reply@yourdomain.com';     // must be on your own domain
$fromName = 'Website Contact Form';
// -------------------------------------------------------------------

header('Content-Type: text/plain');

// Get and validate input
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please fill out all fields with a valid email.';
    exit;
}

$emailSubject = $subject !== '' ? $subject : 'New message from ' . $name;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = $smtpEncryption;
    $mail->Port       = $smtpPort;

    // Sender / recipient
    $mail->setFrom($fromAddr, $fromName);
    $mail->addAddress($to);
    $mail->addReplyTo($email, $name); // reply goes straight to the visitor

    // Content
    $mail->isHTML(true);
    $mail->Subject = $emailSubject;
    $mail->Body    = '
        <strong>Name:</strong> ' . htmlspecialchars($name) . '<br>
        <strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
        <strong>Subject:</strong> ' . htmlspecialchars($subject) . '<br><br>
        <strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '
    ';
    $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\n\n$message";

    $mail->send();
    echo 'The message has been sent successfully.';
} catch (Exception $e) {
    http_response_code(500);
    error_log('Mailer Error: ' . $mail->ErrorInfo); // logged, not shown to visitor
    echo 'Sorry, something went wrong. Please try again later.';
}

/*
 SETUP NOTES:

 1. Install PHPMailer via Composer, run from your project root:
      composer require phpmailer/phpmailer

    This creates a vendor/ folder with vendor/autoload.php (already required above).

    No Composer on your host? Download PHPMailer directly from
    https://github.com/PHPMailer/PHPMailer and require its src/ files manually instead:
      require 'PHPMailer/src/PHPMailer.php';
      require 'PHPMailer/src/SMTP.php';
      require 'PHPMailer/src/Exception.php';

 2. Fill in $smtpHost, $smtpUsername, $smtpPassword above with your SMTP provider's
    credentials. Common options: your host's own SMTP (check your hosting control panel),
    Gmail SMTP (needs an App Password), or a transactional service like Mailgun,
    SendGrid, Postmark, or Brevo (all have free tiers and are more reliable for
    deliverability than a generic SMTP box).

 3. Never commit real SMTP credentials to a public repo — load them from environment
    variables or a .env file (outside the web root) in production instead of hardcoding.
*/
