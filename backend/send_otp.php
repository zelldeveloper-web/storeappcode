<?php
// backend/send_otp.php - Send OTP via Email using PHPMailer
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['storeName'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$email = $data['email'];
$storeName = $data['storeName'];
$isResend = isset($data['resend']) && $data['resend'] === true;

// Generate 8-digit OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Save OTP to session
session_start();
$_SESSION['register_otp'] = $otp;
$_SESSION['register_email'] = $email;
$_SESSION['register_store'] = $storeName;
$_SESSION['otp_expires'] = time() + 300;

// Gmail configuration
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ucupemeelbb@gmail.com';
    $mail->Password   = 'zngl hkyb uafe ggoy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('ucupemeelbb@gmail.com', 'Store Instant');
    $mail->addAddress($email, $storeName);

    $mail->isHTML(true);
    $subject = $isResend ? 'Store Instant - New OTP Verification Code' : 'Store Instant - OTP Verification Code';
    $mail->Subject = $subject;
    $mail->Body    = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0a0a0a; margin: 0; padding: 20px; }
            .container { max-width: 500px; margin: 0 auto; background: #121212; border-radius: 16px; padding: 32px 24px; border: 1px solid #1a1a1a; }
            .header { text-align: center; margin-bottom: 24px; }
            .header h1 { color: #ffffff; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin: 0; }
            .header p { color: #666; font-size: 14px; margin: 4px 0 0; }
            .otp-box { background: #1a1a1a; border-radius: 12px; padding: 24px; text-align: center; margin: 20px 0; }
            .otp-code { font-size: 40px; font-weight: 800; letter-spacing: 8px; color: #ffffff; font-family: monospace; }
            .info { color: #999; font-size: 14px; text-align: center; margin: 0; }
            .expiry { color: #666; font-size: 12px; text-align: center; margin: 8px 0 0; }
            .footer { text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #1a1a1a; }
            .footer p { color: #444; font-size: 12px; margin: 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Store Instant</h1>
                <p>verify your email address</p>
            </div>
            <div class="otp-box">
                <div class="otp-code">' . $otp . '</div>
            </div>
            <p class="info">Enter this code to complete your registration</p>
            <p class="expiry">This code will expire in 5 minutes</p>
            <div class="footer">
                <p>Thank you for choosing Store Instant!</p>
            </div>
        </div>
    </body>
    </html>
    ';
    $mail->AltBody = "Store Instant Verification Code\n\nYour verification code is: " . $otp . "\n\nThis code will expire in 5 minutes.\n\nThank you for choosing Store Instant!";

    $mail->send();
    
    echo json_encode(['success' => true, 'otp' => $otp]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP: ' . $mail->ErrorInfo]);
}
?>