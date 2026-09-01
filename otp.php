<?php
// otp.php - OTP verification page
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>OTP Verification - Store Instant</title>
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

        #otp-container {
            width: 100%;
            height: 100%;
            max-width: 480px;
            background: #000;
            display: flex;
            flex-direction: column;
            padding: 0 20px;
            margin: 0 auto;
            border-left: 1px solid rgba(255,255,255,0.04);
            border-right: 1px solid rgba(255,255,255,0.04);
        }

        .otp-header-bar {
            display: flex;
            align-items: center;
            padding: 16px 0 8px;
            flex-shrink: 0;
        }

        .otp-header-bar .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .otp-header-bar .back-btn:active {
            opacity: 0.5;
        }

        .otp-header-bar .header-title {
            font-size: 18px;
            font-weight: 600;
            margin-left: 12px;
            color: #fff;
        }

        .otp-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 40px;
        }

        .otp-box {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }

        .otp-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .otp-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 50%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .otp-header p {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .otp-header .email-display {
            color: #999;
            font-weight: 500;
            margin-top: 4px;
        }

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input-group input {
            width: 44px;
            height: 56px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: #fff;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .otp-input-group input:focus {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
        }

        .otp-input-group input:disabled {
            opacity: 0.5;
        }

        .btn-otp-submit {
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

        .btn-otp-submit:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn-otp-submit i {
            margin-right: 8px;
            font-size: 15px;
        }

        .btn-otp-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .otp-timer {
            text-align: center;
            margin-top: 16px;
            color: #666;
            font-size: 14px;
        }

        .otp-timer span {
            color: #fff;
            font-weight: 600;
        }

        .otp-resend {
            text-align: center;
            margin-top: 12px;
        }

        .otp-resend button {
            background: none;
            border: none;
            color: #666;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            transition: color 0.2s ease;
        }

        .otp-resend button:active {
            color: #fff;
        }

        .otp-resend button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        @media (max-width: 480px) {
            .otp-header h1 {
                font-size: 24px;
            }
            .otp-input-group input {
                width: 38px;
                height: 48px;
                font-size: 20px;
            }
            .btn-otp-submit {
                padding: 14px 0;
                font-size: 16px;
            }
            .otp-header-bar .back-btn {
                font-size: 20px;
            }
            .otp-header-bar .header-title {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div id="otp-container">
    <div class="otp-header-bar">
        <button class="back-btn" id="backToRegister">
            <i class="fas fa-arrow-left"></i>
        </button>
        <span class="header-title">OTP Verification</span>
    </div>

    <div class="otp-content">
        <div class="otp-box">
            <div class="otp-header">
                <h1>Verify Your Email</h1>
                <p>We sent a verification code to</p>
                <p class="email-display" id="emailDisplay">your@email.com</p>
            </div>

            <form id="otpForm" autocomplete="off">
                <div class="otp-input-group" id="otpInputs">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                </div>

                <button type="submit" class="btn-otp-submit" id="verifyBtn">
                    <i class="fas fa-check-circle"></i> Verify
                </button>
            </form>

            <div class="otp-timer">
                Code expires in <span id="timerDisplay">05:00</span>
            </div>

            <div class="otp-resend">
                <button id="resendBtn" disabled>Resend OTP</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const email = urlParams.get('email') || '';
        const storeName = urlParams.get('store') || '';
        const password = urlParams.get('password') || '';

        const emailDisplay = document.getElementById('emailDisplay');
        const otpInputs = document.querySelectorAll('#otpInputs input');
        const verifyBtn = document.getElementById('verifyBtn');
        const backBtn = document.getElementById('backToRegister');
        const resendBtn = document.getElementById('resendBtn');
        const timerDisplay = document.getElementById('timerDisplay');

        let timeLeft = 300;
        let timerInterval;

        emailDisplay.textContent = email;

        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(function() {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerDisplay.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = '00:00';
                    resendBtn.disabled = false;
                    otpInputs.forEach(input => input.disabled = true);
                    verifyBtn.disabled = true;
                }
            }, 1000);
        }

        startTimer();

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const val = this.value;
                if (val && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('focus', function() {
                this.select();
            });
        });

        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });

            if (otp.length !== 6) {
                alert('Please enter the complete 6-digit code');
                return;
            }

            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyBtn.disabled = true;

            fetch('backend/verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    otp: otp,
                    email: email,
                    storeName: storeName,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify';
                verifyBtn.disabled = false;

                if (data.success) {
                    alert('Registration successful! Welcome to Store Instant!');
                    window.location.href = 'dashboard.php';
                } else {
                    alert(data.message || 'Invalid OTP. Please try again.');
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.disabled = false;
                    });
                    otpInputs[0].focus();
                }
            })
            .catch(() => {
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify';
                verifyBtn.disabled = false;
                alert('Network error. Please check your connection and try again.');
            });
        });

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'register.php';
        });

        resendBtn.addEventListener('click', function() {
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';

            fetch('backend/send_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    storeName: storeName,
                    resend: true
                })
            })
            .then(response => response.json())
            .then(data => {
                resendBtn.textContent = 'Resend OTP';
                if (data.success) {
                    timeLeft = 300;
                    clearInterval(timerInterval);
                    startTimer();
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.disabled = false;
                    });
                    otpInputs[0].focus();
                    verifyBtn.disabled = false;
                    alert('OTP resent successfully! Check your email.');
                } else {
                    alert('Failed to resend OTP. Please try again.');
                    resendBtn.disabled = false;
                }
            })
            .catch(() => {
                resendBtn.textContent = 'Resend OTP';
                resendBtn.disabled = false;
                alert('Network error. Please try again.');
            });
        });
    })();
</script>

</body>
</html>