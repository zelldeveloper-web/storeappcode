<?php
// login.php - Simple login page with back button in header
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Store Instant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
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
            align-items: center;
            justify-content: center;
        }

        #login-container {
            width: 100%;
            height: 100%;
            max-width: 480px;
            background: #000;
            display: flex;
            flex-direction: column;
            padding: 0 20px;
            margin: 0 auto;
            border: none;
        }

        .login-header-bar {
            display: flex;
            align-items: center;
            padding: 16px 0 8px;
            flex-shrink: 0;
            justify-content: flex-start;
        }

        .login-header-bar .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .login-header-bar .back-btn:active {
            opacity: 0.5;
        }

        .login-header-bar .header-title {
            font-size: 18px;
            font-weight: 600;
            margin-left: 12px;
            color: #fff;
        }

        .login-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 40px;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 50%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .login-header p {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #999;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.2s ease;
            outline: none;
            -webkit-appearance: none;
        }

        .form-group input:focus {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
        }

        .form-group input::placeholder {
            color: #444;
        }

        .form-group .input-icon {
            position: absolute;
            right: 14px;
            bottom: 14px;
            color: #444;
            font-size: 18px;
        }

        .btn-login-submit {
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
            margin-top: 8px;
        }

        .btn-login-submit:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn-login-submit i {
            margin-right: 8px;
            font-size: 15px;
        }

        .forgot-password {
            text-align: right;
            margin-top: 8px;
        }

        .forgot-password a {
            color: #666;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-password a:active {
            color: #fff;
        }

        .error-message {
            color: #ff4444;
            font-size: 14px;
            text-align: center;
            margin-top: 12px;
            display: none;
        }

        .error-message.show {
            display: block;
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
            .login-header h1 {
                font-size: 28px;
            }
            .form-group input {
                padding: 12px 14px;
                font-size: 15px;
            }
            .btn-login-submit {
                padding: 14px 0;
                font-size: 16px;
            }
            .login-header-bar .back-btn {
                font-size: 20px;
            }
            .login-header-bar .header-title {
                font-size: 16px;
            }
            .forgot-password a {
                font-size: 12px;
            }
        }

        @media (min-width: 768px) {
            #login-container {
                max-width: 480px;
                border: none;
                padding: 0 20px;
            }
            .login-header-bar {
                padding: 20px 0 8px;
                justify-content: flex-start;
            }
            .login-header-bar .back-btn {
                font-size: 24px;
                padding: 8px 8px 8px 0;
            }
        }

        @media (min-width: 1024px) {
            #login-container {
                max-width: 500px;
                padding: 0 30px;
            }
            .login-header-bar {
                padding: 24px 0 12px;
            }
            .login-header-bar .back-btn {
                font-size: 26px;
            }
            .login-header h1 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<div id="login-container">
    <div class="login-header-bar">
        <button class="back-btn" id="backToMain">
            <i class="fas fa-arrow-left"></i>
        </button>
        <span class="header-title"></span>
    </div>

    <div class="login-content">
        <div class="login-box">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>login to your account</p>
            </div>

            <form id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="emailInput" placeholder="your@email.com" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="passwordInput" placeholder="••••••••" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <div class="forgot-password">
                    <a href="forgotpw.php" id="forgotLink">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login-submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                
                <div class="error-message" id="errorMessage">Invalid email or password</div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        const backBtn = document.getElementById('backToMain');
        const errorMessage = document.getElementById('errorMessage');
        const forgotLink = document.getElementById('forgotLink');

        function navigateToWithTransition(url) {
            const overlay = document.createElement('div');
            overlay.className = 'page-transition';
            document.body.appendChild(overlay);
            requestAnimationFrame(() => { overlay.classList.add('active'); });
            setTimeout(() => { window.location.href = url; }, 450);
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email || !password) {
                errorMessage.textContent = 'Please fill in all fields';
                errorMessage.classList.add('show');
                return;
            }

            const btn = form.querySelector('.btn-login-submit');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            btn.disabled = true;
            errorMessage.classList.remove('show');

            fetch('backend/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.success) {
                    navigateToWithTransition('loading.php');
                } else {
                    errorMessage.textContent = data.message || 'Invalid email or password';
                    errorMessage.classList.add('show');
                }
            })
            .catch((err) => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                errorMessage.textContent = 'Network error. Please try again.';
                errorMessage.classList.add('show');
            });
        });

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'main.php';
        });

        forgotLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'forgotpw.php';
        });
    })();
</script>

</body>
</html>