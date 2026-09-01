<?php
// register.php - Register page with email OTP
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register - Store Instant</title>
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

        #register-container {
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

        .register-header-bar {
            display: flex;
            align-items: center;
            padding: 16px 0 8px;
            flex-shrink: 0;
            justify-content: flex-start;
        }

        .register-header-bar .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
        }

        .register-header-bar .back-btn:active {
            opacity: 0.5;
        }

        .register-header-bar .header-title {
            font-size: 18px;
            font-weight: 600;
            margin-left: 12px;
            color: #fff;
        }

        .register-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 20px;
            overflow-y: auto;
        }

        .register-content::-webkit-scrollbar {
            width: 0;
        }

        .register-box {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .register-header h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff 50%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .register-header p {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .form-group {
            margin-bottom: 16px;
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

        .btn-register-submit {
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

        .btn-register-submit:active {
            transform: scale(0.97);
            opacity: 0.85;
        }

        .btn-register-submit i {
            margin-right: 8px;
            font-size: 15px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 32px 24px 24px;
            max-width: 340px;
            width: 90%;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .modal-box h3 {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #fff;
        }

        .modal-box p {
            font-size: 14px;
            color: #999;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .modal-actions button {
            flex: 1;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .modal-actions button:active {
            transform: scale(0.97);
        }

        .modal-btn-no {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .modal-btn-no:active {
            background: rgba(255,255,255,0.15);
        }

        .modal-btn-yes {
            background: #fff;
            color: #000;
        }

        .modal-btn-yes:active {
            opacity: 0.85;
        }

        @media (max-width: 480px) {
            .register-header h1 {
                font-size: 28px;
            }
            .form-group input {
                padding: 12px 14px;
                font-size: 15px;
            }
            .btn-register-submit {
                padding: 14px 0;
                font-size: 16px;
            }
            .register-header-bar .back-btn {
                font-size: 20px;
            }
            .register-header-bar .header-title {
                font-size: 16px;
            }
            .register-content {
                padding-bottom: 10px;
            }
        }

        @media (min-width: 768px) {
            #register-container {
                max-width: 480px;
                border: none;
                padding: 0 20px;
            }
            .register-header-bar {
                padding: 20px 0 8px;
                justify-content: flex-start;
            }
            .register-header-bar .back-btn {
                font-size: 24px;
                padding: 8px 8px 8px 0;
            }
        }

        @media (min-width: 1024px) {
            #register-container {
                max-width: 500px;
                padding: 0 30px;
            }
            .register-header-bar {
                padding: 24px 0 12px;
            }
            .register-header-bar .back-btn {
                font-size: 26px;
            }
            .register-header h1 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<div id="register-container">
    <div class="register-header-bar">
        <button class="back-btn" id="backToMain">
            <i class="fas fa-arrow-left"></i>
        </button>
        <span class="header-title"></span>
    </div>

    <div class="register-content">
        <div class="register-box">
            <div class="register-header">
                <h1>Create Account</h1>
                <p>register your store now</p>
            </div>

            <form id="registerForm" autocomplete="off">
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" id="storeNameInput" placeholder="Your Store Name" required>
                    <i class="fas fa-store input-icon"></i>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="emailInput" placeholder="your@email.com" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="passwordInput" placeholder="••••••••" required minlength="6">
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <button type="submit" class="btn-register-submit">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <h3>Confirm Registration</h3>
        <p>Are you sure you want to register? An OTP will be sent to your email.</p>
        <div class="modal-actions">
            <button class="modal-btn-no" id="modalNo">No</button>
            <button class="modal-btn-yes" id="modalYes">Yes</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const form = document.getElementById('registerForm');
        const storeNameInput = document.getElementById('storeNameInput');
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        const backBtn = document.getElementById('backToMain');
        const modal = document.getElementById('confirmModal');
        const modalNo = document.getElementById('modalNo');
        const modalYes = document.getElementById('modalYes');

        let formData = {};

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const storeName = storeNameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!storeName || !email || !password) {
                alert('Please fill in all fields');
                return;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                return;
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                alert('Please enter a valid email address');
                return;
            }

            formData = {
                storeName: storeName,
                email: email,
                password: password
            };

            modal.classList.add('show');
        });

        modalNo.addEventListener('click', function() {
            modal.classList.remove('show');
            formData = {};
        });

        modalYes.addEventListener('click', function() {
            modal.classList.remove('show');
            
            const btn = form.querySelector('.btn-register-submit');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending OTP...';
            btn.disabled = true;

            fetch('backend/send_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.email,
                    storeName: formData.storeName
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.success) {
                    window.location.href = 'otp.php?email=' + encodeURIComponent(formData.email) + 
                        '&store=' + encodeURIComponent(formData.storeName) +
                        '&password=' + encodeURIComponent(formData.password);
                } else {
                    alert('Failed to send OTP. Please try again.\n' + (data.message || ''));
                }
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Network error. Please try again.');
            });
        });

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'main.php';
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
                formData = {};
            }
        });
    })();
</script>

</body>
</html>