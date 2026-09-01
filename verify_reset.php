<?php
// verify_reset.php - Verify OTP and reset password
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - Store Instant</title>
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

        #reset-container {
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

        .reset-header-bar {
            display: flex;
            align-items: center;
            padding: 16px 0 8px;
            flex-shrink: 0;
            justify-content: flex-start;
        }

        .reset-header-bar .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .reset-header-bar .back-btn:active {
            opacity: 0.5;
        }

        .reset-header-bar .header-title {
            font-size: 18px;
            font-weight: 600;
            margin-left: 12px;
            color: #fff;
        }

        .reset-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 40px;
        }

        .reset-box {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .reset-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 50%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .reset-header p {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input-group input {
            width: 48px;
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

        .otp-input-group input.success {
            border-color: #29fd53;
            background: rgba(41, 253, 83, 0.1);
        }

        .btn-reset-submit {
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

        .btn-reset-submit:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn-reset-submit:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .btn-reset-submit i {
            margin-right: 8px;
            font-size: 15px;
        }

        .form-group {
            margin-top: 16px;
            display: none;
            position: relative;
        }

        .form-group.show {
            display: block;
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
            padding-right: 50px;
        }

        .form-group input:focus {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
        }

        .form-group input::placeholder {
            color: #444;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 16px;
            bottom: 14px;
            color: #666;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .form-group .toggle-password:active {
            color: #fff;
        }

        .form-group .password-match {
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .form-group .password-match.match {
            color: #29fd53;
            display: block;
        }

        .form-group .password-match.nomatch {
            color: #ff4444;
            display: block;
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

        .otp-timer {
            text-align: center;
            margin-top: 12px;
            color: #666;
            font-size: 14px;
        }

        .otp-timer span {
            color: #fff;
            font-weight: 600;
        }

        .otp-resend {
            text-align: center;
            margin-top: 8px;
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
            .reset-header h1 {
                font-size: 24px;
            }
            .otp-input-group input {
                width: 40px;
                height: 48px;
                font-size: 20px;
            }
            .btn-reset-submit {
                padding: 14px 0;
                font-size: 16px;
            }
            .reset-header-bar .back-btn {
                font-size: 20px;
            }
            .reset-header-bar .header-title {
                font-size: 16px;
            }
        }

        @media (min-width: 768px) {
            #reset-container {
                max-width: 480px;
                border: none;
                padding: 0 20px;
            }
        }

        @media (min-width: 1024px) {
            #reset-container {
                max-width: 500px;
                padding: 0 30px;
            }
            .reset-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

<div id="reset-container">
    <div class="reset-header-bar">
        <button class="back-btn" id="backToLogin">
            <i class="fas fa-arrow-left"></i>
        </button>
        <span class="header-title"></span>
    </div>

    <div class="reset-content">
        <div class="reset-box">
            <div class="reset-header">
                <h1>Reset Password</h1>
                <p id="stepTitle">Enter the OTP sent to your email</p>
            </div>

            <form id="resetForm" autocomplete="off">
                <!-- OTP INPUT -->
                <div id="otpStep">
                    <div class="otp-input-group" id="otpInputs">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    </div>
                    <button type="button" class="btn-reset-submit" id="verifyOtpBtn">Verify OTP</button>
                    <div class="otp-timer">Code expires in <span id="timerDisplay">05:00</span></div>
                    <div class="otp-resend">
                        <button id="resendBtn" disabled>Resend OTP</button>
                    </div>
                </div>

                <!-- NEW PASSWORD STEP -->
                <div id="passwordStep" style="display:none;">
                    <div class="form-group show">
                        <label>New Password</label>
                        <input type="password" id="newPasswordInput" placeholder="••••••••" required minlength="6">
                        <button type="button" class="toggle-password" id="toggleNewPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-group show">
                        <label>Confirm Password</label>
                        <input type="password" id="confirmPasswordInput" placeholder="••••••••" required minlength="6">
                        <div class="password-match" id="passwordMatch"></div>
                    </div>
                    <button type="button" class="btn-reset-submit" id="resetPasswordBtn">Reset Password</button>
                </div>

                <div class="error-message" id="errorMessage"></div>
                <div class="success-message" id="successMessage">Password reset successfully!</div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const backBtn = document.getElementById('backToLogin');
        const otpInputs = document.querySelectorAll('#otpInputs input');
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const resetPasswordBtn = document.getElementById('resetPasswordBtn');
        const newPasswordInput = document.getElementById('newPasswordInput');
        const confirmPasswordInput = document.getElementById('confirmPasswordInput');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        const stepTitle = document.getElementById('stepTitle');
        const otpStep = document.getElementById('otpStep');
        const passwordStep = document.getElementById('passwordStep');
        const timerDisplay = document.getElementById('timerDisplay');
        const resendBtn = document.getElementById('resendBtn');
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const passwordMatch = document.getElementById('passwordMatch');

        const urlParams = new URLSearchParams(window.location.search);
        const email = urlParams.get('email') || '';

        let timeLeft = 300;
        let timerInterval;
        let otpVerified = false;

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
                    verifyOtpBtn.disabled = true;
                }
            }, 1000);
        }

        startTimer();

        // OTP input navigation
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

        // Toggle password visibility
        toggleNewPassword.addEventListener('click', function() {
            const input = newPasswordInput;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });

        // Check password match
        function checkPasswordMatch() {
            const newPass = newPasswordInput.value;
            const confirmPass = confirmPasswordInput.value;

            if (!confirmPass) {
                passwordMatch.className = 'password-match';
                passwordMatch.textContent = '';
                return;
            }

            if (newPass === confirmPass) {
                passwordMatch.className = 'password-match match';
                passwordMatch.textContent = '✓ Passwords match';
            } else {
                passwordMatch.className = 'password-match nomatch';
                passwordMatch.textContent = '✗ Passwords do not match';
            }
        }

        newPasswordInput.addEventListener('input', checkPasswordMatch);
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        // Verify OTP
        verifyOtpBtn.addEventListener('click', function() {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });

            if (otp.length !== 6) {
                errorMessage.textContent = 'Please enter the complete 6-digit code';
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
                return;
            }

            verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyOtpBtn.disabled = true;
            errorMessage.classList.remove('show');

            fetch('backend/verify_reset_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    otp: otp,
                    email: email,
                    verifyOnly: true
                })
            })
            .then(response => response.json())
            .then(data => {
                verifyOtpBtn.innerHTML = 'Verify OTP';
                verifyOtpBtn.disabled = false;

                if (data.success) {
                    otpVerified = true;
                    
                    // Mark all OTP inputs as success
                    otpInputs.forEach(input => {
                        input.classList.add('success');
                        input.disabled = true;
                    });
                    
                    // Hide OTP step, show password step
                    otpStep.style.display = 'none';
                    passwordStep.style.display = 'block';
                    stepTitle.textContent = 'Enter your new password';
                    successMessage.classList.remove('show');
                    errorMessage.classList.remove('show');
                } else {
                    errorMessage.textContent = data.message || 'Invalid OTP. Please try again.';
                    errorMessage.classList.add('show');
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.disabled = false;
                        input.classList.remove('success');
                    });
                    otpInputs[0].focus();
                }
            })
            .catch(() => {
                verifyOtpBtn.innerHTML = 'Verify OTP';
                verifyOtpBtn.disabled = false;
                errorMessage.textContent = 'Network error. Please try again.';
                errorMessage.classList.add('show');
            });
        });

        // Reset Password
        resetPasswordBtn.addEventListener('click', function() {
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            if (!newPassword || newPassword.length < 6) {
                errorMessage.textContent = 'Password must be at least 6 characters';
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
                return;
            }

            if (newPassword !== confirmPassword) {
                errorMessage.textContent = 'Passwords do not match';
                errorMessage.classList.add('show');
                successMessage.classList.remove('show');
                return;
            }

            resetPasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
            resetPasswordBtn.disabled = true;
            errorMessage.classList.remove('show');

            fetch('backend/verify_reset_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    otp: 'verified',
                    email: email,
                    newPassword: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                resetPasswordBtn.innerHTML = 'Reset Password';
                resetPasswordBtn.disabled = false;

                if (data.success) {
                    successMessage.classList.add('show');
                    errorMessage.classList.remove('show');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    errorMessage.textContent = data.message || 'Failed to reset password';
                    errorMessage.classList.add('show');
                }
            })
            .catch(() => {
                resetPasswordBtn.innerHTML = 'Reset Password';
                resetPasswordBtn.disabled = false;
                errorMessage.textContent = 'Network error. Please try again.';
                errorMessage.classList.add('show');
            });
        });

        // Resend OTP
        resendBtn.addEventListener('click', function() {
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';

            fetch('backend/send_reset_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                resendBtn.textContent = 'Resend OTP';
                if (data.success) {
                    timeLeft = 300;
                    startTimer();
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.disabled = false;
                        input.classList.remove('success');
                    });
                    otpInputs[0].focus();
                    verifyOtpBtn.disabled = false;
                    errorMessage.classList.remove('show');
                    successMessage.classList.remove('show');
                    // Kembalikan ke step OTP
                    otpStep.style.display = 'block';
                    passwordStep.style.display = 'none';
                    stepTitle.textContent = 'Enter the OTP sent to your email';
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

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'login.php';
        });
    })();
</script>

</body>
</html>