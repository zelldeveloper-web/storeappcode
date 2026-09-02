<?php
// backend/login.php - Login from users.json (FIXED - pake file lokal)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

// Baca dari users.json lokal
$usersFile = __DIR__ . '/../users.json';
$users = [];

if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    if (!empty($content)) {
        $users = json_decode($content, true) ?? [];
    }
}

// Cari user berdasarkan email
$foundUser = null;
$userId = null;

foreach ($users as $key => $user) {
    if (isset($user['email']) && $user['email'] === $email) {
        $foundUser = $user;
        $userId = $key;
        break;
    }
}

if (!$foundUser) {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
    exit;
}

if (!isset($foundUser['password']) || !password_verify($password, $foundUser['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
    exit;
}

session_start();
$_SESSION['user_id'] = $foundUser['id'] ?? $userId;
$_SESSION['store_name'] = $foundUser['storeName'] ?? 'User';
$_SESSION['user_email'] = $foundUser['email'] ?? '';
$_SESSION['user_bio'] = $foundUser['bio'] ?? 'Building the future of online business.';
$_SESSION['user_avatar'] = $foundUser['avatar'] ?? '';

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $foundUser['id'] ?? $userId,
        'storeName' => $foundUser['storeName'] ?? 'User',
        'email' => $foundUser['email'] ?? ''
    ]
]);
?>