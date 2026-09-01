<?php
// index.php - Splash screen with video card, skip button, and auto-navigate to main.php
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

        #splash-container {
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
        }

        #splash-container.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        .skip-btn-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            padding: 0 20px;
            display: flex;
            justify-content: flex-end;
            pointer-events: none;
            width: 100%;
            max-width: 100%;
        }

        .skip-btn-wrapper button {
            pointer-events: auto;
            padding: 8px 20px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
            font-family: inherit;
        }

        .skip-btn-wrapper button:active {
            background: rgba(255,255,255,0.2);
            transform: scale(0.95);
        }

        .splash-content {
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

        .video-card-wrapper {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: #000;
            position: relative;
            border-radius: 12px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.6);
        }

        #splash-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #000;
        }

        .brand-text {
            margin-top: 28px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .brand-text h1 {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(180deg, #ffffff 60%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            transform: scale(1);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-block;
            padding: 0 4px;
            line-height: 1.1;
        }

        .brand-text h1.animate {
            transform: scale(0.92);
        }

        .brand-text p {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        @media (max-width: 480px) {
            .brand-text h1 {
                font-size: 34px;
            }
            .video-card-wrapper {
                max-width: 340px;
                border-radius: 10px;
            }
            .skip-btn-wrapper {
                top: 16px;
                padding: 0 16px;
            }
            .skip-btn-wrapper button {
                font-size: 12px;
                padding: 6px 16px;
            }
            .splash-content {
                padding: 16px;
            }
        }

        @media (max-width: 380px) {
            .brand-text h1 {
                font-size: 28px;
            }
            .video-card-wrapper {
                max-width: 280px;
                border-radius: 8px;
            }
            .brand-text p {
                font-size: 12px;
            }
            .skip-btn-wrapper button {
                font-size: 11px;
                padding: 5px 14px;
            }
        }

        @media (min-width: 768px) {
            #splash-container {
                padding: 40px 20px;
            }
            .splash-content {
                max-width: 480px;
                padding: 20px;
                background: transparent;
                border: none;
            }
            .video-card-wrapper {
                max-width: 420px;
                border-radius: 14px;
            }
            .brand-text h1 {
                font-size: 48px;
            }
        }

        @media (min-width: 1024px) {
            #splash-container {
                padding: 40px;
            }
            .splash-content {
                max-width: 500px;
                padding: 20px;
                background: transparent;
                border: none;
            }
            .video-card-wrapper {
                max-width: 440px;
                border-radius: 16px;
            }
            .brand-text h1 {
                font-size: 54px;
            }
            .brand-text p {
                font-size: 16px;
            }
            .skip-btn-wrapper {
                max-width: 500px;
                left: 50%;
                transform: translateX(-50%);
                right: auto;
                padding: 0 30px;
                top: 30px;
            }
        }
    </style>
</head>
<body>

<div id="splash-container">
    <div class="skip-btn-wrapper">
        <button id="skipBtn">
            <i class="fas fa-forward" style="font-size: 11px; margin-right: 4px;"></i> Skip
        </button>
    </div>

    <div class="splash-content">
        <div class="video-card-wrapper">
            <video id="splash-video" autoplay muted playsinline>
                <source src="https://files.catbox.moe/jxbxk8.mp4" type="video/mp4">
            </video>
        </div>
        <div class="brand-text">
            <h1 id="brandTitle">Instant Store</h1>
            <p>post your business and your services</p>
        </div>
    </div>
</div>

<script>
    (function() {
        const splashContainer = document.getElementById('splash-container');
        const video = document.getElementById('splash-video');
        const skipBtn = document.getElementById('skipBtn');
        const brandTitle = document.getElementById('brandTitle');

        let splashEnded = false;

        function navigateToMain() {
            if (splashEnded) return;
            splashEnded = true;

            splashContainer.classList.add('hidden');

            setTimeout(() => {
                window.location.href = 'main.php';
            }, 650);
        }

        video.addEventListener('loadeddata', function() {
            video.play().catch(function(e) {});
        });

        video.addEventListener('ended', function() {
            navigateToMain();
        });

        video.addEventListener('error', function() {
            setTimeout(navigateToMain, 1000);
        });

        skipBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToMain();
        });

        brandTitle.addEventListener('click', function() {
            this.classList.add('animate');
            setTimeout(() => {
                this.classList.remove('animate');
            }, 300);
        });

        setTimeout(function() {
            if (!splashEnded) {
                navigateToMain();
            }
        }, 12000);

        document.addEventListener('touchstart', function() {
            if (video.paused) {
                video.play().catch(function(e) {});
            }
        });

        document.addEventListener('click', function() {
            if (video.paused) {
                video.play().catch(function(e) {});
            }
        });
    })();
</script>

</body>
</html>