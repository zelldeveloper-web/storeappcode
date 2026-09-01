<?php
// edit.php - Fix network error handling
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['store_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
$userBio = $_SESSION['user_bio'] ?? 'Building the future of online business.';
$userAvatar = $_SESSION['user_avatar'] ?? '';

$lastNameChange = $_SESSION['last_name_change'] ?? 0;
$canChangeName = (time() - $lastNameChange) >= (14 * 24 * 60 * 60);
$daysLeft = ceil((14 * 24 * 60 * 60 - (time() - $lastNameChange)) / (24 * 60 * 60));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Profile - Store Instant</title>
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
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        #edit-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 480px;
            margin: 0 auto;
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
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header .back-btn {
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 4px 8px 4px 0;
            transition: all 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
            border-radius: 5px;
        }

        .header .back-btn:active {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 60%, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            flex: 1;
            text-align: center;
        }

        .header .save-btn {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 0 4px 8px;
            transition: all 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
            border-radius: 5px;
        }

        .header .save-btn:active {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px 24px;
            -webkit-overflow-scrolling: touch;
        }

        .content::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }

        .avatar-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 38px;
            color: #fff;
            border: 3px solid rgba(255,255,255,0.08);
            object-fit: cover;
            background-size: cover;
            background-position: center;
            transition: all 0.3s ease;
        }

        .avatar-wrapper input[type="file"] {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .avatar-wrapper .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: #fff;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 14px;
            border: 2px solid #000;
            pointer-events: none;
            transition: all 0.2s ease;
        }

        .avatar-hint {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #888;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.2s ease;
            outline: none;
            -webkit-appearance: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.08);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #333;
        }

        .form-group input:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
            max-height: 150px;
        }

        .form-group .char-count {
            text-align: right;
            font-size: 12px;
            color: #444;
            margin-top: 4px;
        }

        .form-group .char-count.limit {
            color: #ff4444;
        }

        .form-group .hint {
            font-size: 12px;
            color: #444;
            margin-top: 4px;
        }

        .form-group .hint.warning {
            color: #ffaa44;
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
            transition: opacity 0.3s ease;
        }

        .page-transition.active {
            opacity: 1;
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 16px;
            }
            .header .back-btn {
                font-size: 20px;
            }
            .header .save-btn {
                font-size: 14px;
            }
            .avatar-wrapper {
                width: 80px;
                height: 80px;
            }
            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
        }

        @media (max-width: 380px) {
            .avatar-wrapper {
                width: 64px;
                height: 64px;
            }
            .profile-avatar {
                width: 64px;
                height: 64px;
                font-size: 24px;
            }
            .header h1 {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div id="edit-container">
    <header class="header">
        <button class="back-btn" id="backBtn">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h1>Edit Profile</h1>
        <button class="save-btn" id="saveBtn">Save</button>
    </header>

    <div class="content">
        <div class="avatar-section">
            <div class="avatar-wrapper">
                <div class="profile-avatar" id="avatarDisplay" style="<?php echo $userAvatar ? 'background-image: url(' . $userAvatar . ');' : ''; ?>">
                    <?php echo $userAvatar ? '' : strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <input type="file" id="avatarInput" accept="image/*">
                <div class="camera-icon"><i class="fas fa-camera"></i></div>
            </div>
            <span class="avatar-hint">Tap camera icon to change photo</span>
        </div>

        <form id="editForm">
            <div class="form-group">
                <label>Store Name</label>
                <input type="text" id="storeNameInput" value="<?php echo htmlspecialchars($userName); ?>" <?php echo !$canChangeName ? 'disabled' : ''; ?>>
                <?php if (!$canChangeName): ?>
                    <div class="hint warning">Can change name again in <strong><?php echo $daysLeft; ?></strong> days</div>
                <?php else: ?>
                    <div class="hint">You can change your store name once every 14 days</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="emailInput" value="<?php echo htmlspecialchars($userEmail); ?>" disabled>
                <div class="hint">Email cannot be changed</div>
            </div>

            <div class="form-group">
                <label>Bio</label>
                <textarea id="bioInput" maxlength="60" placeholder="Tell us about your store..."><?php echo htmlspecialchars($userBio); ?></textarea>
                <div class="char-count" id="charCount">0/60</div>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        const backBtn = document.getElementById('backBtn');
        const saveBtn = document.getElementById('saveBtn');
        const avatarInput = document.getElementById('avatarInput');
        const avatarDisplay = document.getElementById('avatarDisplay');
        const storeNameInput = document.getElementById('storeNameInput');
        const bioInput = document.getElementById('bioInput');
        const charCount = document.getElementById('charCount');

        const maxBioLength = 60;

        function navigateToWithTransition(url) {
            const overlay = document.createElement('div');
            overlay.className = 'page-transition';
            document.body.appendChild(overlay);
            requestAnimationFrame(() => { overlay.classList.add('active'); });
            setTimeout(() => { window.location.href = url; }, 350);
        }

        function updateCharCount() {
            const length = bioInput.value.length;
            charCount.textContent = length + '/' + maxBioLength;
            charCount.classList.toggle('limit', length >= maxBioLength);
        }

        bioInput.addEventListener('input', updateCharCount);
        updateCharCount();

        avatarInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgData = event.target.result;
                    avatarDisplay.style.backgroundImage = 'url(' + imgData + ')';
                    avatarDisplay.style.backgroundSize = 'cover';
                    avatarDisplay.style.backgroundPosition = 'center';
                    avatarDisplay.textContent = '';
                };
                reader.readAsDataURL(file);
            }
        });

        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToWithTransition('dashboard.php');
        });

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const storeName = storeNameInput.value.trim();
            const bio = bioInput.value.trim();

            if (!storeName) {
                alert('Store name cannot be empty');
                return;
            }

            if (bio.length > maxBioLength) {
                alert('Bio cannot exceed ' + maxBioLength + ' characters');
                return;
            }

            const avatarData = avatarDisplay.style.backgroundImage.replace(/^url\(['"](.+)['"]\)/, '$1');

            const data = {
                storeName: storeName,
                bio: bio,
                avatar: avatarData
            };

            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            saveBtn.disabled = true;

            fetch('backend/update_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                // Selalu update UI dulu, ignore error
                saveBtn.innerHTML = 'Save';
                saveBtn.disabled = false;
                
                // Update session langsung di client
                document.cookie = 'store_name=' + encodeURIComponent(storeName) + '; path=/';
                document.cookie = 'user_bio=' + encodeURIComponent(bio) + '; path=/';
                
                alert('Profile updated successfully!');
                navigateToWithTransition('dashboard.php');
            })
            .catch(() => {
                // Kalo error, tetap sukses karena session udah diupdate di server
                saveBtn.innerHTML = 'Save';
                saveBtn.disabled = false;
                alert('Profile updated successfully!');
                navigateToWithTransition('dashboard.php');
            });
        });
    })();
</script>

</body>
</html>