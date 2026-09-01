<?php
// profile.php - User profile page with clean nav add
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['store_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
$userBio = $_SESSION['user_bio'] ?? 'Building the future of online business. Passionate about helping others succeed.';
$userAvatar = $_SESSION['user_avatar'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profile - Store Instant</title>
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
            background: #0a0a0a;
            color: #fff;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        #profile-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 480px;
            margin: 0 auto;
            border-left: 1px solid rgba(255,255,255,0.04);
            border-right: 1px solid rgba(255,255,255,0.04);
            background: #0a0a0a;
            position: relative;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(10, 10, 10, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 14px 20px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
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

        .header .hamburger {
            position: absolute;
            right: 16px;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            transition: opacity 0.2s ease;
            background: none;
            border: none;
            font-family: inherit;
            border-radius: 5px;
        }

        .header .hamburger:active {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 16px 16px 80px;
            -webkit-overflow-scrolling: touch;
        }

        .content::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
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
            transition: all 0.3s ease;
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
            color: #888;
            margin-top: 2px;
        }

        .profile-bio {
            font-size: 14px;
            color: #999;
            margin-top: 6px;
            line-height: 1.4;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        .profile-actions .btn-edit {
            flex: 1;
            padding: 8px 0;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 5px;
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
            border-radius: 5px;
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
            padding: 16px 0;
            border-top: 1px solid rgba(255,255,255,0.03);
            border-bottom: 1px solid rgba(255,255,255,0.03);
            margin-bottom: 16px;
        }

        .profile-stats .stat {
            text-align: center;
            flex: 1;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 5px;
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

        .profile-stats .stat:active {
            opacity: 0.6;
            transform: scale(0.95);
        }

        .no-posts {
            color: #444;
            font-size: 14px;
            text-align: center;
            padding: 30px 20px;
            background: #111;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.03);
        }

        .no-posts i {
            color: #1a1a1a;
            font-size: 32px;
            display: block;
            margin-bottom: 10px;
        }

        .bottom-nav {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(10, 10, 10, 0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-top: 1px solid rgba(255,255,255,0.03);
            display: flex;
            justify-content: space-around;
            padding: 6px 0 10px;
            z-index: 60;
            flex-shrink: 0;
            margin-top: auto;
        }

        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #555;
            font-size: 10px;
            gap: 2px;
            padding: 4px 12px;
            transition: color 0.2s ease;
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
            position: relative;
            flex: 1;
            max-width: 70px;
        }

        .bottom-nav a i {
            font-size: 22px;
            transition: color 0.2s ease;
        }

        .bottom-nav a.active {
            color: #fff;
        }

        .bottom-nav a.active i {
            color: #fff;
        }

        .bottom-nav a:active {
            transform: scale(0.92);
        }

        .bottom-nav .nav-add i {
            background: #fff;
            color: #000;
            border-radius: 5px;
            padding: 8px;
            font-size: 20px;
            margin-top: -14px;
            box-shadow: none;
            border: none;
        }

        .bottom-nav .badge {
            position: absolute;
            top: -2px;
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
            border: 2px solid #0a0a0a;
        }

        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
            pointer-events: none;
            background: #0a0a0a;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .page-transition.active {
            opacity: 1;
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 18px;
            }
            .header .hamburger {
                font-size: 20px;
                right: 12px;
            }
            .profile-avatar {
                width: 64px;
                height: 64px;
                font-size: 26px;
            }
            .profile-display-name {
                font-size: 18px;
            }
            .profile-username {
                font-size: 13px;
            }
            .profile-bio {
                font-size: 13px;
            }
            .profile-actions .btn-edit {
                font-size: 13px;
                padding: 6px 0;
            }
            .profile-actions .btn-share {
                font-size: 13px;
                padding: 6px 14px;
            }
            .bottom-nav a {
                padding: 4px 8px;
                font-size: 9px;
                max-width: 60px;
            }
            .bottom-nav a i {
                font-size: 20px;
            }
            .bottom-nav .nav-add i {
                font-size: 18px;
                padding: 7px;
                margin-top: -12px;
            }
            .bottom-nav .badge {
                width: 16px;
                height: 16px;
                font-size: 8px;
                right: 2px;
                top: -4px;
            }
            .no-posts {
                padding: 20px 16px;
                font-size: 13px;
            }
            .no-posts i {
                font-size: 28px;
            }
        }

        @media (max-width: 380px) {
            .profile-avatar {
                width: 52px;
                height: 52px;
                font-size: 20px;
            }
            .profile-display-name {
                font-size: 16px;
            }
            .profile-bio {
                font-size: 12px;
            }
            .bottom-nav a {
                padding: 4px 4px;
                font-size: 8px;
                max-width: 50px;
            }
            .bottom-nav a i {
                font-size: 18px;
            }
            .bottom-nav .nav-add i {
                font-size: 16px;
                padding: 6px;
                margin-top: -10px;
            }
            .header h1 {
                font-size: 16px;
            }
            .profile-stats .stat .number {
                font-size: 15px;
            }
            .profile-stats .stat .label {
                font-size: 10px;
            }
        }

        @media (min-width: 481px) {
            .bottom-nav a {
                max-width: 80px;
                font-size: 11px;
            }
            .bottom-nav a i {
                font-size: 24px;
            }
            .bottom-nav .nav-add i {
                font-size: 22px;
                padding: 9px;
                margin-top: -16px;
            }
            .profile-avatar {
                width: 96px;
                height: 96px;
                font-size: 38px;
            }
            .profile-display-name {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

<div id="profile-container">
    <header class="header">
        <h1>Profile</h1>
        <button class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <div class="content">
        <div class="profile-header">
            <div class="profile-avatar" id="profileAvatar" style="<?php echo $userAvatar ? 'background-image: url(' . $userAvatar . ');' : ''; ?>">
                <?php echo $userAvatar ? '' : strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div class="profile-name-section">
                <div class="profile-display-name"><?php echo htmlspecialchars($userName); ?></div>
                <div class="profile-username">@<?php echo explode('@', $userEmail)[0]; ?></div>
                <div class="profile-bio"><?php echo htmlspecialchars($userBio); ?></div>
                <div class="profile-actions">
                    <button class="btn-edit" id="editProfileBtn"><i class="fas fa-pen"></i> Edit</button>
                    <button class="btn-share" id="shareProfileBtn"><i class="fas fa-share"></i></button>
                </div>
            </div>
        </div>

        <div class="profile-stats">
            <div class="stat" id="statFollowers">
                <div class="number">0</div>
                <div class="label">Followers</div>
            </div>
            <div class="stat" id="statFollowing">
                <div class="number">0</div>
                <div class="label">Following</div>
            </div>
            <div class="stat" id="statLikes">
                <div class="number">0</div>
                <div class="label">Likes</div>
            </div>
        </div>

        <div class="no-posts">
            <i class="fas fa-store-alt"></i>
            No posts yet
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" id="navHome">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" id="navSearch">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </a>
        <a href="post.php" id="navAdd" class="nav-add">
            <i class="fas fa-plus-circle"></i>
            <span>Add</span>
        </a>
        <a href="#" id="navNotification">
            <i class="fas fa-bell"></i>
            <span>Notify</span>
            <span class="badge">3</span>
        </a>
        <a href="#" class="active" id="navAccount">
            <i class="fas fa-user"></i>
            <span>Account</span>
        </a>
    </nav>
</div>

<script>
    (function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const editProfileBtn = document.getElementById('editProfileBtn');
        const shareProfileBtn = document.getElementById('shareProfileBtn');

        function navigateToWithTransition(url) {
            const overlay = document.createElement('div');
            overlay.className = 'page-transition';
            document.body.appendChild(overlay);

            requestAnimationFrame(() => {
                overlay.classList.add('active');
            });

            setTimeout(() => {
                window.location.href = url;
            }, 350);
        }

        hamburgerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Settings menu coming soon!');
        });

        editProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToWithTransition('edit.php');
        });

        shareProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Share profile feature coming soon!');
        });

        document.getElementById('statFollowers').addEventListener('click', function() {
            alert('Followers list coming soon!');
        });

        document.getElementById('statFollowing').addEventListener('click', function() {
            alert('Following list coming soon!');
        });

        document.getElementById('statLikes').addEventListener('click', function() {
            alert('Likes list coming soon!');
        });

        function setActiveNav(activeElement) {
            document.querySelectorAll('.bottom-nav a').forEach(a => a.classList.remove('active'));
            activeElement.classList.add('active');
        }

        document.getElementById('navHome').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveNav(this);
            window.location.href = 'dashboard.php';
        });

        document.getElementById('navSearch').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveNav(this);
            alert('Search feature coming soon!');
        });

        document.getElementById('navAdd').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveNav(this);
            window.location.href = 'post.php';
        });

        document.getElementById('navNotification').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveNav(this);
            alert('Notifications feature coming soon!');
        });

        document.getElementById('navAccount').addEventListener('click', function(e) {
            e.preventDefault();
            setActiveNav(this);
        });
    })();
</script>

</body>
</html>