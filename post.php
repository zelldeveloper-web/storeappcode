<?php
// post.php - Create new post with hashtag support
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['store_name'] ?? 'User';
$userAvatar = $_SESSION['user_avatar'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Create Post - Store Instant</title>
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

        #post-container {
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

        .header .right-btn {
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

        .header .right-btn:active {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .header .right-btn.danger {
            color: #ff4444;
        }

        #step1 {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 16px;
        }

        #step1 .post-type-section {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        #step1 .post-type-btn {
            flex: 1;
            padding: 14px 0;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            color: #666;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            text-align: center;
        }

        #step1 .post-type-btn.active {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.15);
            color: #fff;
        }

        #step1 .post-type-btn:active {
            transform: scale(0.95);
        }

        #step1 .post-type-btn i {
            margin-right: 8px;
        }

        #step1 .upload-area {
            flex: 1;
            background: rgba(255,255,255,0.04);
            border: 2px dashed rgba(255,255,255,0.08);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 200px;
        }

        #step1 .upload-area:active {
            transform: scale(0.98);
        }

        #step1 .upload-area i {
            font-size: 48px;
            color: #333;
            margin-bottom: 12px;
        }

        #step1 .upload-area p {
            color: #666;
            font-size: 14px;
        }

        #step1 .upload-area .hint {
            color: #444;
            font-size: 12px;
            margin-top: 4px;
        }

        #step1 .bottom-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            padding-bottom: 8px;
        }

        #step1 .bottom-actions .btn-story {
            flex: 1;
            padding: 14px 0;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            color: #888;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            text-align: center;
        }

        #step1 .bottom-actions .btn-story:active {
            transform: scale(0.95);
        }

        #step1 .bottom-actions .btn-next {
            flex: 1;
            padding: 14px 0;
            background: rgba(255,255,255,0.06);
            border: none;
            border-radius: 8px;
            color: #666;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            text-align: center;
            opacity: 0.3;
            pointer-events: none;
        }

        #step1 .bottom-actions .btn-next.active {
            opacity: 1;
            pointer-events: auto;
            background: #fff;
            color: #000;
        }

        #step1 .bottom-actions .btn-next:active {
            transform: scale(0.95);
        }

        #step2 {
            flex: 1;
            display: none;
            flex-direction: column;
            background: #000;
        }

        #step2.visible {
            display: flex;
        }

        #step2 .preview-container {
            flex: 1;
            overflow-y: auto;
            padding: 0 0 80px;
        }

        #step2 .preview-container::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        #step2 .post-preview {
            background: #000;
        }

        #step2 .post-preview .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
        }

        #step2 .post-preview .post-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        #step2 .post-preview .post-author {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }

        #step2 .post-preview .post-image-wrapper {
            width: 100%;
            position: relative;
            background: #000;
            overflow: hidden;
            touch-action: pan-y;
        }

        #step2 .post-preview .post-image-wrapper .slide-track {
            display: flex;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }

        #step2 .post-preview .post-image-wrapper .slide-track .slide {
            flex: 0 0 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            min-height: 300px;
        }

        #step2 .post-preview .post-image-wrapper .slide-track .slide img,
        #step2 .post-preview .post-image-wrapper .slide-track .slide video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #000;
            max-height: 400px;
        }

        #step2 .post-preview .post-image-wrapper .slide-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 5;
            pointer-events: none;
        }

        #step2 .post-preview .post-image-wrapper .slide-indicators .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transition: all 0.3s ease;
        }

        #step2 .post-preview .post-image-wrapper .slide-indicators .dot.active {
            background: #fff;
            width: 16px;
            border-radius: 3px;
        }

        #step2 .post-preview .post-caption {
            padding: 12px 16px;
        }

        #step2 .post-preview .post-caption .caption-input {
            width: 100%;
            padding: 12px 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            color: #fff;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            resize: none;
            min-height: 60px;
        }

        #step2 .post-preview .post-caption .caption-input::placeholder {
            color: #444;
        }

        #step2 .post-preview .post-caption .caption-input:focus {
            border-bottom-color: rgba(255,255,255,0.15);
        }

        #step2 .post-preview .post-caption .char-count {
            text-align: right;
            font-size: 12px;
            color: #444;
            margin-top: 4px;
        }

        #step2 .post-preview .post-caption .char-count.limit {
            color: #ff4444;
        }

        /* Hashtag styling di preview */
        #step2 .post-preview .post-caption .caption-preview {
            color: #fff;
            font-size: 15px;
            line-height: 1.5;
            padding: 12px 0;
            min-height: 60px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        #step2 .post-preview .post-caption .caption-preview .hashtag {
            color: #888;
        }

        #step2 .bottom-actions {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,0.04);
            padding: 12px 16px 16px;
            display: flex;
            gap: 12px;
        }

        #step2 .bottom-actions .btn-post {
            flex: 1;
            padding: 14px 0;
            background: #fff;
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
            text-align: center;
        }

        #step2 .bottom-actions .btn-post:active {
            transform: scale(0.95);
            opacity: 0.85;
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
            #step1 .post-type-btn {
                font-size: 13px;
                padding: 12px 0;
            }
            #step1 .bottom-actions .btn-story,
            #step1 .bottom-actions .btn-next {
                font-size: 14px;
                padding: 12px 0;
            }
            #step2 .post-preview .post-image-wrapper .slide-track .slide {
                min-height: 200px;
            }
            #step2 .post-preview .post-image-wrapper .slide-track .slide img,
            #step2 .post-preview .post-image-wrapper .slide-track .slide video {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>

<div id="post-container">
    <!-- STEP 1 -->
    <div id="step1">
        <header class="header">
            <button class="back-btn" id="backBtn1">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>New Post</h1>
            <span style="width:30px;"></span>
        </header>

        <div class="post-type-section">
            <button class="post-type-btn active" data-type="photo" id="photoBtn">
                <i class="fas fa-image"></i> Photo
            </button>
            <button class="post-type-btn" data-type="video" id="videoBtn">
                <i class="fas fa-video"></i> Video
            </button>
        </div>

        <div class="upload-area" id="uploadArea">
            <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
            <p id="uploadText">Tap to select photos</p>
            <span class="hint" id="uploadHint">Max 18 photos</span>
        </div>

        <div class="bottom-actions">
            <button class="btn-story" id="storyBtn">Post to Story</button>
            <button class="btn-next" id="nextBtn">Next</button>
        </div>
    </div>

    <!-- STEP 2 -->
    <div id="step2">
        <header class="header">
            <button class="back-btn" id="backBtn2">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>Select Song</h1>
            <button class="right-btn danger" id="deleteBtn">Delete</button>
        </header>

        <div class="preview-container">
            <div class="post-preview" id="postPreview">
                <div class="post-header">
                    <div class="post-avatar" style="<?php echo $userAvatar ? 'background-image: url(' . $userAvatar . ');' : ''; ?>">
                        <?php echo $userAvatar ? '' : strtoupper(substr($userName, 0, 1)); ?>
                    </div>
                    <span class="post-author"><?php echo htmlspecialchars($userName); ?></span>
                </div>
                <div class="post-image-wrapper" id="previewWrapper">
                    <div class="slide-track" id="previewTrack"></div>
                    <div class="slide-indicators" id="previewIndicators"></div>
                </div>
                <div class="post-caption">
                    <div class="caption-preview" id="captionPreview">Write a caption...</div>
                    <textarea class="caption-input" id="captionInput" placeholder="Write a caption..." maxlength="500"></textarea>
                    <div class="char-count" id="captionCount">0/500</div>
                </div>
            </div>
        </div>

        <div class="bottom-actions">
            <button class="btn-post" id="postBtn">Post</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const backBtn1 = document.getElementById('backBtn1');
        const backBtn2 = document.getElementById('backBtn2');
        const photoBtn = document.getElementById('photoBtn');
        const videoBtn = document.getElementById('videoBtn');
        const uploadArea = document.getElementById('uploadArea');
        const nextBtn = document.getElementById('nextBtn');
        const storyBtn = document.getElementById('storyBtn');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const deleteBtn = document.getElementById('deleteBtn');
        const postBtn = document.getElementById('postBtn');
        const captionInput = document.getElementById('captionInput');
        const captionCount = document.getElementById('captionCount');
        const captionPreview = document.getElementById('captionPreview');
        const previewTrack = document.getElementById('previewTrack');
        const previewIndicators = document.getElementById('previewIndicators');

        let selectedFiles = [];
        let selectedType = 'photo';
        let currentSlide = 0;

        function navigateToWithTransition(url) {
            const overlay = document.createElement('div');
            overlay.className = 'page-transition';
            document.body.appendChild(overlay);
            requestAnimationFrame(() => { overlay.classList.add('active'); });
            setTimeout(() => { window.location.href = url; }, 350);
        }

        function formatCaption(text) {
            return text.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
        }

        function updateCaptionPreview() {
            const text = captionInput.value.trim() || 'Write a caption...';
            captionPreview.innerHTML = formatCaption(text);
        }

        backBtn1.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToWithTransition('dashboard.php');
        });

        backBtn2.addEventListener('click', function(e) {
            e.preventDefault();
            step2.classList.remove('visible');
            step1.style.display = 'flex';
        });

        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            selectedFiles = [];
            step2.classList.remove('visible');
            step1.style.display = 'flex';
            updateUI();
        });

        photoBtn.addEventListener('click', function() {
            photoBtn.classList.add('active');
            videoBtn.classList.remove('active');
            selectedType = 'photo';
            document.getElementById('uploadText').textContent = 'Tap to select photos';
            document.getElementById('uploadHint').textContent = 'Max 18 photos';
            document.getElementById('uploadIcon').className = 'fas fa-cloud-upload-alt';
        });

        videoBtn.addEventListener('click', function() {
            videoBtn.classList.add('active');
            photoBtn.classList.remove('active');
            selectedType = 'video';
            document.getElementById('uploadText').textContent = 'Tap to select videos';
            document.getElementById('uploadHint').textContent = 'Max 5 videos';
            document.getElementById('uploadIcon').className = 'fas fa-video';
        });

        uploadArea.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = selectedType === 'photo' ? 'image/*' : 'video/*';
            input.multiple = true;
            input.style.display = 'none';
            document.body.appendChild(input);

            input.click();

            input.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                if (files.length > 0) {
                    const maxFiles = selectedType === 'photo' ? 18 : 5;
                    if (selectedFiles.length + files.length > maxFiles) {
                        alert('Max ' + maxFiles + ' ' + selectedType + 's allowed');
                        document.body.removeChild(input);
                        return;
                    }
                    selectedFiles = [...selectedFiles, ...files];
                    updateUI();
                    goToStep2();
                }
                document.body.removeChild(input);
            });
        });

        function goToStep2() {
            step1.style.display = 'none';
            step2.classList.add('visible');
            renderPreview();
        }

        function renderPreview() {
            previewTrack.innerHTML = '';
            previewIndicators.innerHTML = '';

            if (selectedFiles.length === 0) return;

            selectedFiles.forEach((file, index) => {
                const slide = document.createElement('div');
                slide.className = 'slide';
                const url = URL.createObjectURL(file);
                if (file.type.startsWith('video/')) {
                    slide.innerHTML = `<video src="${url}" controls></video>`;
                } else {
                    slide.innerHTML = `<img src="${url}" alt="preview">`;
                }
                previewTrack.appendChild(slide);

                if (selectedFiles.length > 1) {
                    const dot = document.createElement('span');
                    dot.className = 'dot' + (index === 0 ? ' active' : '');
                    previewIndicators.appendChild(dot);
                }
            });

            currentSlide = 0;
            updateSlide();

            const wrapper = document.getElementById('previewWrapper');
            if (selectedFiles.length > 1) {
                let startX = 0, currentX = 0, isDragging = false;

                wrapper.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                    isDragging = true;
                    previewTrack.style.transition = 'none';
                }, { passive: true });

                wrapper.addEventListener('touchmove', function(e) {
                    if (!isDragging) return;
                    currentX = e.touches[0].clientX;
                    const diff = currentX - startX;
                    const offset = -currentSlide * 100 + (diff / wrapper.offsetWidth) * 100;
                    previewTrack.style.transform = 'translateX(' + offset + '%)';
                }, { passive: true });

                wrapper.addEventListener('touchend', function(e) {
                    if (!isDragging) return;
                    isDragging = false;
                    const diff = currentX - startX;
                    if (Math.abs(diff) > 50) {
                        if (diff < 0 && currentSlide < selectedFiles.length - 1) {
                            currentSlide++;
                        } else if (diff > 0 && currentSlide > 0) {
                            currentSlide--;
                        }
                    }
                    updateSlide();
                }, { passive: true });
            }
        }

        function updateSlide() {
            previewTrack.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            previewTrack.style.transform = 'translateX(-' + currentSlide * 100 + '%)';

            const dots = previewIndicators.querySelectorAll('.dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlide);
            });
        }

        function updateUI() {
            const hasFiles = selectedFiles.length > 0;
            nextBtn.classList.toggle('active', hasFiles);
        }

        captionInput.addEventListener('input', function() {
            const len = this.value.length;
            captionCount.textContent = len + '/500';
            captionCount.classList.toggle('limit', len >= 500);
            updateCaptionPreview();
        });

        storyBtn.addEventListener('click', function() {
            alert('Post to Story feature coming soon!');
        });

        nextBtn.addEventListener('click', function() {
            if (selectedFiles.length === 0) return;
            goToStep2();
        });

        postBtn.addEventListener('click', function() {
            const caption = captionInput.value.trim();
            if (selectedFiles.length === 0) {
                alert('No files selected');
                return;
            }

            postBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
            postBtn.disabled = true;

            // Convert files to base64
            const imageDataUrls = [];
            let processed = 0;

            selectedFiles.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageDataUrls.push(e.target.result);
                    processed++;
                    if (processed === selectedFiles.length) {
                        // Save to posts.json
                        const postData = {
                            author: '<?php echo addslashes($userName); ?>',
                            avatar: '<?php echo addslashes($userAvatar); ?>',
                            images: imageDataUrls,
                            caption: caption,
                            time: new Date().toLocaleString(),
                            likes: 0,
                            liked: false,
                            currentSlide: 0
                        };

                        fetch('backend/save_post.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(postData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            postBtn.innerHTML = 'Post';
                            postBtn.disabled = false;
                            if (data.success) {
                                alert('Post created successfully!');
                                navigateToWithTransition('dashboard.php');
                            } else {
                                alert('Failed to create post: ' + data.message);
                            }
                        })
                        .catch(() => {
                            postBtn.innerHTML = 'Post';
                            postBtn.disabled = false;
                            alert('Network error. Please try again.');
                        });
                    }
                };
                reader.readAsDataURL(file);
            });
        });

        updateUI();
        updateCaptionPreview();
    })();
</script>

</body>
</html>