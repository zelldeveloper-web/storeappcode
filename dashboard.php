<?php
// dashboard.php - Full code with comment sidebar for mobile (bottom sheet)
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Load profile from Firebase
$email = $_SESSION['user_email'] ?? '';
$firebaseUrl = 'https://storeapp-8486c-default-rtdb.asia-southeast1.firebasedatabase.app/';
$apiKey = 'AIzaSyAOH4qhUNqOsIR5Nj8LZYg6hmCDo5Dxx_Y';

$userName = $_SESSION['store_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
$userBio = $_SESSION['user_bio'] ?? 'Building the future of online business.';
$userAvatar = $_SESSION['user_avatar'] ?? '';

$userData = null;
if (!empty($email)) {
    $url = $firebaseUrl . 'users/' . md5($email) . '.json?key=' . $apiKey;
    $response = @file_get_contents($url);
    if ($response !== false) {
        $userData = json_decode($response, true);
        if ($userData && !empty($userData)) {
            $userName = $userData['storeName'] ?? $userName;
            $userBio = $userData['bio'] ?? $userBio;
            $userAvatar = $userData['avatar'] ?? $userAvatar;
            
            $_SESSION['store_name'] = $userName;
            $_SESSION['user_bio'] = $userBio;
            $_SESSION['user_avatar'] = $userAvatar;
        }
    }
}

// Update all posts with current user info
$postsFile = __DIR__ . '/posts.json';
$posts = [];
if (file_exists($postsFile)) {
    $content = file_get_contents($postsFile);
    if (!empty($content)) {
        $posts = json_decode($content, true) ?? [];
    }
}

$updated = false;
foreach ($posts as &$post) {
    if ($post['author'] === $userName || isset($post['email']) && $post['email'] === $userEmail) {
        $post['author'] = $userName;
        $post['avatar'] = $userAvatar;
        $post['email'] = $userEmail;
        $updated = true;
    }
}
if ($updated) {
    file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));
}

$commentsFile = __DIR__ . '/comments.json';
$comments = [];
if (file_exists($commentsFile)) {
    $content = file_get_contents($commentsFile);
    if (!empty($content)) {
        $comments = json_decode($content, true) ?? [];
    }
}

// Load messages for notification badge
$messagesFile = __DIR__ . '/messages.json';
$messages = [];
if (file_exists($messagesFile)) {
    $content = file_get_contents($messagesFile);
    if (!empty($content)) {
        $messages = json_decode($content, true) ?? [];
    }
}

$unreadCount = 0;
foreach ($messages as $msg) {
    if ($msg['to'] === $userName && !$msg['read']) {
        $unreadCount++;
    }
}

// Get all users for messages
$allUsers = [];
foreach ($posts as $post) {
    if (isset($post['author']) && $post['author'] !== $userName) {
        $allUsers[$post['author']] = [
            'name' => $post['author'],
            'avatar' => $post['avatar'] ?? ''
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Store Instant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ===== SAME CSS AS BEFORE PLUS ADDITIONS ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #000;
            color: #fff;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        #dashboard-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100%;
            max-width: 100%;
            background: #000;
            position: relative;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 14px 20px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
            text-align: center;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 60%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
            overflow: hidden;
            position: relative;
            background: #000;
        }

        .content {
            flex: 1;
            overflow-y: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            height: 100%;
            background: #000;
            padding-bottom: 80px;
            transition: all 0.3s ease;
        }

        .content::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        /* ===== MESSAGES TAB ===== */
        #messagesTab {
            display: none;
            height: 100%;
            overflow-y: auto;
            padding-bottom: 80px;
        }
        #messagesTab.active {
            display: block;
        }

        .messages-header {
            padding: 12px 16px;
            position: sticky;
            top: 0;
            background: rgba(0,0,0,0.95);
            z-index: 5;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        .messages-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.2s ease;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        .conversation-item:active {
            background: rgba(255,255,255,0.04);
        }

        .conversation-item .conv-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        .conversation-item .conv-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-item .conv-info .conv-name {
            font-weight: 600;
            font-size: 15px;
            color: #fff;
        }

        .conversation-item .conv-info .conv-last {
            font-size: 13px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-item .conv-time {
            font-size: 11px;
            color: #555;
            flex-shrink: 0;
        }

        .conversation-item .conv-unread {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #29fd53;
            flex-shrink: 0;
        }

        .messages-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            height: 100%;
        }

        .messages-empty i {
            font-size: 64px;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .messages-empty h2 {
            font-size: 20px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .messages-empty p {
            font-size: 14px;
            color: #333;
            max-width: 280px;
            margin: 0 auto;
        }

        /* ===== POST CARD ===== */
        .post-card {
            display: flex;
            flex-direction: column;
            background: #000;
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            height: calc(100vh - 130px);
            max-height: calc(100vh - 130px);
            overflow: hidden;
        }

        .post-card .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            flex-shrink: 0;
            z-index: 2;
        }

        .post-card .post-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        .post-card .post-author {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            cursor: pointer;
        }

        .post-card .post-author:active {
            opacity: 0.6;
        }

        .post-card .post-time {
            font-size: 12px;
            color: #555;
            margin-left: auto;
        }

        .post-card .post-image-wrapper {
            flex: 1;
            position: relative;
            background: #000;
            overflow: hidden;
            touch-action: pan-y;
            min-height: 0;
        }

        .post-card .post-image-wrapper .slide-track {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }

        .post-card .post-image-wrapper .slide-track .slide {
            flex: 0 0 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            height: 100%;
        }

        .post-card .post-image-wrapper .slide-track .slide img,
        .post-card .post-image-wrapper .slide-track .slide video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #000;
        }

        .post-card .post-image-wrapper .slide-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 5;
            pointer-events: none;
        }

        .post-card .post-image-wrapper .slide-indicators .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transition: all 0.3s ease;
        }

        .post-card .post-image-wrapper .slide-indicators .dot.active {
            background: #fff;
            width: 16px;
            border-radius: 3px;
        }

        .post-card .post-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 8px 16px 4px;
            flex-shrink: 0;
            z-index: 2;
        }

        .post-card .post-actions button {
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .post-card .post-actions button:active {
            transform: scale(0.85);
        }

        .post-card .post-actions .like-btn.liked {
            color: #ff4444;
        }

        .post-card .post-actions .like-count {
            font-size: 14px;
            color: #888;
            margin-left: -8px;
        }

        .post-card .post-caption {
            padding: 0 16px 8px;
            flex-shrink: 0;
            z-index: 2;
        }

        .post-card .post-caption .caption-text {
            font-size: 14px;
            color: #ccc;
            line-height: 1.4;
            word-break: break-word;
        }

        .post-card .post-caption .caption-text .hashtag {
            color: #1a8cd8;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            background: #000;
            min-height: 60vh;
        }

        .empty-state i {
            font-size: 64px;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .empty-state h2 {
            font-size: 20px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: #333;
            max-width: 280px;
            margin: 0 auto;
        }

        .profile-tab-content {
            height: 100%;
            overflow-y: auto;
            padding: 16px 0 0;
            background: #000;
            padding-bottom: 80px;
        }

        .profile-tab-content::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 16px 0;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 32px;
            color: #fff;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.06);
            background-size: cover;
            background-position: center;
        }

        .profile-name-section {
            flex: 1;
            min-width: 0;
        }

        .profile-display-name {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .profile-username {
            font-size: 14px;
            color: #666;
            margin-top: 2px;
        }

        .profile-bio {
            font-size: 14px;
            color: #888;
            margin-top: 4px;
            line-height: 1.4;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .profile-actions .btn-edit {
            flex: 1;
            padding: 8px 0;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            text-align: center;
        }

        .profile-actions .btn-edit:active {
            transform: scale(0.95);
            background: rgba(255,255,255,0.12);
        }

        .profile-actions .btn-share {
            padding: 8px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .profile-actions .btn-share:active {
            transform: scale(0.95);
            background: rgba(255,255,255,0.12);
        }

        .profile-stats {
            display: flex;
            gap: 24px;
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            margin: 12px 0 16px;
        }

        .profile-stats .stat {
            text-align: center;
            flex: 1;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 4px;
        }

        .profile-stats .stat .number {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        .profile-stats .stat .label {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .profile-posts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            padding: 0;
            margin-top: 8px;
        }

        .profile-posts-grid .grid-item {
            aspect-ratio: 1;
            background: #111;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }

        .profile-posts-grid .grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-posts-grid .grid-item .grid-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: #fff;
            font-size: 12px;
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .profile-posts-grid .grid-item:hover .grid-overlay {
            opacity: 1;
        }

        .profile-posts-grid .grid-item .grid-overlay i {
            font-size: 14px;
        }

        .profile-posts-empty {
            color: #555;
            font-size: 14px;
            text-align: center;
            padding: 60px 20px;
            background: transparent;
            border: none;
            margin: 0;
        }

        .profile-posts-empty i {
            color: #1a1a1a;
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }

        #profileTab {
            display: none;
            height: 100%;
            overflow-y: auto;
            background: #000;
        }

        #profileTab.active {
            display: block;
        }

        /* ===== MODAL POST ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            background: #000;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        .modal-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .modal-right {
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease;
            display: flex;
            flex-direction: column;
            background: #0a0a0a;
            border-left: 1px solid rgba(255,255,255,0.04);
        }

        .modal-right.open {
            width: 320px;
        }

        .modal-right .modal-comments-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-right .modal-comments-header h4 {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .modal-right .modal-comments-header button {
            background: none;
            border: none;
            color: #888;
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
        }

        .modal-content .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
        }

        .modal-content .modal-header .modal-close {
            background: none;
            border: none;
            color: #888;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
        }

        .modal-content .modal-header .modal-close:active {
            color: #fff;
        }

        .modal-content .modal-header .modal-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-content .modal-header .modal-user .modal-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            color: #fff;
            background-size: cover;
            background-position: center;
        }

        .modal-content .modal-header .modal-user .modal-name {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }

        .modal-content .modal-image-wrapper {
            flex: 1;
            position: relative;
            background: #000;
            overflow: hidden;
            min-height: 300px;
        }

        .modal-content .modal-image-wrapper .modal-slide-track {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .modal-content .modal-image-wrapper .modal-slide-track .modal-slide {
            flex: 0 0 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            min-height: 300px;
        }

        .modal-content .modal-image-wrapper .modal-slide-track .modal-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #000;
            max-height: 500px;
        }

        .modal-content .modal-image-wrapper .modal-slide-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 5;
            pointer-events: none;
        }

        .modal-content .modal-image-wrapper .modal-slide-indicators .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transition: all 0.3s ease;
        }

        .modal-content .modal-image-wrapper .modal-slide-indicators .dot.active {
            background: #fff;
            width: 16px;
            border-radius: 3px;
        }

        .modal-content .modal-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 8px 16px 4px;
            flex-shrink: 0;
        }

        .modal-content .modal-actions button {
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .modal-content .modal-actions button:active {
            transform: scale(0.85);
        }

        .modal-content .modal-actions .like-btn.liked {
            color: #ff4444;
        }

        .modal-content .modal-actions .like-count {
            font-size: 14px;
            color: #888;
            margin-left: -8px;
        }

        .modal-content .modal-actions .comment-toggle-btn {
            color: #888;
            transition: color 0.2s ease;
        }

        .modal-content .modal-actions .comment-toggle-btn.active {
            color: #fff;
        }

        .modal-content .modal-caption {
            padding: 0 16px 12px;
            flex-shrink: 0;
        }

        .modal-content .modal-caption .caption-text {
            font-size: 14px;
            color: #ccc;
            line-height: 1.4;
            word-break: break-word;
        }

        .modal-content .modal-caption .caption-text .hashtag {
            color: #1a8cd8;
        }

        .modal-right .modal-comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px 16px;
        }

        .modal-right .modal-comments-list .modal-comment-item {
            display: flex;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.02);
        }

        .modal-right .modal-comments-list .modal-comment-item .mc-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 10px;
            color: #fff;
            flex-shrink: 0;
        }

        .modal-right .modal-comments-list .modal-comment-item .mc-body .mc-author {
            font-weight: 600;
            font-size: 12px;
            color: #fff;
        }

        .modal-right .modal-comments-list .modal-comment-item .mc-body .mc-text {
            font-size: 13px;
            color: #ccc;
        }

        .modal-right .modal-comments-list .modal-comment-item .mc-body .mc-time {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .modal-right .modal-comment-input {
            display: flex;
            gap: 10px;
            padding: 10px 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
        }

        .modal-right .modal-comment-input input {
            flex: 1;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        .modal-right .modal-comment-input input::placeholder {
            color: #444;
        }

        .modal-right .modal-comment-input button {
            padding: 10px 20px;
            background: #fff;
            border: none;
            border-radius: 10px;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
        }

        .modal-right .modal-comment-input button:active {
            opacity: 0.8;
            transform: scale(0.95);
        }

        .modal-right .empty-comments {
            color: #555;
            text-align: center;
            padding: 30px 20px;
            font-size: 14px;
        }

        /* ===== COMMENT BOTTOM SHEET (MOBILE) ===== */
        .comment-bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70vh;
            background: #0a0a0a;
            border-radius: 20px 20px 0 0;
            z-index: 100;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            padding: 20px 20px 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            touch-action: pan-y;
        }

        .comment-bottom-sheet.open {
            transform: translateY(0);
        }

        .comment-bottom-sheet .sheet-drag-handle {
            width: 40px;
            height: 4px;
            background: #333;
            border-radius: 2px;
            margin: 0 auto 12px;
            flex-shrink: 0;
            cursor: grab;
        }

        .comment-bottom-sheet .sheet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .comment-bottom-sheet .sheet-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .comment-bottom-sheet .sheet-header button {
            background: none;
            border: none;
            color: #888;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        .comment-bottom-sheet .sheet-list {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 10px;
        }

        .comment-bottom-sheet .sheet-list .comment-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        .comment-bottom-sheet .sheet-list .comment-item .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            color: #fff;
            flex-shrink: 0;
        }

        .comment-bottom-sheet .sheet-list .comment-item .comment-body .comment-author {
            font-weight: 600;
            font-size: 13px;
            color: #fff;
        }

        .comment-bottom-sheet .sheet-list .comment-item .comment-body .comment-text {
            font-size: 13px;
            color: #ccc;
            margin-top: 2px;
        }

        .comment-bottom-sheet .sheet-list .comment-item .comment-body .comment-time {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        .comment-bottom-sheet .sheet-input-area {
            display: flex;
            gap: 10px;
            padding: 10px 0 16px;
            border-top: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
            background: #0a0a0a;
        }

        .comment-bottom-sheet .sheet-input-area input {
            flex: 1;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        .comment-bottom-sheet .sheet-input-area input::placeholder {
            color: #444;
        }

        .comment-bottom-sheet .sheet-input-area button {
            padding: 10px 20px;
            background: #fff;
            border: none;
            border-radius: 10px;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
        }

        .comment-bottom-sheet .sheet-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
            display: none;
        }

        .comment-bottom-sheet .sheet-backdrop.open {
            display: block;
        }

        /* ===== COMMENT SIDEBAR (DESKTOP) ===== */
        .comment-sidebar {
            display: none;
            position: sticky;
            top: 0;
            right: 0;
            width: 380px;
            height: 100vh;
            background: #0a0a0a;
            border-left: 1px solid rgba(255,255,255,0.04);
            z-index: 100;
            padding: 20px;
            overflow-y: auto;
            flex-direction: column;
            flex-shrink: 0;
        }

        .comment-sidebar.open {
            display: flex;
        }

        .comment-sidebar .comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .comment-sidebar .comment-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .comment-sidebar .comment-header button {
            background: none;
            border: none;
            color: #888;
            font-size: 20px;
            cursor: pointer;
            padding: 4px 8px;
        }

        .comment-sidebar .comment-list {
            flex: 1;
            overflow-y: auto;
        }

        .comment-sidebar .comment-list .comment-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        .comment-sidebar .comment-list .comment-item .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            color: #fff;
            flex-shrink: 0;
        }

        .comment-sidebar .comment-list .comment-item .comment-body .comment-author {
            font-weight: 600;
            font-size: 13px;
            color: #fff;
        }

        .comment-sidebar .comment-list .comment-item .comment-body .comment-text {
            font-size: 13px;
            color: #ccc;
            margin-top: 2px;
        }

        .comment-sidebar .comment-list .comment-item .comment-body .comment-time {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        .comment-sidebar .comment-input-area {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.04);
            flex-shrink: 0;
        }

        .comment-sidebar .comment-input-area input {
            flex: 1;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        .comment-sidebar .comment-input-area input::placeholder {
            color: #444;
        }

        .comment-sidebar .comment-input-area button {
            padding: 10px 20px;
            background: #fff;
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        /* ===== SEARCH ===== */
        #searchTab {
            display: none;
        }
        #searchTab.active {
            display: block;
        }

        .search-header {
            padding: 12px 16px;
            position: sticky;
            top: 0;
            background: rgba(0,0,0,0.95);
            z-index: 5;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        .search-header .search-input {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-family: inherit;
            outline: none;
        }

        .search-header .search-input::placeholder {
            color: #444;
        }

        .search-header .search-input:focus {
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.08);
        }

        .search-results {
            padding: 8px 0;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .search-result-item:active {
            background: rgba(255,255,255,0.04);
        }

        .search-result-item .result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        .search-result-item .result-info {
            flex: 1;
        }

        .search-result-item .result-info .result-name {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }

        .search-result-item .result-info .result-email {
            font-size: 12px;
            color: #555;
        }

        .search-result-empty {
            text-align: center;
            color: #555;
            padding: 60px 20px;
            font-size: 14px;
        }

        .search-result-empty i {
            font-size: 48px;
            color: #1a1a1a;
            display: block;
            margin-bottom: 12px;
        }

        /* ===== NAVBAR ===== */
        .bottom-nav {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: space-around;
            padding: 6px 0 10px;
            z-index: 60;
            flex-shrink: 0;
            width: 100%;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            padding: 0 8px;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            background: none;
            border: none;
            color: #888;
            font-size: 10px;
            cursor: pointer;
            font-family: inherit;
            padding: 4px 8px;
            position: relative;
            transition: color 0.2s ease;
            flex: 1;
            max-width: 70px;
            min-height: 44px;
        }

        .nav-item i {
            font-size: 24px;
            transition: all 0.2s ease;
        }

        .nav-item .nav-label {
            font-size: 10px;
            color: #888;
            transition: color 0.2s ease;
            margin-top: 2px;
            display: none;
        }

        .nav-item.active i {
            color: #fff;
        }

        .nav-item.active .nav-label {
            color: #fff;
        }

        .nav-item:active {
            transform: scale(0.92);
        }

        .nav-item .nav-add i {
            background: #fff;
            color: #000;
            border-radius: 5px;
            padding: 4px 8px;
            font-size: 22px;
            margin-top: -4px;
        }

        .nav-item .badge {
            position: absolute;
            top: 0;
            right: 4px;
            background: #ff4444;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #000;
            z-index: 3;
        }

        /* ===== DESKTOP ===== */
        @media (min-width: 769px) {
            #dashboard-container {
                flex-direction: row;
                align-items: stretch;
                max-width: 100%;
            }

            .header {
                display: none;
            }

            .bottom-nav {
                position: sticky;
                top: 0;
                bottom: 0;
                left: 0;
                width: 220px;
                max-width: 220px;
                height: 100vh;
                border-radius: 0;
                border-right: 1px solid rgba(255,255,255,0.06);
                padding: 20px 0;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: flex-start;
                background: rgba(0, 0, 0, 0.95);
                order: 0;
            }

            .nav-items {
                flex-direction: column;
                align-items: flex-start;
                justify-content: flex-start;
                max-width: 100%;
                width: 100%;
                padding: 0 8px;
                gap: 4px;
                margin: 0;
            }

            .nav-item {
                flex-direction: row;
                justify-content: flex-start;
                gap: 12px;
                max-width: 100%;
                width: 100%;
                min-height: 48px;
                padding: 10px 14px;
                border-radius: 8px;
                font-size: 14px;
                color: #888;
                transition: all 0.2s ease;
            }

            .nav-item i {
                font-size: 22px;
                width: 28px;
                text-align: center;
            }

            .nav-item .nav-label {
                display: block;
                font-size: 14px;
                font-weight: 500;
                color: #888;
                margin-top: 0;
            }

            .nav-item.active {
                background: rgba(255,255,255,0.06);
            }

            .nav-item.active i {
                color: #fff;
            }

            .nav-item.active .nav-label {
                color: #fff;
            }

            .nav-item .nav-add i {
                background: #fff;
                color: #000;
                border-radius: 5px;
                padding: 4px 10px;
                font-size: 20px;
                margin-top: 0;
            }

            .nav-item .badge {
                position: relative;
                top: auto;
                right: auto;
                margin-left: auto;
                border-color: #000;
            }

            .main-wrapper {
                flex: 1;
                max-width: calc(100% - 220px);
                height: 100vh;
                order: 1;
                background: #000;
                display: flex;
            }

            .content {
                max-width: 100%;
                height: 100vh;
                padding: 0;
                background: #000;
                flex: 1;
                transition: flex 0.3s ease, max-width 0.3s ease;
            }

            .content.with-sidebar {
                flex: 0 0 calc(100% - 380px);
                max-width: calc(100% - 380px);
            }

            .post-card {
                max-width: 600px;
                margin: 0 auto;
                height: calc(100vh - 20px);
                max-height: calc(100vh - 20px);
            }

            .profile-tab-content {
                max-width: 100%;
                margin: 0;
                padding: 16px 20px 20px 20px;
            }

            .profile-header {
                max-width: 100%;
                padding: 16px 20px 0;
            }

            .profile-stats {
                max-width: 100%;
                padding: 16px 20px;
                margin: 12px 0 16px;
            }

            .profile-posts-grid {
                max-width: 100%;
                padding: 0;
                margin-top: 8px;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2px;
            }

            .profile-posts-empty {
                max-width: 100%;
                margin: 0;
                padding: 60px 20px;
            }

            .empty-state {
                max-width: 100%;
                margin: 0;
            }

            .search-header {
                padding: 12px 20px;
            }

            .search-header .search-input {
                max-width: 100%;
                margin: 0;
                font-size: 16px;
                padding: 12px 16px;
            }

            .search-results {
                max-width: 100%;
                margin: 0;
                padding: 8px 0;
            }

            .search-result-item {
                padding: 12px 20px;
            }

            .comment-bottom-sheet {
                display: none !important;
            }
            .comment-bottom-sheet .sheet-backdrop {
                display: none !important;
            }

            .comment-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                width: 380px;
                max-width: 380px;
            }

            .comment-sidebar.open {
                display: flex;
            }

            #messagesTab {
                padding-bottom: 0;
            }

            .messages-header {
                padding: 12px 20px;
            }

            .conversation-item {
                padding: 12px 20px;
            }
        }

        @media (max-width: 768px) {
            .bottom-nav {
                padding: 6px 0 10px;
                width: 100%;
            }
            .nav-items {
                max-width: 100%;
                padding: 0 8px;
            }
            .nav-item {
                max-width: 70px;
                min-height: 44px;
                font-size: 10px;
            }
            .nav-item i {
                font-size: 24px;
            }
            .nav-item .nav-label {
                display: none;
            }
            .nav-item .nav-add i {
                font-size: 22px;
                padding: 4px 8px;
            }
            .post-card {
                height: calc(100vh - 120px);
                max-height: calc(100vh - 120px);
            }
            .modal-content {
                flex-direction: column;
            }
            .modal-right {
                width: 100% !important;
                max-height: 0;
                overflow: hidden;
                border-left: none;
                border-top: 1px solid rgba(255,255,255,0.04);
                transition: max-height 0.3s ease;
            }
            .modal-right.open {
                width: 100% !important;
                max-height: 300px;
            }
            .modal-right .modal-comments-list {
                max-height: 200px;
            }
            .comment-sidebar {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .bottom-nav {
                padding: 4px 0 8px;
            }
            .nav-items {
                padding: 0 4px;
            }
            .nav-item {
                max-width: 60px;
                min-height: 40px;
            }
            .nav-item i {
                font-size: 20px;
            }
            .nav-item .nav-add i {
                font-size: 18px;
                padding: 3px 7px;
            }
            .post-card {
                height: calc(100vh - 110px);
                max-height: calc(100vh - 110px);
            }
            .modal-content {
                max-height: 100vh;
                border-radius: 0;
                max-width: 100%;
            }
            .modal-content .modal-image-wrapper .modal-slide-track .modal-slide {
                min-height: 200px;
            }
            .modal-content .modal-image-wrapper .modal-slide-track .modal-slide img {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>

<div id="dashboard-container">
    <header class="header">
        <h1>Instant Store</h1>
    </header>

    <div class="main-wrapper">
        <div class="content" id="contentArea">
            <!-- HOME TAB -->
            <div id="homeTab">
                <div id="postsContainer"></div>
                <div id="emptyState" style="display:none;" class="empty-state">
                    <i class="fas fa-store-alt"></i>
                    <h2>No Posts Yet</h2>
                    <p>Be the first to share your business or services</p>
                </div>
            </div>

            <!-- SEARCH TAB -->
            <div id="searchTab">
                <div class="search-header">
                    <input class="search-input" id="searchInput" type="text" placeholder="Search posts or users (#username to find user)...">
                </div>
                <div class="search-results" id="searchResults">
                    <div class="search-result-empty">
                        <i class="fas fa-search"></i>
                        Search for posts, users, and more
                    </div>
                </div>
            </div>

            <!-- MESSAGES TAB -->
            <div id="messagesTab">
                <div class="messages-header">
                    <h2>Messages</h2>
                </div>
                <div id="conversationsList"></div>
                <div id="messagesEmpty" class="messages-empty">
                    <i class="fas fa-comment-dots"></i>
                    <h2>No Messages</h2>
                    <p>Start a conversation with other users</p>
                </div>
            </div>

            <!-- PROFILE TAB -->
            <div id="profileTab">
                <div class="profile-tab-content">
                    <div class="profile-header">
                        <div class="profile-avatar" id="profileAvatar" style="<?php echo $userAvatar ? 'background-image: url(' . $userAvatar . ');' : ''; ?>">
                            <?php echo $userAvatar ? '' : strtoupper(substr($userName, 0, 1)); ?>
                        </div>
                        <div class="profile-name-section">
                            <div class="profile-display-name"><?php echo htmlspecialchars($userName); ?></div>
                            <div class="profile-username">@<?php echo explode('@', $userEmail)[0]; ?></div>
                            <div class="profile-bio" id="profileBio"><?php echo htmlspecialchars($userBio); ?></div>
                            <div class="profile-actions">
                                <button class="btn-edit" id="editProfileBtn"><i class="fas fa-pen"></i> Edit</button>
                                <button class="btn-share" id="shareProfileBtn"><i class="fas fa-share"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="profile-stats">
                        <div class="stat">
                            <div class="number" id="profilePostCount">0</div>
                            <div class="label">Posts</div>
                        </div>
                        <div class="stat">
                            <div class="number">0</div>
                            <div class="label">Followers</div>
                        </div>
                        <div class="stat">
                            <div class="number">0</div>
                            <div class="label">Following</div>
                        </div>
                    </div>

                    <div id="profilePostsGrid" class="profile-posts-grid"></div>
                    <div id="profileEmptyState" style="display:none;" class="profile-posts-empty">
                        <i class="fas fa-store-alt"></i>
                        No posts yet
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== MODAL POST (PROFILE) ===== -->
        <div class="modal-overlay" id="modalOverlay">
            <div class="modal-content" id="modalContent">
                <div class="modal-left">
                    <div class="modal-header">
                        <div class="modal-user">
                            <div class="modal-avatar" id="modalAvatar"></div>
                            <span class="modal-name" id="modalAuthor">User</span>
                        </div>
                        <button class="modal-close" id="modalClose"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-image-wrapper" id="modalWrapper">
                        <div class="modal-slide-track" id="modalTrack"></div>
                        <div class="modal-slide-indicators" id="modalIndicators"></div>
                    </div>
                    <div class="modal-actions">
                        <button class="like-btn" id="modalLikeBtn"><i class="far fa-heart"></i></button>
                        <span class="like-count" id="modalLikeCount">0</span>
                        <button class="comment-toggle-btn" id="modalCommentToggle"><i class="far fa-comment"></i></button>
                        <button style="margin-left:auto;"><i class="far fa-paper-plane"></i></button>
                    </div>
                    <div class="modal-caption">
                        <div class="caption-text" id="modalCaption"></div>
                    </div>
                </div>
                <div class="modal-right" id="modalRight">
                    <div class="modal-comments-header">
                        <h4>Comments</h4>
                        <button id="modalCommentClose"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-comments-list" id="modalComments">
                        <div class="empty-comments">No comments yet</div>
                    </div>
                    <div class="modal-comment-input">
                        <input type="text" id="modalCommentInput" placeholder="Write a comment...">
                        <button id="modalCommentPost">Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== COMMENT SIDEBAR (DESKTOP) ===== -->
        <div class="comment-sidebar" id="commentSidebar">
            <div class="comment-header">
                <h3>Comments</h3>
                <button id="closeCommentSidebar"><i class="fas fa-times"></i></button>
            </div>
            <div class="comment-list" id="commentListSidebar"></div>
            <div class="comment-input-area">
                <input type="text" id="commentInputSidebar" placeholder="Write a comment...">
                <button id="postCommentSidebar">Post</button>
            </div>
        </div>

        <!-- ===== COMMENT BOTTOM SHEET (MOBILE) ===== -->
        <div class="comment-bottom-sheet" id="commentBottomSheet">
            <div class="sheet-backdrop" id="sheetBackdrop"></div>
            <div class="sheet-drag-handle" id="sheetDragHandle"></div>
            <div class="sheet-header">
                <h3>Comments</h3>
                <button id="closeSheetBtn"><i class="fas fa-times"></i></button>
            </div>
            <div class="sheet-list" id="sheetList"></div>
            <div class="sheet-input-area">
                <input type="text" id="sheetCommentInput" placeholder="Write a comment...">
                <button id="sheetCommentPost">Post</button>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="bottom-nav">
        <div class="nav-items">
            <button class="nav-item active" data-tab="home" id="navHome">
                <i class="fas fa-home"></i>
                <span class="nav-label">Home</span>
            </button>
            <button class="nav-item" data-tab="search" id="navSearch">
                <i class="fas fa-search"></i>
                <span class="nav-label">Search</span>
            </button>
            <button class="nav-item" data-tab="add" id="navAdd">
                <span class="nav-add"><i class="fas fa-plus-circle"></i></span>
                <span class="nav-label">Add</span>
            </button>
            <button class="nav-item" data-tab="messages" id="navMessages">
                <i class="fas fa-envelope"></i>
                <span class="nav-label">Messages</span>
                <?php if ($unreadCount > 0): ?>
                <span class="badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </button>
            <button class="nav-item" data-tab="profile" id="navProfile">
                <i class="fas fa-user"></i>
                <span class="nav-label">Profile</span>
            </button>
        </div>
    </nav>
</div>

<script>
    (function() {
        const homeTab = document.getElementById('homeTab');
        const searchTab = document.getElementById('searchTab');
        const messagesTab = document.getElementById('messagesTab');
        const profileTab = document.getElementById('profileTab');
        const postsContainer = document.getElementById('postsContainer');
        const searchResults = document.getElementById('searchResults');
        const searchInput = document.getElementById('searchInput');
        const emptyState = document.getElementById('emptyState');
        const contentArea = document.getElementById('contentArea');
        const navItems = document.querySelectorAll('.nav-item');
        const profilePostsGrid = document.getElementById('profilePostsGrid');
        const profileEmptyState = document.getElementById('profileEmptyState');
        const profilePostCount = document.getElementById('profilePostCount');

        // Conversations
        const conversationsList = document.getElementById('conversationsList');
        const messagesEmpty = document.getElementById('messagesEmpty');

        // Modal elements (for profile only)
        const modalOverlay = document.getElementById('modalOverlay');
        const modalClose = document.getElementById('modalClose');
        const modalAvatar = document.getElementById('modalAvatar');
        const modalAuthor = document.getElementById('modalAuthor');
        const modalTrack = document.getElementById('modalTrack');
        const modalIndicators = document.getElementById('modalIndicators');
        const modalCaption = document.getElementById('modalCaption');
        const modalLikeBtn = document.getElementById('modalLikeBtn');
        const modalLikeCount = document.getElementById('modalLikeCount');
        const modalCommentToggle = document.getElementById('modalCommentToggle');
        const modalCommentClose = document.getElementById('modalCommentClose');
        const modalWrapper = document.getElementById('modalWrapper');
        const modalRight = document.getElementById('modalRight');
        const modalComments = document.getElementById('modalComments');
        const modalCommentInput = document.getElementById('modalCommentInput');
        const modalCommentPost = document.getElementById('modalCommentPost');

        // Comment sidebar (desktop)
        const commentSidebar = document.getElementById('commentSidebar');
        const commentListSidebar = document.getElementById('commentListSidebar');
        const commentInputSidebar = document.getElementById('commentInputSidebar');
        const postCommentSidebar = document.getElementById('postCommentSidebar');
        const closeSidebarBtn = document.getElementById('closeCommentSidebar');

        // Comment bottom sheet (mobile)
        const bottomSheet = document.getElementById('commentBottomSheet');
        const sheetList = document.getElementById('sheetList');
        const sheetInput = document.getElementById('sheetCommentInput');
        const sheetPostBtn = document.getElementById('sheetCommentPost');
        const sheetCloseBtn = document.getElementById('closeSheetBtn');
        const sheetBackdrop = document.getElementById('sheetBackdrop');
        const sheetDragHandle = document.getElementById('sheetDragHandle');

        let posts = <?php echo json_encode($posts); ?>;
        let comments = <?php echo json_encode($comments); ?>;
        let currentTab = 'home';
        let isDesktop = window.innerWidth >= 769;
        let currentModalIndex = -1;
        let currentSlide = 0;
        let isModalOpen = false;
        let commentsOpen = false;
        let currentPostId = null;
        let sheetOpen = false;
        let touchStartY = 0;

        // ===== MESSAGES =====
        let conversations = {};
        let allUsers = <?php echo json_encode($allUsers); ?>;
        let messages = <?php echo json_encode($messages); ?>;
        let unreadCount = <?php echo $unreadCount; ?>;

        function buildConversations() {
            const newConvs = {};
            messages.forEach(msg => {
                if (msg.from === '<?php echo addslashes($userName); ?>' || msg.to === '<?php echo addslashes($userName); ?>') {
                    const other = msg.from === '<?php echo addslashes($userName); ?>' ? msg.to : msg.from;
                    if (!newConvs[other]) {
                        newConvs[other] = {
                            lastMessage: msg.text,
                            time: msg.time,
                            unread: msg.to === '<?php echo addslashes($userName); ?>' && !msg.read
                        };
                    } else {
                        if (new Date(msg.time) > new Date(newConvs[other].time)) {
                            newConvs[other].lastMessage = msg.text;
                            newConvs[other].time = msg.time;
                        }
                        if (msg.to === '<?php echo addslashes($userName); ?>' && !msg.read) {
                            newConvs[other].unread = true;
                        }
                    }
                }
            });
            conversations = newConvs;
        }
        buildConversations();

        function renderConversations() {
            if (!conversationsList) return;
            conversationsList.innerHTML = '';
            const keys = Object.keys(conversations);
            
            if (keys.length === 0) {
                if (messagesEmpty) messagesEmpty.style.display = 'flex';
                if (conversationsList) conversationsList.style.display = 'none';
                return;
            }

            if (messagesEmpty) messagesEmpty.style.display = 'none';
            if (conversationsList) conversationsList.style.display = 'block';

            // Sort by time
            keys.sort((a, b) => {
                return new Date(conversations[b].time) - new Date(conversations[a].time);
            });

            keys.forEach(other => {
                const conv = conversations[other];
                const div = document.createElement('div');
                div.className = 'conversation-item';
                const avatarStyle = allUsers[other]?.avatar ? `background-image: url('${allUsers[other].avatar}');` : '';
                const initial = other ? other.charAt(0).toUpperCase() : '?';
                
                div.innerHTML = `
                    <div class="conv-avatar" style="${avatarStyle}">${avatarStyle ? '' : initial}</div>
                    <div class="conv-info">
                        <div class="conv-name">${other}</div>
                        <div class="conv-last">${conv.lastMessage || ''}</div>
                    </div>
                    <div class="conv-time">${conv.time ? new Date(conv.time).toLocaleDateString() : ''}</div>
                    ${conv.unread ? '<div class="conv-unread"></div>' : ''}
                `;
                
                div.addEventListener('click', function() {
                    window.location.href = 'chat.php?user=' + encodeURIComponent(other);
                });
                
                conversationsList.appendChild(div);
            });
        }

        // ===== HOME COMMENT SIDEBAR (DESKTOP) =====
        function renderCommentsSidebar(postId) {
            commentListSidebar.innerHTML = '';
            const postComments = comments.filter(c => c.postId === postId);
            if (postComments.length === 0) {
                commentListSidebar.innerHTML = '<div style="color:#555;text-align:center;padding:20px;">No comments yet. Be the first!</div>';
                return;
            }
            postComments.forEach(comment => {
                const div = document.createElement('div');
                div.className = 'comment-item';
                const initial = comment.author ? comment.author.charAt(0).toUpperCase() : '?';
                div.innerHTML = `
                    <div class="comment-avatar">${initial}</div>
                    <div class="comment-body">
                        <div class="comment-author">${comment.author || 'User'}</div>
                        <div class="comment-text">${comment.text}</div>
                        <div class="comment-time">${comment.time || 'Just now'}</div>
                    </div>
                `;
                commentListSidebar.appendChild(div);
            });
        }

        function openHomeComments(postId) {
            currentPostId = postId;
            if (isDesktop) {
                commentSidebar.classList.add('open');
                contentArea.classList.add('with-sidebar');
                renderCommentsSidebar(postId);
            } else {
                // Mobile: bottom sheet
                renderSheetComments(postId);
                bottomSheet.classList.add('open');
                sheetBackdrop.classList.add('open');
                sheetOpen = true;
                document.body.style.overflow = 'hidden';
            }
        }

        function closeHomeComments() {
            if (isDesktop) {
                commentSidebar.classList.remove('open');
                contentArea.classList.remove('with-sidebar');
            } else {
                bottomSheet.classList.remove('open');
                sheetBackdrop.classList.remove('open');
                sheetOpen = false;
                document.body.style.overflow = '';
            }
            currentPostId = null;
        }

        function postCommentSidebarFn() {
            if (!currentPostId) return;
            const text = commentInputSidebar.value.trim();
            if (!text) return;
            const newComment = {
                id: 'c_' + Date.now(),
                postId: currentPostId,
                author: '<?php echo addslashes($userName); ?>',
                text: text,
                time: new Date().toLocaleString()
            };
            comments.push(newComment);
            fetch('backend/save_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newComment)
            });
            renderCommentsSidebar(currentPostId);
            commentInputSidebar.value = '';
        }

        // ===== COMMENT BOTTOM SHEET (MOBILE) =====
        function renderSheetComments(postId) {
            sheetList.innerHTML = '';
            const postComments = comments.filter(c => c.postId === postId);
            if (postComments.length === 0) {
                sheetList.innerHTML = '<div style="color:#555;text-align:center;padding:20px;">No comments yet. Be the first!</div>';
                return;
            }
            postComments.forEach(comment => {
                const div = document.createElement('div');
                div.className = 'comment-item';
                const initial = comment.author ? comment.author.charAt(0).toUpperCase() : '?';
                div.innerHTML = `
                    <div class="comment-avatar">${initial}</div>
                    <div class="comment-body">
                        <div class="comment-author">${comment.author || 'User'}</div>
                        <div class="comment-text">${comment.text}</div>
                        <div class="comment-time">${comment.time || 'Just now'}</div>
                    </div>
                `;
                sheetList.appendChild(div);
            });
        }

        function postSheetComment() {
            if (!currentPostId) return;
            const text = sheetInput.value.trim();
            if (!text) return;
            const newComment = {
                id: 'c_' + Date.now(),
                postId: currentPostId,
                author: '<?php echo addslashes($userName); ?>',
                text: text,
                time: new Date().toLocaleString()
            };
            comments.push(newComment);
            fetch('backend/save_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newComment)
            });
            renderSheetComments(currentPostId);
            sheetInput.value = '';
        }

        // Sheet drag to close
        let sheetStartY = 0;
        let sheetCurrentY = 0;
        let isSheetDragging = false;

        bottomSheet.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                sheetStartY = e.touches[0].clientY;
                isSheetDragging = true;
            }
        }, { passive: true });

        bottomSheet.addEventListener('touchmove', function(e) {
            if (!isSheetDragging || e.touches.length !== 1) return;
            sheetCurrentY = e.touches[0].clientY;
            const diff = sheetCurrentY - sheetStartY;
            if (diff > 0) {
                bottomSheet.style.transform = 'translateY(' + diff + 'px)';
            }
        }, { passive: true });

        bottomSheet.addEventListener('touchend', function(e) {
            if (!isSheetDragging) return;
            isSheetDragging = false;
            const diff = sheetCurrentY - sheetStartY;
            bottomSheet.style.transition = 'transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            if (diff > 100) {
                closeHomeComments();
            } else {
                bottomSheet.style.transform = 'translateY(0)';
            }
            setTimeout(() => {
                bottomSheet.style.transition = '';
            }, 300);
        }, { passive: true });

        if (sheetCloseBtn) sheetCloseBtn.addEventListener('click', closeHomeComments);
        if (sheetBackdrop) sheetBackdrop.addEventListener('click', closeHomeComments);
        if (sheetPostBtn) sheetPostBtn.addEventListener('click', postSheetComment);
        if (sheetInput) {
            sheetInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') postSheetComment();
            });
        }

        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeHomeComments);
        if (postCommentSidebar) postCommentSidebar.addEventListener('click', postCommentSidebarFn);
        if (commentInputSidebar) {
            commentInputSidebar.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') postCommentSidebarFn();
            });
        }

        // ===== PROFILE MODAL =====
        function renderCommentsModal(postId) {
            const postComments = comments.filter(c => c.postId === postId);
            modalComments.innerHTML = '';
            if (postComments.length === 0) {
                modalComments.innerHTML = '<div class="empty-comments">No comments yet</div>';
                return;
            }
            postComments.forEach(comment => {
                const div = document.createElement('div');
                div.className = 'modal-comment-item';
                const initial = comment.author ? comment.author.charAt(0).toUpperCase() : '?';
                div.innerHTML = `
                    <div class="mc-avatar">${initial}</div>
                    <div class="mc-body">
                        <div class="mc-author">${comment.author || 'User'}</div>
                        <div class="mc-text">${comment.text}</div>
                        <div class="mc-time">${comment.time || 'Just now'}</div>
                    </div>
                `;
                modalComments.appendChild(div);
            });
        }

        function toggleComments() {
            commentsOpen = !commentsOpen;
            if (modalRight) modalRight.classList.toggle('open', commentsOpen);
            if (modalCommentToggle) modalCommentToggle.classList.toggle('active', commentsOpen);
        }

        function openModal(index) {
            const post = posts[index];
            if (!post) return;
            
            currentModalIndex = index;
            currentSlide = 0;
            isModalOpen = true;
            commentsOpen = false;
            if (modalRight) modalRight.classList.remove('open');
            if (modalCommentToggle) modalCommentToggle.classList.remove('active');
            
            const avatarStyle = post.avatar ? 'background-image: url(' + post.avatar + ');' : '';
            if (modalAvatar) {
                modalAvatar.style.cssText = avatarStyle + ' display:flex; align-items:center; justify-content:center;';
                modalAvatar.textContent = post.avatar ? '' : (post.author ? post.author.charAt(0).toUpperCase() : '?');
            }
            if (modalAuthor) modalAuthor.textContent = post.author || 'User';
            
            if (modalTrack) modalTrack.innerHTML = '';
            if (modalIndicators) modalIndicators.innerHTML = '';
            
            if (post.images && post.images.length > 0) {
                post.images.forEach((img, i) => {
                    const slide = document.createElement('div');
                    slide.className = 'modal-slide';
                    slide.innerHTML = '<img src="' + img + '" alt="slide ' + (i+1) + '" loading="lazy">';
                    if (modalTrack) modalTrack.appendChild(slide);
                    
                    if (post.images.length > 1) {
                        const dot = document.createElement('span');
                        dot.className = 'dot' + (i === 0 ? ' active' : '');
                        if (modalIndicators) modalIndicators.appendChild(dot);
                    }
                });
                
                const captionHtml = post.caption ? post.caption.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>') : '';
                if (modalCaption) modalCaption.innerHTML = captionHtml;
                
                const isLiked = post.liked || false;
                if (modalLikeBtn) {
                    modalLikeBtn.innerHTML = '<i class="' + (isLiked ? 'fas' : 'far') + ' fa-heart"></i>';
                    modalLikeBtn.classList.toggle('liked', isLiked);
                }
                if (modalLikeCount) modalLikeCount.textContent = post.likes || 0;
                
                renderCommentsModal(post.id);
                updateModalSlide();
            }
            
            if (modalOverlay) modalOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (modalOverlay) modalOverlay.classList.remove('show');
            document.body.style.overflow = '';
            isModalOpen = false;
            commentsOpen = false;
            if (modalRight) modalRight.classList.remove('open');
            if (modalCommentToggle) modalCommentToggle.classList.remove('active');
            currentModalIndex = -1;
        }

        function updateModalSlide() {
            const post = posts[currentModalIndex];
            if (!post) return;
            const slideWidth = 100;
            if (modalTrack) {
                modalTrack.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                modalTrack.style.transform = 'translateX(-' + (currentSlide * slideWidth) + '%)';
            }
            const dots = modalIndicators ? modalIndicators.querySelectorAll('.dot') : [];
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlide);
            });
        }

        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        }
        if (modalCommentToggle) modalCommentToggle.addEventListener('click', toggleComments);
        if (modalCommentClose) modalCommentClose.addEventListener('click', toggleComments);

        if (modalCommentPost) {
            modalCommentPost.addEventListener('click', function() {
                if (currentModalIndex === -1) return;
                const post = posts[currentModalIndex];
                const text = modalCommentInput ? modalCommentInput.value.trim() : '';
                if (!text) return;
                const newComment = {
                    id: 'c_' + Date.now(),
                    postId: post.id,
                    author: '<?php echo addslashes($userName); ?>',
                    text: text,
                    time: new Date().toLocaleString()
                };
                comments.push(newComment);
                fetch('backend/save_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newComment)
                });
                renderCommentsModal(post.id);
                if (modalCommentInput) modalCommentInput.value = '';
            });
        }

        if (modalCommentInput) {
            modalCommentInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && modalCommentPost) modalCommentPost.click();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (isModalOpen) {
                const post = posts[currentModalIndex];
                if (!post || !post.images || post.images.length <= 1) {
                    if (e.key === 'Escape') closeModal();
                    return;
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    if (currentSlide < post.images.length - 1) { currentSlide++; updateModalSlide(); }
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    if (currentSlide > 0) { currentSlide--; updateModalSlide(); }
                } else if (e.key === 'Escape') {
                    closeModal();
                }
            }
        });

        let touchStartX = 0;
        if (modalWrapper) {
            modalWrapper.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1) touchStartX = e.touches[0].clientX;
            }, { passive: true });

            modalWrapper.addEventListener('touchend', function(e) {
                if (!isModalOpen) return;
                const post = posts[currentModalIndex];
                if (!post || !post.images || post.images.length <= 1) return;
                const diff = touchStartX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0 && currentSlide < post.images.length - 1) { currentSlide++; updateModalSlide(); }
                    else if (diff < 0 && currentSlide > 0) { currentSlide--; updateModalSlide(); }
                }
            }, { passive: true });
        }

        if (modalLikeBtn) {
            modalLikeBtn.addEventListener('click', function() {
                if (currentModalIndex === -1) return;
                const post = posts[currentModalIndex];
                post.liked = !post.liked;
                post.likes = (post.likes || 0) + (post.liked ? 1 : -1);
                this.innerHTML = '<i class="' + (post.liked ? 'fas' : 'far') + ' fa-heart"></i>';
                this.classList.toggle('liked', post.liked);
                if (modalLikeCount) modalLikeCount.textContent = post.likes || 0;
                fetch('backend/update_post.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ index: currentModalIndex, liked: post.liked, likes: post.likes })
                });
            });
        }

        function renderProfileGrid() {
            if (!profilePostsGrid) return;
            profilePostsGrid.innerHTML = '';
            const userPosts = posts.filter(p => p.author === '<?php echo addslashes($userName); ?>');
            if (userPosts.length === 0) {
                if (profileEmptyState) profileEmptyState.style.display = 'block';
                if (profilePostsGrid) profilePostsGrid.style.display = 'none';
                if (profilePostCount) profilePostCount.textContent = '0';
                return;
            }
            if (profileEmptyState) profileEmptyState.style.display = 'none';
            if (profilePostsGrid) profilePostsGrid.style.display = 'grid';
            if (profilePostCount) profilePostCount.textContent = userPosts.length;
            userPosts.forEach((post) => {
                const div = document.createElement('div');
                div.className = 'grid-item';
                const imgSrc = post.images && post.images.length > 0 ? post.images[0] : '';
                const commentCount = comments.filter(c => c.postId === post.id).length;
                div.innerHTML = '<img src="' + imgSrc + '" alt="post" loading="lazy" onerror="this.style.display=\'none\'">' +
                    '<div class="grid-overlay">' +
                        '<span><i class="fas fa-heart"></i> ' + (post.likes || 0) + '</span>' +
                        '<span><i class="fas fa-comment"></i> ' + commentCount + '</span>' +
                    '</div>';
                const realIndex = posts.findIndex(p => p.id === post.id);
                div.addEventListener('click', function() { 
                    openModal(realIndex);
                });
                if (profilePostsGrid) profilePostsGrid.appendChild(div);
            });
        }

        function renderPosts() {
            if (!postsContainer) return;
            postsContainer.innerHTML = '';
            if (posts.length === 0) {
                if (emptyState) emptyState.style.display = 'flex';
                if (postsContainer) postsContainer.style.display = 'none';
                return;
            }
            if (emptyState) emptyState.style.display = 'none';
            if (postsContainer) postsContainer.style.display = 'block';

            posts.forEach((post, index) => {
                const card = document.createElement('div');
                card.className = 'post-card';
                card.dataset.index = index;
                const avatarStyle = post.avatar ? 'background-image: url(' + post.avatar + ');' : '';
                const initial = post.author ? post.author.charAt(0).toUpperCase() : '?';
                let imagesHtml = '';
                if (post.images && post.images.length > 0) {
                    const activeIndex = post.currentSlide || 0;
                    imagesHtml = '<div class="post-image-wrapper" data-index="' + index + '">' +
                        '<div class="slide-track" style="transform: translateX(-' + (activeIndex * 100) + '%);">' +
                        post.images.map((img, i) => '<div class="slide"><img src="' + img + '" alt="slide ' + (i+1) + '" loading="lazy"></div>').join('') +
                        '</div>' +
                        (post.images.length > 1 ? '<div class="slide-indicators">' + post.images.map((_, i) => '<span class="dot ' + (i === activeIndex ? 'active' : '') + '"></span>').join('') + '</div>' : '') +
                        '</div>';
                }
                const captionHtml = post.caption ? post.caption.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>') : '';
                card.innerHTML = '<div class="post-header">' +
                    '<div class="post-avatar" style="' + avatarStyle + '">' + (avatarStyle ? '' : initial) + '</div>' +
                    '<span class="post-author">' + (post.author || 'User') + '</span>' +
                    '<span class="post-time">' + (post.time || 'Just now') + '</span>' +
                    '</div>' +
                    imagesHtml +
                    '<div class="post-actions">' +
                        '<button class="like-btn" data-index="' + index + '"><i class="' + (post.liked ? 'fas' : 'far') + ' fa-heart"></i></button>' +
                        '<span class="like-count">' + (post.likes || 0) + '</span>' +
                        '<button class="comment-btn" data-index="' + index + '" data-postid="' + post.id + '"><i class="far fa-comment"></i></button>' +
                        '<button class="share-btn" style="margin-left:auto;"><i class="far fa-paper-plane"></i></button>' +
                    '</div>' +
                    '<div class="post-caption"><div class="caption-text">' + captionHtml + '</div></div>';

                const likeBtn = card.querySelector('.like-btn');
                const likeCount = card.querySelector('.like-count');
                if (likeBtn) {
                    likeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const idx = parseInt(this.dataset.index);
                        posts[idx].liked = !posts[idx].liked;
                        posts[idx].likes = (posts[idx].likes || 0) + (posts[idx].liked ? 1 : -1);
                        this.querySelector('i').className = posts[idx].liked ? 'fas fa-heart' : 'far fa-heart';
                        this.classList.toggle('liked', posts[idx].liked);
                        if (likeCount) likeCount.textContent = posts[idx].likes;
                        fetch('backend/update_post.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ index: idx, liked: posts[idx].liked, likes: posts[idx].likes })
                        });
                    });
                }

                const commentBtn = card.querySelector('.comment-btn');
                if (commentBtn) {
                    commentBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const postId = this.dataset.postid;
                        openHomeComments(postId);
                    });
                }

                const wrapper = card.querySelector('.post-image-wrapper');
                const track = card.querySelector('.slide-track');
                if (wrapper && track && post.images && post.images.length > 1) {
                    let startX = 0, currentX = 0, isDragging = false, startTime = 0;
                    wrapper.addEventListener('touchstart', function(e) {
                        if (e.touches.length === 1) {
                            startX = e.touches[0].clientX;
                            startTime = Date.now();
                            isDragging = true;
                            track.style.transition = 'none';
                        }
                    }, { passive: true });
                    wrapper.addEventListener('touchmove', function(e) {
                        if (!isDragging || e.touches.length !== 1) return;
                        currentX = e.touches[0].clientX;
                        const diff = currentX - startX;
                        const idx = parseInt(wrapper.dataset.index);
                        const currentSlide = posts[idx].currentSlide || 0;
                        const offset = -currentSlide * 100 + (diff / wrapper.offsetWidth) * 100;
                        track.style.transform = 'translateX(' + offset + '%)';
                    }, { passive: true });
                    wrapper.addEventListener('touchend', function(e) {
                        if (!isDragging) return;
                        isDragging = false;
                        const diff = currentX - startX;
                        const idx = parseInt(wrapper.dataset.index);
                        const total = post.images.length;
                        const currentSlide = posts[idx].currentSlide || 0;
                        let newSlide = currentSlide;
                        if (Math.abs(diff) > 50 || (Math.abs(diff) > 20 && Date.now() - startTime < 300)) {
                            if (diff < 0 && currentSlide < total - 1) newSlide = currentSlide + 1;
                            else if (diff > 0 && currentSlide > 0) newSlide = currentSlide - 1;
                        }
                        posts[idx].currentSlide = newSlide;
                        track.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        track.style.transform = 'translateX(-' + (newSlide * 100) + '%)';
                        const dots = wrapper.querySelectorAll('.dot');
                        dots.forEach((dot, i) => { dot.classList.toggle('active', i === newSlide); });
                        fetch('backend/update_post.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ index: idx, currentSlide: newSlide })
                        });
                    }, { passive: true });
                }

                if (isDesktop && post.images && post.images.length > 1) {
                    card.setAttribute('tabindex', '0');
                    card.addEventListener('keydown', function(e) {
                        if (!this.contains(document.activeElement)) return;
                        const idx = parseInt(this.dataset.index);
                        const total = posts[idx].images.length;
                        const currentSlide = posts[idx].currentSlide || 0;
                        const track = this.querySelector('.slide-track');
                        if (!track) return;
                        if (e.key === 'd' || e.key === 'D') {
                            e.preventDefault();
                            if (currentSlide < total - 1) {
                                posts[idx].currentSlide = currentSlide + 1;
                                track.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                                track.style.transform = 'translateX(-' + (posts[idx].currentSlide * 100) + '%)';
                                const dots = this.querySelectorAll('.dot');
                                dots.forEach((dot, i) => { dot.classList.toggle('active', i === posts[idx].currentSlide); });
                                fetch('backend/update_post.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ index: idx, currentSlide: posts[idx].currentSlide })
                                });
                            }
                        } else if (e.key === 'a' || e.key === 'A') {
                            e.preventDefault();
                            if (currentSlide > 0) {
                                posts[idx].currentSlide = currentSlide - 1;
                                track.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                                track.style.transform = 'translateX(-' + (posts[idx].currentSlide * 100) + '%)';
                                const dots = this.querySelectorAll('.dot');
                                dots.forEach((dot, i) => { dot.classList.toggle('active', i === posts[idx].currentSlide); });
                                fetch('backend/update_post.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ index: idx, currentSlide: posts[idx].currentSlide })
                                });
                            }
                        }
                    });
                }

                if (postsContainer) postsContainer.appendChild(card);
            });
            renderProfileGrid();
        }

        // ===== SEARCH =====
        function performSearch(query) {
            if (!searchResults) return;
            searchResults.innerHTML = '';
            const q = query.trim();
            if (!q) {
                searchResults.innerHTML = '<div class="search-result-empty"><i class="fas fa-search"></i>Search for posts, users, and more</div>';
                return;
            }

            let isUserSearch = false;
            let searchUsername = '';
            if (q.startsWith('#')) {
                isUserSearch = true;
                searchUsername = q.substring(1).toLowerCase();
            }

            let results = [];

            if (isUserSearch) {
                const allUsers = {};
                posts.forEach(p => {
                    if (p.author && !allUsers[p.author]) {
                        allUsers[p.author] = { author: p.author, avatar: p.avatar || '', email: p.email || '' };
                    }
                });
                const userResults = Object.values(allUsers).filter(u => 
                    u.author.toLowerCase().includes(searchUsername)
                );
                if (userResults.length > 0) {
                    results = userResults.map(u => ({ 
                        type: 'user', 
                        author: u.author, 
                        avatar: u.avatar,
                        email: u.email 
                    }));
                } else {
                    searchResults.innerHTML = '<div class="search-result-empty"><i class="fas fa-search"></i>No user found for "' + searchUsername + '"</div>';
                    return;
                }
            } else {
                results = posts.filter(p => {
                    const captionMatch = p.caption && p.caption.toLowerCase().includes(q.toLowerCase());
                    const hashtagMatch = p.caption && p.caption.toLowerCase().includes('#' + q.toLowerCase());
                    const authorMatch = p.author && p.author.toLowerCase().includes(q.toLowerCase());
                    return captionMatch || hashtagMatch || authorMatch;
                }).map(p => ({ type: 'post', ...p }));
            }

            if (results.length === 0) {
                searchResults.innerHTML = '<div class="search-result-empty"><i class="fas fa-search"></i>No results found for "' + q + '"</div>';
                return;
            }

            if (isUserSearch) {
                const label = document.createElement('div');
                label.style.cssText = 'color:#666;font-size:12px;padding:8px 20px;border-bottom:1px solid rgba(255,255,255,0.03);';
                label.textContent = 'USERS';
                searchResults.appendChild(label);
            } else {
                const label = document.createElement('div');
                label.style.cssText = 'color:#666;font-size:12px;padding:8px 20px;border-bottom:1px solid rgba(255,255,255,0.03);';
                label.textContent = 'POSTS';
                searchResults.appendChild(label);
            }

            results.forEach((item) => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                
                if (item.type === 'user') {
                    const avatarStyle = item.avatar ? 'background-image: url(' + item.avatar + ');' : '';
                    const initial = item.author ? item.author.charAt(0).toUpperCase() : '?';
                    div.innerHTML = `
                        <div class="result-avatar" style="${avatarStyle}">${avatarStyle ? '' : initial}</div>
                        <div class="result-info">
                            <div class="result-name">${item.author || 'User'}</div>
                            <div class="result-email">${item.email || ''}</div>
                        </div>
                    `;
                    div.addEventListener('click', function() {
                        alert('User: ' + item.author);
                        switchTab('profile');
                    });
                } else {
                    const avatarStyle = item.avatar ? 'background-image: url(' + item.avatar + ');' : '';
                    const initial = item.author ? item.author.charAt(0).toUpperCase() : '?';
                    div.innerHTML = `
                        <div class="result-avatar" style="${avatarStyle}">${avatarStyle ? '' : initial}</div>
                        <div class="result-info">
                            <div class="result-name">${item.author || 'User'}</div>
                            <div class="result-email">${item.caption ? item.caption.substring(0, 60) + (item.caption.length > 60 ? '...' : '') : ''}</div>
                        </div>
                    `;
                    const realIndex = posts.findIndex(p => p.id === item.id);
                    div.addEventListener('click', function() { openModal(realIndex); });
                }
                searchResults.appendChild(div);
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                performSearch(this.value);
            });
        }

        function switchTab(tab) {
            currentTab = tab;
            if (homeTab) homeTab.style.display = tab === 'home' ? 'block' : 'none';
            if (searchTab) searchTab.classList.toggle('active', tab === 'search');
            if (messagesTab) messagesTab.classList.toggle('active', tab === 'messages');
            if (profileTab) profileTab.classList.toggle('active', tab === 'profile');
            navItems.forEach(item => { item.classList.toggle('active', item.dataset.tab === tab); });
            if (contentArea) contentArea.scrollTop = 0;
            if (tab === 'add') window.location.href = 'post.php';
            else if (tab === 'search') { 
                if (searchInput) searchInput.value = ''; 
                if (searchResults) searchResults.innerHTML = '<div class="search-result-empty"><i class="fas fa-search"></i>Search for posts, users, and more</div>'; 
                if (searchInput) searchInput.focus(); 
            }
            else if (tab === 'messages') {
                renderConversations();
            }
            if (tab === 'profile') renderProfileGrid();
        }

        navItems.forEach(item => {
            item.addEventListener('click', function() { switchTab(this.dataset.tab); });
        });

        if (document.getElementById('editProfileBtn')) {
            document.getElementById('editProfileBtn').addEventListener('click', function() {
                window.location.href = 'edit.php';
            });
        }
        if (document.getElementById('shareProfileBtn')) {
            document.getElementById('shareProfileBtn').addEventListener('click', function() {
                alert('Share profile feature coming soon!');
            });
        }

        window.addEventListener('resize', function() {
            isDesktop = window.innerWidth >= 769;
            if (!isDesktop) {
                closeHomeComments();
            }
        });

        renderPosts();
        renderConversations();
    })();
</script>

</body>
</html>