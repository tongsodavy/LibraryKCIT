<?php
require_once 'includes/db.php';

// ១. ទាញយកទិន្នន័យអត្ថបទចុងក្រោយ ដាក់ចូលក្នុង Array ដើម្បីប្រើបានច្រើនដង
$posts_query = $conn->query("SELECT * FROM posts ORDER BY id DESC LIMIT 10");
$posts_data = [];
if ($posts_query) {
    while($row = $posts_query->fetch_assoc()) {
        $posts_data[] = $row;
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bokor&display=swap" rel="stylesheet">
<style>
    /* Global Font Settings */
    body { 
        margin: 0;
        padding: 0;
        font-family: 'Siemreap', 'Segoe UI', sans-serif;
        background-color: #fcfcfc;
    }

    /* ==========================================================================
       ១. រចនាបថ Menu Bar (Navigation Bar) - កែប្រែថ្មី
       ========================================================================== */
    .top-navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 30px;
        background: #ffffff;
        border-bottom: 1px solid #eaeaea;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        position: relative;
        z-index: 999;
        gap: 20px; /* បន្ថែមគម្លាតរវាងធាតុនីមួយៗ */
    }
    .nav-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0; /* ការពារកុំឱ្យ Logo រួញតូច */
    }
    .nav-brand img {
        height: 55px;
        width: auto;
    }
    .brand-text {
        display: flex;
        flex-direction: column;
    }
    .brand-kh {
        font-family: 'Moul', cursive; 
        font-size: 15px;
        color: #1a237e;
        font-weight: bold;
        line-height: 1.3;
    }
    .brand-en {
        font-size: 12px;
        color: #555;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    /* ផ្នែកអក្សររត់កណ្ដាល */
    .navbar-marquee {
        flex: 1; /* ឱ្យវាទាញយកលំហរកណ្ដាលដែលនៅសល់ទាំងអស់ */
        display: flex;
        align-items: center;
        background-color: #f5f5f5; /* ពណ៌ផ្ទៃខាងក្រោយស្រាលងាយស្រួលមើល */
        padding: 6px 15px;
        border-radius: 30px;
        overflow: hidden;
        margin: 0 15px;
    }
    .navbar-marquee marquee {
        font-size: 14px;
        color: #d13ca4; /* ពណ៌ផ្កាឈូកក្រម៉ៅដូចចំណងជើង */
        font-weight: bold;
    }
    .navbar-marquee i {
        margin-right: 8px;
        color: #3f51b5;
    }
    
    /* រុញ Menu និងឧបករណ៍ទៅខាងស្ដាំបង្អស់ */
    .nav-right-container {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-shrink: 0;
    }

    /* Menu List */
    .nav-menu {
        display: flex;
        align-items: center;
        gap: 20px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .nav-item a {
        text-decoration: none;
        color: #555;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 4px;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    .nav-item.active a, .nav-item a:hover {
        color: #3f51b5;
        border-bottom-color: #3f51b5;
    }
    .nav-right-tools {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .lang-switch {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .lang-switch img {
        width: 24px;
        height: 16px;
        object-fit: cover;
        border-radius: 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .menu-toggle {
        display: none;
        font-size: 24px;
        color: #1a237e;
        cursor: pointer;
        background: none;
        border: none;
        outline: none;
    }

    /* ==========================================================================
       ២. រចនាបថ Main Content & Hero Section
       ========================================================================== */
    .container { 
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 10px;
    }
    
    .welcome-hero {
        background-color: #ffffff;
        background-image: radial-gradient(#dcdcdc 1px, transparent 1px);
        background-size: 24px 24px; 
        padding: 40px 20px;
        text-align: center;
        margin-bottom: 40px;
    }
    
    .welcome-title-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 40px;
        max-width: 1100px;
        margin: 0 auto 30px auto;
    }

    .welcome-khmer, .welcome-english {
        flex: 1;
        font-size: 42px;
        color: #d13ca4; 
        line-height: 1.5;
    }

    .welcome-khmer {
        font-family: 'Moul', cursive; 
        text-align: right;
        white-space: nowrap; 
    }

    .welcome-english {
        font-family: 'Moul', cursive; 
        text-align: left;
    }

    .welcome-divider {
        width: 8px;
        height: 50px;
        background-color: #0000ff; 
        border-radius: 2px;
    }

    /* ==========================================
       ផ្នែក Slider 
       ========================================== */
    .image-slider-container {
        max-width: 100%; 
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .mySlides {
        display: none; 
        position: relative;
    }

    .mySlides img {
        width: 100%;
        max-width: 1190px;
        height: 450px; 
        object-fit: cover;
        display: block;
        margin: 0 auto;
    }

    .slide-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 40px 20px 20px 20px;
        text-align: left;
        font-family: 'Siemreap', sans-serif;
        font-size: 16px;
    }

    .photo-gallery-btn {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: #2557a7;
        color: #ffffff;
        padding: 6px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
        font-weight: bold;
        z-index: 10;
    }

    .slider-dots {
        text-align: center;
        margin-top: 15px;
    }

    .dot {
        height: 12px;
        width: 12px;
        margin: 0 4px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.6s ease;
        cursor: pointer;
    }

    .active-dot {
        background-color: #000;
        width: 25px; 
        border-radius: 10px;
    }

    .fade {
        animation-name: fade;
        animation-duration: 1s;
    }
    @keyframes fade {
        from {opacity: 0.4;} 
        to {opacity: 1;}
    }

    /* ==========================================================================
       ៤. Responsive Web Design
       ========================================================================== */
    @media (max-width: 992px) {
        .top-navbar { padding: 10px 20px; }
        .navbar-marquee { display: none; } /* លាក់អក្សររត់លើអេក្រង់តូច ដើម្បីកុំឱ្យចង្អៀត */
        .menu-toggle { display: block; order: 3; }
        .nav-right-container { margin-left: auto; }
        .nav-right-tools { order: 2; margin-right: 15px; }
        .nav-menu {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%; left: 0; width: 100%;
            background: #ffffff;
            box-shadow: 0 10px 15px rgba(0,0,0,0.05);
            border-top: 1px solid #eaeaea;
            padding: 15px 20px;
            box-sizing: border-box;
            gap: 10px;
            align-items: flex-start;
        }
        .nav-menu.show { display: flex; }
        .nav-item { width: 100%; }
        .nav-item a { width: 100%; padding: 10px 0; border-bottom: 1px solid #f5f5f5; }
        .mySlides img { height: 300px; }
    }

    @media (max-width: 768px) {
        .welcome-title-container {
            flex-direction: column;
            gap: 10px;
        }
        .welcome-khmer, .welcome-english {
            text-align: center;
            font-size: 26px;
        }
        .welcome-divider { display: none; }
        .brand-kh { font-size: 13px; }
        .brand-en { font-size: 10px; }
        .nav-brand img { height: 45px; }
        .mySlides img { height: 220px; }
    }
</style>
</head>
<body>

<nav class="top-navbar">
    <div class="nav-brand">
       <img src="uploads/logo.jpg" alt="Logo" onerror="this.src='https://via.placeholder.com/55?text=Logo'">
        <div class="brand-text">
            <span class="brand-kh">វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ឈើទាល</span>
            <span class="brand-en">Kampong Chheuteal Institute of Technology</span>
        </div>
    </div>

    <div class="navbar-marquee">
        <i class="fas fa-bullhorn"></i>
        <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
            <?php 
            if (!empty($posts_data)) {
                $marquee_text = [];
                foreach ($posts_data as $post) {
                    $marquee_text[] = "🔹 " . htmlspecialchars($post['title']);
                }
                echo implode(" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ", $marquee_text);
            } else {
                echo "សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងបណ្ណាល័យ នៃវិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ឈើទាល!";
            }
            ?>
        </marquee>
    </div>

    <div class="nav-right-container">
        <button class="menu-toggle" id="mobile-menu-btn" aria-label="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="nav-menu" id="nav-menu-list">
            <li class="nav-item active"><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="nav-item"><a href="catalog.php"><i class="fas fa-book"></i> Books In Library</a></li>
            <li class="nav-item"><a href="member/login.php"><i class="fas fa-sign-in-alt"></i> Sign In (Admin)</a></li>
        </ul>

        <div class="nav-right-tools">
            <div class="lang-switch">
                <img src="https://flagcdn.com/w20/kh.png" alt="Khmer" title="ភាសាខ្មែរ">
                <img src="https://flagcdn.com/w20/us.png" alt="English" title="English">
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-hero">
        <div class="welcome-title-container">
            <div class="welcome-khmer">
                បណ្ណាល័យសូមស្វាគមន៍
            </div>
            <div class="welcome-divider"></div>
            <div class="welcome-english">
                Welcome to Library
            </div>
        </div>

        <div class="image-slider-container">
            <?php if (!empty($posts_data)): ?>
                <?php foreach ($posts_data as $index => $post): ?>
                    <div class="mySlides fade">
                        <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" 
                             onerror="this.src='https://via.placeholder.com/1000x450?text=No+Image+Found'">
                        <div class="slide-caption">
                            <?= htmlspecialchars($post['title']) ?>
                        </div>
                        <a href="gallery.php" class="photo-gallery-btn">Photo Gallery</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mySlides fade" style="display: block;">
                    <img src="https://via.placeholder.com/1000x450?text=Welcome+To+Library">
                    <div class="slide-caption">សូមស្វាគមន៍មកកាន់ប្រព័ន្ធគ្រប់គ្រងបណ្ណាល័យ</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="slider-dots">
            <?php if (!empty($posts_data)): ?>
                <?php foreach ($posts_data as $index => $post): ?>
                    <span class="dot" onclick="currentSlide(<?= $index + 1 ?>)"></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let slideIndex = 0;
    let slideTimer;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");

    if (slides.length > 0) {
        showSlides();
    }

    function showSlides() {
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
        }
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1; }    
        
        for (let i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active-dot", "");
        }
        
        if (slides[slideIndex-1]) {
            slides[slideIndex-1].style.display = "block";  
        }
        if (dots.length > 0 && dots[slideIndex-1]) {
            dots[slideIndex-1].className += " active-dot";
        }
        
        clearTimeout(slideTimer);
        slideTimer = setTimeout(showSlides, 5000); 
    }

    function currentSlide(n) {
        clearTimeout(slideTimer); 
        slideIndex = n;
        
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (let i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active-dot", "");
        }
        
        if (slides[slideIndex-1]) {
            slides[slideIndex-1].style.display = "block";
        }
        if (dots[slideIndex-1]) {
            dots[slideIndex-1].className += " active-dot";
        }
        
        slideTimer = setTimeout(showSlides, 5000);
    }

    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const navMenuList = document.getElementById('nav-menu-list');

    if (mobileMenuBtn && navMenuList) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenuList.classList.toggle('show');
        });
    }
</script>

</body>
</html>