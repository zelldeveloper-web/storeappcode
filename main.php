<?php
// main.php - Main landing page with fixed navigation
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Store Instant</title>
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

        #main-container {
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
            padding: 20px;
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        #main-container.active {
            opacity: 1;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 480px;
            background: transparent;
            border: none;
            padding: 20px;
            height: 100%;
        }

        .video-wrapper {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .video-card-wrapper {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: #000;
            position: relative;
            border-radius: 0;
            flex-shrink: 0;
        }

        #main-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #000;
        }

        .buttons-wrapper {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 0 0 16px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex-shrink: 0;
        }

        .btn {
            width: 100%;
            padding: 16px 0;
            border: none;
            border-radius: 5px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            letter-spacing: 0.3px;
            background: #fff;
            color: #000;
            font-family: inherit;
        }

        .btn:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn i {
            margin-right: 8px;
            font-size: 15px;
        }

        @media (max-width: 480px) {
            .video-card-wrapper {
                max-width: 340px;
            }
            .btn {
                padding: 14px 0;
                font-size: 16px;
            }
            .buttons-wrapper {
                padding: 0 0 16px 0;
                gap: 10px;
            }
            .main-content {
                padding: 16px;
                justify-content: center;
            }
        }

        @media (max-width: 380px) {
            .video-card-wrapper {
                max-width: 280px;
            }
            .btn {
                padding: 12px 0;
                font-size: 14px;
            }
            .buttons-wrapper {
                gap: 8px;
                padding: 0 0 12px 0;
            }
            .main-content {
                padding: 12px;
            }
        }

        @media (min-width: 481px) and (max-width: 767px) {
            .buttons-wrapper {
                padding: 0 0 20px 0;
            }
            .video-card-wrapper {
                max-width: 380px;
            }
        }

        @media (min-width: 768px) {
            #main-container {
                padding: 40px 20px;
            }
            .main-content {
                max-width: 480px;
                padding: 20px;
                background: transparent;
                border: none;
                justify-content: center;
            }
            .video-card-wrapper {
                max-width: 420px;
            }
            .buttons-wrapper {
                padding: 20px 0 0 0;
            }
        }

        @media (min-width: 1024px) {
            #main-container {
                padding: 40px;
            }
            .main-content {
                max-width: 500px;
                padding: 20px;
                background: transparent;
                border: none;
            }
            .video-card-wrapper {
                max-width: 440px;
            }
            .btn {
                font-size: 18px;
                padding: 18px 0;
            }
            .buttons-wrapper {
                padding: 24px 0 0 0;
            }
        }
    </style>
</head>
<body>

<div id="main-container">
    <div class="main-content">
        <div class="video-wrapper">
            <div class="video-card-wrapper">
                <video id="main-video" autoplay muted playsinline loop>
                    <source src="https://files.catbox.moe/jxbxk8.mp4" type="video/mp4">
                </video>
            </div>
        </div>
        <div class="buttons-wrapper">
            <button class="btn" id="registerBtn">
                <i class="fas fa-user-plus"></i> Register
            </button>
            <button class="btn" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('main-container');
        const video = document.getElementById('main-video');

        container.classList.add('active');

        video.addEventListener('loadeddata', function() {
            video.play().catch(function(e) {});
        });

        document.getElementById('loginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'login.php';
        });

        document.getElementById('registerBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'register.php';
        });
    })();
</script>

</body>
</html>