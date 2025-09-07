<?php
session_start();
include 'db.php';

// Set default language to English if not set
if (!isset($_SESSION['language_id'])) {
    $_SESSION['language_id'] = 1;
}

// Handle language selection
if (isset($_GET['language_id'])) {
    $_SESSION['language_id'] = intval($_GET['language_id']);
}

$current_language_id = $_SESSION['language_id'];

// Fetch language list
$languages = [];
$language_query = mysqli_query($conn, "SELECT * FROM about_ministry_languages");
while ($lang = mysqli_fetch_assoc($language_query)) {
    $languages[$lang['language_id']] = $lang['language_name'];
}

// Fetch ministry information
$about_query = mysqli_query($conn, "SELECT * FROM about_ministry WHERE language_id = $current_language_id");
$about_data = mysqli_fetch_assoc($about_query);

// Fetch leadership information
$leaders = [];
$leaders_query = mysqli_query($conn, "SELECT * FROM fivefold_ministry_leaders WHERE language_id = $current_language_id");
while ($leader = mysqli_fetch_assoc($leaders_query)) {
    $leaders[] = $leader;
}

// Fetch church social media information
$social_query = mysqli_query($conn, "SELECT * FROM church_socialmedia");
$social_data = mysqli_fetch_assoc($social_query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Eagles Food Ministry</title>
     <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Google Fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Base Styles */
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #8a9a5b;
        --accent-color: #007bff;
        --light-color: #f8f8f8;
        --dark-color: #333;
        --text-color: #444;
        --white: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --font-primary: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif,Noto Serif Telugu, Hind Guntur, Gidugu, Timmana, Ramabhadra,Anek Telugu;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: var(--light-color);
        color: var(--dark-color);
        line-height: 1.6;
        overflow-x: hidden;
        font-family: var(--font-primary);
    }

    /* Font Awesome fallback styles */
    .fa {
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }

    /* Header */
    header {
        background-color: #eeeeee;
        padding: 1rem 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-img {
        height: 50px;
        width: auto;
        border-radius: 50%;
        border: 2px solid var(--accent-color);
    }

    .logo-text {
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(147, 143, 143, 0.3);
    }

    .menu-toggle {
        background: transparent;
        border: none;
        color: var(--primary-color);
        font-size: 1.5rem;
        cursor: pointer;
        display: none;
    }

    /* Navigation */
    .menu {
        display: flex;
        gap: 1rem;
    }

    .menu a {
        color: #333;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .menu a:hover,
    .menu a.active {
        background-color: rgb(78, 156, 234);
        color: white;
    }

    /* Language Dropdown */
    .language-dropdown {
        position: relative;
    }

    .language-dropdown-btn {
        background: transparent;
        border: none;
        color: #1b57d8;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .language-dropdown-content {
        display: none;
        position: absolute;
        background-color: var(--white);
        min-width: 250px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 4px;
        overflow: hidden;
        top: 100%;
        left: 0;
    }

    .language-dropdown:hover .language-dropdown-content {
        display: block;
    }

    .language-search {
        padding: 10px;
        background-color: #f1f1f1;
    }

    .language-search input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .language-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .language-item {
        display: flex;
        align-items: center;
        padding: 10px;
        text-decoration: none;
        color: var(--dark-color);
        border-bottom: 1px solid #eee;
        gap: 10px;
        cursor: pointer;
    }

    .language-item:hover {
        background-color: #2680f6;
        color: white;
    }

    /* Main Content */
    main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
        background-color: var(--light-color);
    }

    .page-title {
        font-family: 'Merriweather', serif;
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .page-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background-color: var(--accent-color);
    }

    .section {
        background-color: var(--white);
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .section-title {
        font-family: 'Merriweather', serif;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--accent-color);
    }

    /* Ministry About */
    .ministry-about {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .ministry-text {
        flex: 1;
        min-width: 300px;
    }

    .ministry-image {
        flex: 1;
        min-width: 300px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
        height: 350px;
        background-color: #e0e0e0;
    }

    .ministry-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Leadership - Horizontal scroll for mobile */
    .leadership-container {
        position: relative;
        margin: 0 -1rem;
        padding: 0 1rem;
    }

    .leadership-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 1.5rem;
        padding: 0.5rem 0;
        scrollbar-width: thin;
        -ms-overflow-style: none;
    }

    .leadership-grid::-webkit-scrollbar {
        display: none;
    }

    .leader-card {
            flex: 0 0 auto;
            width: 320px;
            height: 500px; /* Increased height to accommodate larger image */
            scroll-snap-align: start;
            background-color: var(--light-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .leader-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .leader-image {
            height: 280px; /* Increased image height */
            overflow: hidden;
            position: relative;
        }

        .leader-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .leader-card:hover .leader-image img {
            transform: scale(1.05);
        }

        .leader-image::before {
            content: '[Leader Photo]';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #999;
            font-size: 14px;
            display: none;
        }

        .leader-image:empty::before {
            display: block;
        }

        .leader-info {
            padding: 18px; /* Reduced padding for less space */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .leader-name {
            font-family: 'Merriweather', serif;
            color: var(--primary-color);
            margin-bottom: 6px; /* Reduced margin */
            font-size: 1.4rem;
            line-height: 1.3;
        }

        .leader-role {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 12px; /* Reduced margin */
            font-size: 1rem;
        }

        .leader-info p {
            margin-bottom: 15px;
            flex-grow: 1;
            font-size: 0.95rem;
            line-height: 1.5;
            color: #555;
        }


    /* Location & Contact */
    .location-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .location-info {
        flex: 1;
        min-width: 280px;
        word-break: break-word;
    }

    .contact-details {
        margin-top: 1.5rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 15px;
    }

    .contact-item i {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background-color: var(--light-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }

    .google-map-container {
        flex: 1;
        min-width: 300px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--shadow);
        position: relative;
    }

    .video-preview {
        position: relative;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .video-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(0,0,0,0.6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    /* YouTube Videos Section */
    .yt-video-section {
        background: #f8f9fa;
        padding: 2rem 1rem;
        margin: 0 -1rem 2rem;
        position: relative;
    }

    .video-section-wrapper {
        position: relative;
        margin: 0 auto;
        max-width: 1200px;
    }

    .video-scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        z-index: 10;
        display: none;
    }

    .video-scroll-btn.left {
        left: 10px;
    }

    .video-scroll-btn.right {
        right: 10px;
    }

    .video-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 20px;
        padding: 10px 0;
        scrollbar-width: thin;
        -ms-overflow-style: none;
    }

    .video-grid::-webkit-scrollbar {
        display: none;
    }

    .video-card {
        flex: 0 0 300px;
        scroll-snap-align: start;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
    }

    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        background: #000;
    }

    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .video-info {
        padding: 15px;
    }

    .video-info h3 {
        margin: 0 0 10px 0;
        font-size: 1rem;
        line-height: 1.4;
        color: #333;
        font-weight: 600;
    }

    .video-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #606060;
        margin-bottom: 10px;
    }

    .yt-link {
        display: inline-block;
        background: #ff0000;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.8rem;
        transition: background 0.3s ease;
        font-weight: 500;
    }

    .yt-link:hover {
        background: #cc0000;
    }

    .see-more-container {
        text-align: center;
        margin-top: 20px;
    }

    .see-more-btn {
        display: inline-block;
        background: #ff0000;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .see-more-btn:hover {
        background: #cc0000;
    }

    /* Form */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    /* Footer */
    footer {
        background: var(--primary-color);
        color: var(--white);
        padding: 2rem 1rem;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-section h3 {
        margin-bottom: 1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-section h3:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 2px;
        background-color: var(--accent-color);
    }

    .footer-section p {
        margin-bottom: 0.8rem;
        word-break: break-word;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: var(--white);
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background-color: var(--accent-color);
    }

    .copyright {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    /* Responsive Design */
    @media (min-width: 768px) {
          .leader-card {
                width: 280px;
                height: 460px;
            }
            
            .leader-image {
                height: 240px;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-group:last-child {
            grid-column: 1 / -1;
        }
        
        .submit-btn {
            width: auto;
        }
        
        .footer-content {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 992px) {
        .leadership-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            overflow-x: visible;
        }
        
        .leader-card {
            flex: 1;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            overflow-x: visible;
        }
        
        .video-card {
            flex: 1;
        }
        
        .video-scroll-btn {
            display: block;
        }
    }

    @media (max-width: 900px) {
        .menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color:#ffffff;
            flex-direction: column;
            padding: 1rem;
        }

        .menu.active {
            display: flex;
        }

        .menu-toggle {
            display: block;
        }

        .language-dropdown-content {
            right: 0;
            left: auto;
        }
    }

    @media (max-width: 768px) {
        .ministry-about {
            flex-direction: column;
        }

        .location-container {
            flex-direction: column;
        }
        
        .google-map-container {
            height: 250px;
        }
        
        .contact-item {
            flex-direction: row;
            align-items: center;
        }
    }

    @media (max-width: 480px) {
         .leader-card {
                width: 260px;
                height: 440px;
            }
            
            .leader-image {
                height: 220px;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .leader-name {
                font-size: 1.2rem;
            }
        .page-title {
            font-size: 1.8rem;
        }
        
        .section {
            padding: 1rem;
        }
        
        .leader-card {
            flex: 0 0 85%;
        }
        
        .video-card {
            flex: 0 0 85%;
        }
    }
</style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="images/appicon.png" alt="Eagle's Food Logo" class="logo-img" />
            <span class="logo-text">Eagles Food</span>
        </div>

        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="menu" id="mainMenu">
            <a href="mainpage.php"><i class="fas fa-house"></i> Home</a>

            <!-- Enhanced Language Dropdown with Search -->
            <div class="language-dropdown" id="languageDropdown">
                <button class="language-dropdown-btn">
                    <span><i class="fas fa-globe"></i> <?= $languages[$current_language_id] ?? 'English' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="language-dropdown-content">
                    <div class="language-search">
                        <input type="text" id="languageSearch" placeholder="Search languages...">
                    </div>
                    <div class="language-list" id="languageList">
                        <?php if (count($languages) > 0): ?>
                            <?php foreach ($languages as $id => $name): ?>
                                <div class="language-item" data-lang-id="<?= $id ?>">
                                    <span><?= $name ?></span>
                                    <small>ID: <?= $id ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-results">No languages available</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <a href="seals.php" target="myTab"><i class="fas fa-scroll"></i> Seven Seals</a>
            <a href="church_ages.php" target="myTab"><i class="fas fa-church"></i> Seven Church Ages</a>
            <a href="teachings_list.php"><i class="fas fa-book"></i> Teachings</a>
            <a href="#" class="active"><i class="fas fa-envelope"></i> Contact Us</a>
        </div>
    </header>

    <main>
        <h1 class="page-title">Contact Lord Jesus Christ Praying Tabernacle</h1>

        <section class="section">
            <h2 class="section-title"><i class="fas fa-church"></i> About Our Ministry</h2>
            <div class="ministry-about">
                <div class="ministry-text">
                    <?php if ($about_data): ?>
                        <p><?= nl2br($about_data['about_text']) ?></p>
                    <?php else: ?>
                        <p>Lord Jesus Christ Praying Tabernacle is a vibrant Christian community dedicated to spreading the Word of God and nurturing spiritual growth. Founded in 2005, our ministry has been serving the community with love, compassion, and biblical truth.</p>
                        <p>Our mission is to provide spiritual nourishment through faithful teaching, worship, and fellowship. We believe in the power of the Holy Spirit to transform lives and bring healing to broken hearts.</p>
                        <p>At Eagles Food, we focus on:</p>
                        <ul>
                            <li>Expounding the Seven Seals and Seven Church Ages</li>
                            <li>Teaching sound biblical doctrine</li>
                            <li>Fostering a community of love and support</li>
                            <li>Reaching out to the lost and hurting</li>
                        </ul>
                        <p>We welcome you to join us for our weekly services, Bible studies, and special events as we grow together in faith and understanding of God's Word.</p>
                    <?php endif; ?>
                </div>
                <div class="ministry-image">
                    <?php if ($about_data && !empty($about_data['image'])): ?>
                        <img src="http://localhost/eaglesfood_adminpanel/<?= $about_data['image'] ?>" alt="Church Building">
                    <?php else: ?>
                        [Church Building Image]
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title"><i class="fas fa-user-friends"></i> Our Five Fold Ministry</h2>
            <div class="leadership-container">
                <div class="leadership-grid">
                    <?php if (count($leaders) > 0): ?>
                        <?php foreach ($leaders as $leader): ?>
                            <div class="leader-card">
                                <div class="leader-image">
                                    <?php if (!empty($leader['image'])): ?>
                                        <img src="http://localhost/eaglesfood_adminpanel/<?= $leader['image'] ?>" alt="<?= $leader['name'] ?>">
                                    <?php else: ?>
                                        [Leader Photo]
                                    <?php endif; ?>
                                </div>
                                <div class="leader-info">
                                    <h3 class="leader-name"><?= $leader['name'] ?></h3>
                                    <p class="leader-role"><?= $leader['role'] ?></p>
                                    <p><?= $leader['bio'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="leader-card">
                            <div class="leader-image">
                                [Pastor Photo]
                            </div>
                            <div class="leader-info">
                                <h3 class="leader-name">Rev. Michael Johnson</h3>
                                <p class="leader-role">Senior Pastor</p>
                                <p>Pastor Michael has been serving at Eagles Food Ministry for 12 years. He holds a Master of Divinity degree and is passionate about expository preaching and discipleship.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- YouTube Videos Section -->
        <section class="yt-video-section">
            <h2 class="section-title"><i class="fab fa-youtube"></i> Jesus Christ True Bride Videos</h2>
            <div class="video-section-wrapper">
                <button class="video-scroll-btn left" onclick="scrollVideos(-1)">‹</button>
                
                <div class="video-grid" id="videoGrid">
                    <?php
                    // Function to extract YouTube ID from URL
                    function getYoutubeID($url)
                    {
                        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
                        preg_match($pattern, $url, $matches);
                        return isset($matches[1]) ? $matches[1] : '';
                    }

                    // Fetch videos from database
                    $sql = "SELECT * FROM `jesuschristtruebride_videos` ORDER BY `last_updated` DESC";
                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($video = mysqli_fetch_assoc($result)) {
                            $videoId = getYoutubeID($video['url']);
                            if (!empty($videoId)) {
                    ?>
                                <div class="video-card">
                                    <div class="video-wrapper">
                                        <iframe src="https://www.youtube.com/embed/<?= $videoId ?>"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                    </div>
                                    <div class="video-info">
                                        <h3><?= htmlspecialchars($video['title']) ?></h3>
                                        <div class="video-meta">
                                            <span>Views: <?= number_format($video['views']) ?></span> |
                                            <span>Updated: <?= date('M j, Y', strtotime($video['last_updated'])) ?></span>
                                        </div>
                                        <a href="https://youtube.com/watch?v=<?= $videoId ?>" target="myTab" class="yt-link">
                                            Watch on YouTube
                                        </a>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    } else {
                        echo '<p class="text-center">No videos available at this time.</p>';
                    }
                    ?>
                </div>
                
                <button class="video-scroll-btn right" onclick="scrollVideos(1)">›</button>
            </div>
            <div class="see-more-container">
                <a href="https://www.youtube.com/@jesuschristtruebride9964" target="myTab" class="see-more-btn" id="seeMoreBtn">See More</a>
            </div>
        </section>

        <!-- Location & Contact Section -->
        <section class="section">
            <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Location & Contact</h2>
            <div class="location-container">
                
                <!-- Contact Info -->
                <div class="location-info">
                    <p>We are located in the heart of the city, easily accessible by public transportation with ample parking available.</p>

                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-map-pin"></i>
                            <div>
                                <strong>Address</strong><br>
                                <?= $social_data['address'] ?? '123 Faith Avenue, Grace City, GC 12345' ?>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Phone</strong><br>
                                <?= $social_data['phone_number'] ?? '(555) 123-4567' ?>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                <?= $social_data['email'] ?? 'info@eaglesfoodministry.org' ?>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Service Times</strong><br>
                                <?php if ($social_data): ?>
                                    Sunday: <?= $social_data['service_from'] ?> to <?= $social_data['servicehour_to'] ?><br>
                                    Friday: Fasting & Prayer<br>
                                    Wednesday Bible Study: 7:00 PM
                                <?php else: ?>
                                    Sunday: 9:00 AM & 1:00 AM<br>
                                    Friday: Fasting & Prayer<br>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map / Video Section -->
                <div class="google-map-container">
                    <div class="video-preview" onclick="playMapVideo(this)">
                        <img src="./images/jesuschriattruebride_banner.png" alt="Church Location Map" class="video-thumbnail">
                        <div class="play-button">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title"><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
            <form class="form-grid" method="POST" action="contact_request_insert.php">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" class="form-control" name="fullname" placeholder="Your name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" class="form-control" name="email" placeholder="Your email" required>
                </div>
            
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" class="form-control" name="phone_number" placeholder="Your phone number">
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" class="form-control" name="subject" placeholder="Message subject" required>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="message">Message</label>
                    <textarea id="message" class="form-control" name="Message" placeholder="Your message" required></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" class="submit-btn" onclick=submitAlert()>Send Message</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Eagles Food</h3>
                <p>A ministry dedicated to providing spiritual nourishment through faithful teaching of God's Word and fostering a community of believers.</p>
                <div class="social-links">
                    <?php if ($social_data && !empty($social_data['facebook'])): ?>
                        <a href="<?= $social_data['facebook'] ?>" target="mytab"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if ($social_data && !empty($social_data['youtube'])): ?>
                        <a href="<?= $social_data['youtube'] ?>" target="mytab"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if ($social_data && !empty($social_data['whatsapp'])): ?>
                        <a href="<?= $social_data['whatsapp'] ?>" target="mytab"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="mainpage.php" style="color: white; text-decoration: none;">Home</a></p>
                <p><a href="teachings_list.php" style="color: white; text-decoration: none;">Teachings</a></p>
                <p><a href="seals.php" style="color: white; text-decoration: none;">Seven Seals</a></p>
                <p><a href="church_ages.php" style="color: white; text-decoration: none;">Church Ages</a></p>
            </div>

            <div class="footer-section">
                <h3>Contact Information</h3>
                <p><i class="fas fa-map-marker-alt"></i> <?= $social_data['address'] ?? '123 Faith Avenue, Grace City' ?></p>
                <p><i class="fas fa-phone"></i> <?= $social_data['phone_number'] ?? '(555) 123-4567' ?></p>
                <p><i class="fas fa-envelope"></i> <?= $social_data['email'] ?? 'info@eaglesfoodministry.org' ?></p>
            </div>
        </div>

        <div class="copyright">
            <p>©<script>
            document.write(new Date().getFullYear())
          </script> Eagles Food. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const mainMenu = document.getElementById('mainMenu');

        menuToggle.addEventListener('click', () => {
            mainMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuToggle.contains(e.target) && !mainMenu.contains(e.target)) {
                mainMenu.classList.remove('active');
            }
        });

        // Language search functionality
        const languageSearch = document.getElementById('languageSearch');
        const languageItems = document.querySelectorAll('.language-item');

        if (languageSearch) {
            languageSearch.addEventListener('input', () => {
                const searchTerm = languageSearch.value.toLowerCase();

                languageItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Language selection
        document.querySelectorAll('.language-item').forEach(item => {
            item.addEventListener('click', () => {
                const langId = item.getAttribute('data-lang-id');
                window.location.href = `?language_id=${langId}`;
            });
        });

     

        // Video scrolling function
        function scrollVideos(direction) {
            const videoGrid = document.getElementById('videoGrid');
            const scrollAmount = 300;
            videoGrid.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }

        // Leadership cards scrolling
        function scrollLeaders(direction) {
            const leaderGrid = document.querySelector('.leadership-grid');
            const scrollAmount = 300;
            leaderGrid.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }

        function submitAlert(){
            alert("Your message has been sent successfully. We will get back to you shortly.");
        }

        // Map video player
        function playMapVideo(container) {
            container.innerHTML = `
                <video controls autoplay style="width:100%; height:100%; border-radius:8px;">
                    <source src="./images/map_kurupam.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>`;
        }

        // Show scroll buttons only when needed
        function checkScrollButtons() {
            const videoGrid = document.getElementById('videoGrid');
            const leaderGrid = document.querySelector('.leadership-grid');
            
            // For video scroll buttons
            if (videoGrid.scrollWidth > videoGrid.clientWidth) {
                document.querySelectorAll('.video-scroll-btn').forEach(btn => {
                    btn.style.display = 'block';
                });
            } else {
                document.querySelectorAll('.video-scroll-btn').forEach(btn => {
                    btn.style.display = 'none';
                });
            }
        }

        // Check on load and resize
        window.addEventListener('load', checkScrollButtons);
        window.addEventListener('resize', checkScrollButtons);
    </script>
      <!-- JavaScript for Font Awesome fallback -->
  <script>
    // Check if Font Awesome loaded successfully
    document.addEventListener('DOMContentLoaded', function() {
      // Check if Font Awesome loaded by testing if an icon element has the proper styling
      const testIcon = document.createElement('i');
      testIcon.className = 'fa fa-check';
      document.body.appendChild(testIcon);
      
      // Get computed style
      const style = window.getComputedStyle(testIcon);
      
      // If Font Awesome didn't load properly, use local fallback
      if (style.fontFamily !== 'Font Awesome 6 Free' && style.fontFamily !== '"Font Awesome 6 Free"') {
        console.log('Font Awesome not loaded, using fallback');
        
        // Create a fallback stylesheet
        const fallbackStyle = document.createElement('style');
        fallbackStyle.textContent = `
          .fa::before {
            content: "✓";
            font-weight: bold;
          }
          .fa-envelope::before { content: "✉"; }
          .fa-phone::before { content: "📞"; }
          .fa-map-marker-alt::before { content: "📍"; }
          .fa-globe::before { content: "🌐"; }
          .fa-bars::before { content: "☰"; }
          .fa-search::before { content: "🔍"; }
          .fa-user::before { content: "👤"; }
          .fa-users::before { content: "👥"; }
          .fa-youtube::before { content: "▶"; }
          .fa-facebook-f::before { content: "f"; }
          .fa-twitter::before { content: "t"; }
          .fa-instagram::before { content: "📷"; }
        `;
        document.head.appendChild(fallbackStyle);
      }
      
      // Remove test element
      document.body.removeChild(testIcon);
    });
  </script>

</body>
</html>