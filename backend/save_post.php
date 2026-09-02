<?php
// backend/save_post.php - Save post to posts.json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['author']) || !isset($data['images']) || !isset($data['caption'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$postsFile = __DIR__ . '/../posts.json';
$posts = [];

if (file_exists($postsFile)) {
    $content = file_get_contents($postsFile);
    if (!empty($content)) {
        $posts = json_decode($content, true) ?? [];
    }
}

$newPost = [
    'id' => uniqid(),
    'author' => $data['author'],
    'avatar' => $data['avatar'] ?? '',
    'images' => $data['images'],
    'caption' => $data['caption'],
    'time' => $data['time'] ?? date('Y-m-d H:i:s'),
    'likes' => 0,
    'liked' => false,
    'currentSlide' => 0
];

array_unshift($posts, $newPost);

if (file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save post']);
}
?>