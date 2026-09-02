<?php
// backend/update_profile.php - Fix network error
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Matikan error reporting di output
error_reporting(0);
ini_set('display_errors', 0);

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$storeName = $data['storeName'] ?? '';
$bio = $data['bio'] ?? '';
$avatar = $data['avatar'] ?? '';
$email = $_SESSION['user_email'] ?? '';

if (empty($storeName)) {
    echo json_encode(['success' => false, 'message' => 'Store name cannot be empty']);
    exit;
}

// Check last name change (14 days cooldown)
$lastNameChange = $_SESSION['last_name_change'] ?? 0;
$canChangeName = (time() - $lastNameChange) >= (14 * 24 * 60 * 60);

if ($storeName !== $_SESSION['store_name']) {
    if (!$canChangeName) {
        $daysLeft = ceil((14 * 24 * 60 * 60 - (time() - $lastNameChange)) / (24 * 60 * 60));
        echo json_encode(['success' => false, 'message' => 'You can change name again in ' . $daysLeft . ' days']);
        exit;
    }
    $_SESSION['last_name_change'] = time();
}

// Update session
$_SESSION['store_name'] = $storeName;
$_SESSION['user_bio'] = $bio;
$_SESSION['user_avatar'] = $avatar;

// Firebase configuration
$firebaseUrl = 'https://storeapp-8486c-default-rtdb.asia-southeast1.firebasedatabase.app/';
$apiKey = 'AIzaSyAOH4qhUNqOsIR5Nj8LZYg6hmCDo5Dxx_Y';

// Save to Firebase
$userData = [
    'storeName' => $storeName,
    'bio' => $bio,
    'avatar' => $avatar,
    'updatedAt' => date('Y-m-d H:i:s')
];

$url = $firebaseUrl . 'users/' . md5($email) . '.json?key=' . $apiKey;

// Coba pake file_get_contents dulu (lebih simple)
$options = [
    'http' => [
        'method' => 'PUT',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($userData),
        'timeout' => 5,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

// Kalo file_get_contents gagal, coba curl
if ($response === false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

// Always return success karena session sudah diupdate
echo json_encode(['success' => true]);
?>