<?php
// backend/send_reset_otp.php - Send OTP for reset password (FIXED)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$email = $data['email'];

// Firebase configuration
$firebaseUrl = 'https://storeapp-8486c-default-rtdb.asia-southeast1.firebasedatabase.app/';
$apiKey = 'AIzaSyAOH4qhUNqOsIR5Nj8LZYg6hmCDo5Dxx_Y';

// Cek apakah email terdaftar di Firebase pake file_get_contents
$checkUrl = $firebaseUrl . 'users.json?key=' . $apiKey . '&orderBy="email"&equalTo="' . urlencode($email) . '"';

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n",
        'timeout' => 10,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($checkUrl, false, $context);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to connect to server']);
    exit;
}

$users = json_decode($response, true);

if (empty($users)) {
    echo json_encode(['success' => false, 'message' => 'This email is not registered on database']);
    exit;
}

// Generate 6-digit OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Save OTP to session
session_start();
$_SESSION['reset_otp'] = $otp;
$_SESSION['reset_email'] = $email;
$_SESSION['reset_otp_expires'] = time() + 300;

// Get user data for name
$userData = reset($users);
$storeName = $userData['storeName'] ?? 'User';

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
    $mail->Subject = 'Store Instant - Reset Password OTP';
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
                <p>reset your password</p>
            </div>
            <div class="otp-box">
                <div class="otp-code">' . $otp . '</div>
            </div>
            <p class="info">This is your OTP to reset your password</p>
            <p class="expiry">This code will expire in 5 minutes</p>
            <div class="footer">
                <p>Thank you for choosing Store Instant!</p>
            </div>
        </div>
    </body>
    </html>
    ';
    $mail->AltBody = "Store Instant - Reset Password OTP\n\nYour OTP code is: " . $otp . "\n\nThis code will expire in 5 minutes.\n\nThank you for choosing Store Instant!";

    $mail->send();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP: ' . $mail->ErrorInfo]);
}
?>