<?php
// backend/verify_otp.php - Verify OTP and save to Firebase (FIXED)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['otp']) || !isset($data['email']) || !isset($data['storeName']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$otp = $data['otp'];
$email = $data['email'];
$storeName = $data['storeName'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

session_start();

// Debug: log session
error_log("Session OTP: " . ($_SESSION['register_otp'] ?? 'not set'));
error_log("Input OTP: " . $otp);
error_log("Session Email: " . ($_SESSION['register_email'] ?? 'not set'));
error_log("Input Email: " . $email);

if (!isset($_SESSION['register_otp']) || !isset($_SESSION['register_email'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please register again.']);
    exit;
}

if ($_SESSION['register_otp'] !== $otp) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP code. Please try again.']);
    exit;
}

if ($_SESSION['register_email'] !== $email) {
    echo json_encode(['success' => false, 'message' => 'Email mismatch. Please try again.']);
    exit;
}

if (time() > $_SESSION['otp_expires']) {
    echo json_encode(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
    exit;
}

// 🔥 Firebase configuration
$firebaseUrl = 'https://storeapp-8486c-default-rtdb.asia-southeast1.firebasedatabase.app/';
$apiKey = 'AIzaSyAOH4qhUNqOsIR5Nj8LZYg6hmCDo5Dxx_Y';

// 🔥 CEK EMAIL - PAKE USERS.JSON LOKAL
$usersFile = __DIR__ . '/../users.json';
$users = [];
if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    if (!empty($content)) {
        $users = json_decode($content, true) ?? [];
    }
}

// Cek di users.json lokal
foreach ($users as $user) {
    if (isset($user['email']) && $user['email'] === $email) {
        echo json_encode(['success' => false, 'message' => 'Email already registered. Please login.']);
        exit;
    }
}

// 🔥 CEK JUGA DI FIREBASE
$checkUrl = $firebaseUrl . 'users.json?key=' . $apiKey . '&orderBy="email"&equalTo="' . urlencode($email) . '"';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $checkUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $existingData = json_decode($response, true);
    if (!empty($existingData)) {
        echo json_encode(['success' => false, 'message' => 'Email already registered. Please login.']);
        exit;
    }
}

// 🔥 SAVE KE USERS.JSON LOKAL
$newUser = [
    'id' => uniqid(),
    'storeName' => $storeName,
    'email' => $email,
    'password' => $password,
    'bio' => 'Building the future of online business.',
    'avatar' => '',
    'createdAt' => date('Y-m-d H:i:s'),
    'status' => 'active'
];

$users[] = $newUser;
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

// 🔥 SAVE JUGA KE FIREBASE
$userData = [
    'storeName' => $storeName,
    'email' => $email,
    'bio' => 'Building the future of online business.',
    'avatar' => '',
    'createdAt' => date('Y-m-d H:i:s'),
    'status' => 'active'
];

$url = $firebaseUrl . 'users/' . md5($email) . '.json?key=' . $apiKey;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PUT, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Clear session
unset($_SESSION['register_otp']);
unset($_SESSION['register_email']);
unset($_SESSION['register_store']);
unset($_SESSION['otp_expires']);

// Set session untuk auto login
$_SESSION['user_id'] = $newUser['id'];
$_SESSION['store_name'] = $storeName;
$_SESSION['user_email'] = $email;
$_SESSION['user_bio'] = 'Building the future of online business.';
$_SESSION['user_avatar'] = '';

echo json_encode(['success' => true]);
?>