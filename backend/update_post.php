<?php
// backend/update_post.php - Update post (FIXED - handle index properly)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['index'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$postsFile = __DIR__ . '/../posts.json';
if (!file_exists($postsFile)) {
    echo json_encode(['success' => false, 'message' => 'Posts file not found']);
    exit;
}

$content = file_get_contents($postsFile);
$posts = json_decode($content, true) ?? [];

$idx = (int)$data['index'];

if (isset($posts[$idx])) {
    if (isset($data['liked']) && isset($data['likes'])) {
        $posts[$idx]['liked'] = (bool)$data['liked'];
        $posts[$idx]['likes'] = (int)$data['likes'];
    }
    if (isset($data['currentSlide'])) {
        $posts[$idx]['currentSlide'] = (int)$data['currentSlide'];
    }
    
    if (file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Post not found at index ' . $idx]);
}
?>