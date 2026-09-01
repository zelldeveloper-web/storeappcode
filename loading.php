<?php
// loading.php - Loading screen after login
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['store_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Welcome - Store Instant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loading-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.6s ease;
        }

        #loading-container.active {
            opacity: 1;
        }

        #loading-container.fade-out {
            opacity: 0;
            transition: opacity 0.6s ease;
        }

        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 480px;
            padding: 20px;
            background: transparent;
            border: none;
        }

        .loading-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 40px;
            color: #fff;
            background-size: cover;
            background-position: center;
            margin-bottom: 24px;
            border: 3px solid rgba(255,255,255,0.08);
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .loading-text {
            text-align: center;
        }

        .loading-text h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 60%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .loading-text p {
            font-size: 14px;
            color: #666;
            letter-spacing: 0.3px;
        }

        .loading-spinner {
            margin-top: 32px;
            width: 32px;
            height: 32px;
            border: 3px solid rgba(255,255,255,0.06);
            border-top: 3px solid #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-dots {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .loading-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #333;
            animation: dotBounce 1.4s ease-in-out infinite;
        }

        .loading-dots span:nth-child(1) { animation-delay: 0s; }
        .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loading-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes dotBounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
            40% { transform: scale(1); opacity: 1; }
        }

        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
            pointer-events: none;
            background: #000;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .page-transition.active {
            opacity: 1;
        }

        @media (max-width: 480px) {
            .loading-avatar {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
            .loading-text h1 {
                font-size: 24px;
            }
        }

        @media (min-width: 768px) {
            .loading-content {
                max-width: 480px;
                padding: 30px;
                border-left: 1px solid rgba(255,255,255,0.04);
                border-right: 1px solid rgba(255,255,255,0.04);
            }
        }

        @media (min-width: 1024px) {
            .loading-content {
                max-width: 500px;
                padding: 40px;
                border-left: 1px solid rgba(255,255,255,0.04);
                border-right: 1px solid rgba(255,255,255,0.04);
                border-radius: 16px;
                background: rgba(255,255,255,0.02);
            }
        }
    </style>
</head>
<body>

<div id="loading-container">
    <div class="loading-content">
        <div class="loading-avatar" id="loadingAvatar" style="<?php echo isset($_SESSION['user_avatar']) && $_SESSION['user_avatar'] ? 'background-image: url(' . $_SESSION['user_avatar'] . ');' : ''; ?>">
            <?php echo isset($_SESSION['user_avatar']) && $_SESSION['user_avatar'] ? '' : strtoupper(substr($userName, 0, 1)); ?>
        </div>
        <div class="loading-text">
            <h1>Welcome back, <?php echo htmlspecialchars($userName); ?></h1>
            <p>Loading your dashboard...</p>
        </div>
        <div class="loading-spinner"></div>
        <div class="loading-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('loading-container');

        // Show loading with animation
        setTimeout(() => {
            container.classList.add('active');
        }, 100);

        // Auto redirect to dashboard
        setTimeout(() => {
            container.classList.add('fade-out');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 650);
        }, 2500);
    })();
</script>

</body>
</html>