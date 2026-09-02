<?php
// backend/save_comment.php - Save comment
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['postId']) || !isset($data['author']) || !isset($data['text'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$commentsFile = __DIR__ . '/../comments.json';
$comments = [];

if (file_exists($commentsFile)) {
    $content = file_get_contents($commentsFile);
    if (!empty($content)) {
        $comments = json_decode($content, true) ?? [];
    }
}

$comments[] = [
    'id' => $data['id'] ?? 'c_' . uniqid(),
    'postId' => $data['postId'],
    'author' => $data['author'],
    'text' => $data['text'],
    'time' => $data['time'] ?? date('Y-m-d H:i:s')
];

if (file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save comment']);
}
?>