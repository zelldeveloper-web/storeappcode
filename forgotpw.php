<?php
// forgotpw.php - Forgot password with OTP
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Forgot Password - Store Instant</title>
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

        #forgot-container {
            width: 100%;
            height: 100%;
            max-width: 480px;
            background: #000;
            display: flex;
            flex-direction: column;
            padding: 0 20px;
            margin: 0 auto;
            border: none;
            box-shadow: none;
        }

        .forgot-header-bar {
            display: flex;
            align-items: center;
            padding: 16px 0 8px;
            flex-shrink: 0;
            justify-content: flex-start;
        }

        .forgot-header-bar .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .forgot-header-bar .back-btn:active {
            opacity: 0.5;
        }

        .forgot-header-bar .header-title {
            font-size: 18px;
            font-weight: 600;
            margin-left: 12px;
            color: #fff;
        }

        .forgot-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 40px;
        }

        .forgot-box {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }

        .forgot-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .forgot-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 50%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .forgot-header p {
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

        .btn-forgot-submit {
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

        .btn-forgot-submit:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn-forgot-submit i {
            margin-right: 8px;
            font-size: 15px;
        }

        .success-message {
            color: #29fd53;
            font-size: 14px;
            text-align: center;
            margin-top: 12px;
            display: none;
        }

        .success-message.show {
            display: block;
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

        @media (max-width: 480px) {
            .forgot-header h1 {
                font-size: 24px;
            }
            .form-group input {
                padding: 12px 14px;
                font-size: 15px;
            }
            .btn-forgot-submit {
                padding: 14px 0;
                font-size: 16px;
            }
            .forgot-header-bar .back-btn {
                font-size: 20px;
            }
            .forgot-header-bar .header-title {
                font-size: 16px;
            }
        }

        @media (min-width: 768px) {
            #forgot-container {
                max-width: 480px;
                border: none;
                box-shadow: none;
                padding: 0 20px;
            }
            .forgot-header-bar {
                padding: 20px 0 8px;
                justify-content: flex-start;
            }
            .forgot-header-bar .back-btn {
                font-size: 24px;
                padding: 8px 8px 8px 0;
            }
        }

        @media (min-width: 1024px) {
            #forgot-container {
                max-width: 500px;
                padding: 0 30px;
                border: none;
                box-shadow: none;
            }
            .forgot-header-bar {
                padding: 24px 0 12px;
            }
            .forgot-header-bar .back-btn {
                font-size: 26px;
            }
            .forgot-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

<div id="forgot-container">
    <div class="forgot-header-bar">
        <button class="back-btn" id="backToLogin">
            <i class="fas fa-arrow-left"></i>
        </button>
        <span class="header-title"></span>
    </div>

    <div class="forgot-content">
        <div class="forgot-box">
            <div class="forgot-header">
                <h1>Forgot Password</h1>
                <p>Enter your email to receive OTP</p>
            </div>

            <form id="forgotForm" autocomplete="off">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="emailInput" placeholder="your@email.com" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <button type="submit" class="btn-forgot-submit">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
                
                <div class="success-message" id="successMessage">OTP sent to your email! Redirecting...</div>
                <div class="error-message" id="errorMessage"></div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const form = document.getElementById('forgotForm');
        const emailInput = document.getElementById('emailInput');
        const backBtn = document.getElementById('backToLogin');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        let redirectTimer = null;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous timer
            if (redirectTimer) {
                clearTimeout(redirectTimer);
                redirectTimer = null;
            }

            const email = emailInput.value.trim();

            if (!email) {
                errorMessage.textContent = 'Please enter your email';
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
                return;
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                errorMessage.textContent = 'Please enter a valid email address';
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
                return;
            }

            const btn = form.querySelector('.btn-forgot-submit');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;
            errorMessage.classList.remove('show');
            successMessage.classList.remove('show');

            fetch('backend/send_reset_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
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
                    // OTP terkirim -> redirect ke verify_reset.php
                    successMessage.textContent = 'OTP sent to your email! Redirecting...';
                    successMessage.classList.add('show');
                    errorMessage.classList.remove('show');
                    
                    redirectTimer = setTimeout(() => {
                        window.location.href = 'verify_reset.php?email=' + encodeURIComponent(email);
                    }, 1500);
                } else {
                    // Cek pesan error dari server
                    const msg = data.message || 'Failed to send OTP. Try again.';
                    if (msg.toLowerCase().includes('not registered') || msg.toLowerCase().includes('not found')) {
                        // Akun tidak terdaftar -> redirect ke login.php
                        errorMessage.textContent = 'Email not registered! Redirecting to login...';
                        errorMessage.classList.add('show');
                        successMessage.classList.remove('show');
                        
                        redirectTimer = setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    } else {
                        // OTP tidak terkirim -> tetap di forgotpw.php
                        errorMessage.textContent = msg;
                        errorMessage.classList.add('show');
                        successMessage.classList.remove('show');
                    }
                }
            })
            .catch((err) => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                errorMessage.textContent = 'Network error: ' + err.message;
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
            });
        });

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (redirectTimer) {
                clearTimeout(redirectTimer);
                redirectTimer = null;
            }
            window.location.href = 'login.php';
        });
    })();
</script>

</body>
</html>