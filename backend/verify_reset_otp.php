<?php
// backend/verify_reset_otp.php - Verify OTP and reset password (UPDATE users.json)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$email = $data['email'];

session_start();

// Cek apakah hanya verify OTP
if (isset($data['verifyOnly']) && $data['verifyOnly'] === true) {
    if (!isset($data['otp'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
        exit;
    }

    $otp = $data['otp'];

    if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please request OTP again.']);
        exit;
    }

    if ($_SESSION['reset_otp'] !== $otp) {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP code. Please try again.']);
        exit;
    }

    if ($_SESSION['reset_email'] !== $email) {
        echo json_encode(['success' => false, 'message' => 'Email mismatch. Please try again.']);
        exit;
    }

    if (time() > $_SESSION['reset_otp_expires']) {
        echo json_encode(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
        exit;
    }

    $_SESSION['reset_otp_verified'] = true;
    
    echo json_encode(['success' => true]);
    exit;
}

// Reset password
if (!isset($data['newPassword'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Cek apakah OTP sudah diverifikasi
if (!isset($_SESSION['reset_otp_verified']) || $_SESSION['reset_otp_verified'] !== true) {
    echo json_encode(['success' => false, 'message' => 'OTP not verified. Please verify first.']);
    exit;
}

$newPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

// UPDATE users.json LOKAL
$usersFile = __DIR__ . '/../users.json';
$users = [];

if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    if (!empty($content)) {
        $users = json_decode($content, true) ?? [];
    }
}

$found = false;
foreach ($users as $key => $user) {
    if (isset($user['email']) && $user['email'] === $email) {
        $users[$key]['password'] = $newPassword;
        $users[$key]['updatedAt'] = date('Y-m-d H:i:s');
        $found = true;
        break;
    }
}

if (!$found) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
    unset($_SESSION['reset_otp']);
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_otp_expires']);
    unset($_SESSION['reset_otp_verified']);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}
?>