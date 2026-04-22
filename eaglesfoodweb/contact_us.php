<?php
session_start();
include 'db.php';

// Base URL for your admin panel assets
$admin_url = "https://eaglesfood.info/admin/";

// Set default language
if (!isset($_SESSION['language_id'])) {
    $_SESSION['language_id'] = 1;
}

// Handle language selection
if (isset($_GET['language_id'])) {
    $_SESSION['language_id'] = intval($_GET['language_id']);
    header("Location: contact_us.php");
    exit();
}

$current_language_id = $_SESSION['language_id'];

// Fetch language list
$languages = [];
$language_query = mysqli_query($conn, "SELECT language_id, language_name FROM about_ministry_languages");
while ($lang = mysqli_fetch_assoc($language_query)) {
    $languages[$lang['language_id']] = $lang['language_name'];
}

// Fetch ministry information
$current_id = intval($current_language_id);
$about_query = mysqli_query($conn, "SELECT heading, about_text, image FROM about_ministry WHERE language_id = $current_id LIMIT 1");
$about_data = mysqli_fetch_assoc($about_query);

// Fetch leadership
$leaders = [];
$leaders_query = mysqli_query($conn, "SELECT * FROM fivefold_ministry_leaders WHERE language_id = $current_id");
while ($leader = mysqli_fetch_assoc($leaders_query)) {
    $leaders[] = $leader;
}

// Fetch social media
$social_query = mysqli_query($conn, "SELECT * FROM church_socialmedia LIMIT 1");
$social_data = mysqli_fetch_assoc($social_query);

// Fetch videos
$videos = [];
$videos_query = mysqli_query($conn, "SELECT * FROM jesuschristtruebride_videos ORDER BY id DESC");
while ($video = mysqli_fetch_assoc($videos_query)) {    
    $videos[] = $video;
}

/**
 * Robust YouTube ID Parser
 */
function getYoutubeID($url) {  
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/\s]{11})/i';
    if (preg_match($pattern, $url, $matches)) {        
        return $matches[1];  
    }    
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Eagles Food Ministry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --dark: #0f172a;
            --text-main: #334155;
            --text-light: #64748b;
            --bg-body: #f8fafc;
            --white: #ffffff;
            --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 10px -2px rgba(0, 0, 0, 0.03);
            --radius: 12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-body); color: var(--text-main); line-height: 1.6; overflow-x: hidden; }

        /* --- Header --- */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.8rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 1000;
        }

        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-img { height: 40px; width: 40px; border-radius: 10px; object-fit: cover; }
        .logo-text { font-size: 1.2rem; font-weight: 700; color: var(--dark); letter-spacing: -0.5px; }

        .menu { display: flex; align-items: center; gap: 8px; }
        .menu a { text-decoration: none; color: var(--text-main); padding: 8px 12px; font-size: 0.9rem; font-weight: 500; border-radius: 8px; transition: 0.2s; }
        .menu a:hover, .menu a.active { background: var(--primary); color: white; }

        /* Language Dropdown */
        .language-dropdown { position: relative; }
        .lang-btn { background: #f1f5f9; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-family: inherit; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }
        .lang-content { display: none; position: absolute; right: 0; top: 120%; background: white; min-width: 200px; box-shadow: var(--card-shadow); border-radius: 10px; border: 1px solid #eee; overflow: hidden; z-index: 1001; }
        .lang-content.show { display: block; }
        .lang-item { padding: 10px 15px; cursor: pointer; display: flex; justify-content: space-between; font-size: 0.9rem; }
        .lang-item:hover { background: var(--primary); color: white; }

        /* --- Content Layout --- */
        .page-header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 60px 5% 100px; text-align: center; }
        main { max-width: 1200px; margin: -50px auto 60px; padding: 0 15px; }
        
        .card { background: var(--white); padding: 40px; border-radius: var(--radius); box-shadow: var(--card-shadow); margin-bottom: 30px; border: 1px solid rgba(0,0,0,0.03); }
        .section-tag { color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; display: block; margin-bottom: 10px; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin-bottom: 25px; color: var(--dark); }

        .about-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; }
        .ministry-img { width: 100%; border-radius: var(--radius); height: auto; max-height: 400px; object-fit: cover; box-shadow: 15px 15px 0 #f1f5f9; }

        /* Video Scroller (Horizontal on Desktop, Grid on Mobile) */
        .video-container { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 15px; scroll-snap-type: x mandatory; }
        .video-card { min-width: 320px; background: var(--white); border-radius: var(--radius); overflow: hidden; border: 1px solid #f1f5f9; scroll-snap-align: start; }
        .video-wrap { position: relative; padding-bottom: 56.25%; height: 0; background: #000; }
        .video-wrap iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        .video-title { padding: 15px; font-size: 0.9rem; font-weight: 600; height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }

        /* Leaders */
        .leader-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 50px; }
        .leader-card { background: var(--white); border-radius: var(--radius); overflow: hidden; border: 1px solid #f1f5f9; box-shadow: var(--card-shadow); text-align: center; }
        .leader-photo { height: 280px; width: 100%; object-fit: cover; }

        /* Contact Details & Form */
        .contact-container { display: grid; grid-template-columns: 1fr 1.2fr; gap: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: span 2; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; font-family: inherit; font-size: 0.95rem; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-submit { background: var(--primary); color: white; padding: 14px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.2s; }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-2px); }

        .menu-toggle { display: none; font-size: 1.5rem; background: none; border: none; cursor: pointer; }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .about-grid, .contact-container { grid-template-columns: 1fr; }
            .ministry-img { box-shadow: none; }
        }

        @media (max-width: 768px) {
            .menu { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; flex-direction: column; padding: 20px; box-shadow: var(--shadow); }
            .menu.active { display: flex; }
            .menu-toggle { display: block; }
            .card { padding: 25px 20px; }
            .video-container { flex-direction: column; overflow-x: visible; }
            .video-card { min-width: 100%; }
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
        }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="logo">
        <img src="images/appicon.png" alt="Logo" class="logo-img">
        <span class="logo-text">Eagles Food</span>
    </a>
    <button class="menu-toggle" id="menuBtn"><i class="fas fa-bars"></i></button>
    <div class="menu" id="navMenu">
        <a href="index.php">Home</a>
        <div class="language-dropdown">
            <button class="lang-btn" id="langBtn">
                <i class="fas fa-globe"></i> <?= $languages[$current_language_id] ?? 'English' ?> <i class="fas fa-caret-down"></i>
            </button>
            <div class="lang-content" id="langContent">
                <div style="padding: 10px;"><input type="text" id="langSearch" placeholder="Search language..." class="form-control" style="padding: 5px;"></div>
                <?php foreach ($languages as $id => $name): ?>
                    <div class="lang-item" onclick="changeLang(<?= $id ?>)">
                        <span><?= htmlspecialchars($name) ?></span>
                        <small style="color: var(--text-light)">#<?= $id ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="teachings_list.php">Teachings</a>
        <a href="contact_us.php" class="active">Contact</a>
    </div>
</header>

<section class="page-header">
    <h1>Contact Our Ministry</h1>
    <p>We are here to serve and support you in your spiritual walk.</p>
</section>

<main>
    <section class="card">
        <div class="about-grid">
            <div class="about-content">
                <span class="section-tag">Our Mission</span>
                <h2 class="section-title"><?= htmlspecialchars($about_data['heading'] ?? 'About Ministry') ?></h2>
                <div style="white-space: pre-wrap; font-size: 0.95rem;">
                    <?= htmlspecialchars($about_data['about_text'] ?? 'Welcome to Lord Jesus Christ Praying Tabernacle.') ?>
                </div>
            </div>
            <div class="about-image">
                <?php $about_img = (!empty($about_data['image'])) ? $admin_url . $about_data['image'] : 'images/church_default.jpg'; ?>
                <img src="<?= $about_img ?>" class="ministry-img" alt="Ministry" onerror="this.src='images/church_default.jpg'">
            </div>
        </div>
    </section>

    <?php if(!empty($leaders)): ?>
    <section>
        <span class="section-tag" style="text-align:center;">Leadership</span>
        <h2 class="section-title" style="text-align:center;">Fivefold Ministry Leaders</h2>
        <div class="leader-grid">
            <?php foreach ($leaders as $leader): 
                $leader_img = (!empty($leader['image'])) ? $admin_url . $leader['image'] : 'images/user.png';
            ?>
            <div class="leader-card">
                <img src="<?= $leader_img ?>" class="leader-photo" alt="<?= htmlspecialchars($leader['name']) ?>" onerror="this.src='images/user.png'">
                <div style="padding: 15px;">
                    <span style="color: var(--primary); font-size: 0.75rem; font-weight: 700; text-transform: uppercase;"><?= htmlspecialchars($leader['role']) ?></span>
                    <h3 style="font-size: 1.1rem; color: var(--dark);"><?= htmlspecialchars($leader['name']) ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if(!empty($videos)): ?> 
    <section class="card" style="padding: 30px;">     
        <span class="section-tag">Multimedia</span>
        <h2 class="section-title"><i class="fab fa-youtube" style="color: #ff0000; margin-right: 10px;"></i>Latest Teachings</h2>        
        <div class="video-container">          
            <?php foreach ($videos as $video): 
                $youtubeID = getYoutubeID($video['url']); 
                if(!empty($youtubeID)): 
            ?>    
                <div class="video-card">
                    <div class="video-wrap">
                        <iframe src="https://www.youtube.com/embed/<?= $youtubeID ?>" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">
                        <?= htmlspecialchars($video['title']) ?>
                    </div>
                </div>
            <?php endif; endforeach; ?>
        </div> 
    </section>    
    <?php endif; ?>

    <div class="contact-container">
        <section class="card">
            <span class="section-tag">Reach Us</span>
            <h2 class="section-title">Contact Info</h2>
            <div style="margin-bottom: 25px;">
                <p style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-phone-alt" style="color: var(--primary);"></i> 
                    <?= htmlspecialchars($social_data['phone_number'] ?? 'N/A') ?>
                </p>
                <p style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-envelope" style="color: var(--primary);"></i> 
                    <?= htmlspecialchars($social_data['email'] ?? 'N/A') ?>
                </p>
                <p style="margin-bottom: 12px; display: flex; align-items: start; gap: 10px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary); margin-top: 5px;"></i> 
                    <span><?= htmlspecialchars($social_data['address'] ?? 'N/A') ?></span>
                </p>
            </div>
            <div style="height: 250px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0;">
                 <iframe width="100%" height="100%" frameborder="0" src="https://maps.google.com/maps?q=<?= urlencode($social_data['address'] ?? 'Church') ?>&output=embed"></iframe>
            </div>
        </section>

       <section class="card">
    <span class="section-tag">Message</span>
    <h2 class="section-title">Send Us a Note</h2>
    <form action="contact_request_insert.php" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>
            <div class="form-group full-width">
                <input type="tel" name="phone_number" class="form-control" placeholder="Phone Number (Optional)">
            </div>
            <div class="form-group full-width">
                <input type="text" name="subject" class="form-control" placeholder="Subject" required>
            </div>
            <div class="form-group full-width">
                <textarea name="message" rows="5" class="form-control" placeholder="How can we help you?" required></textarea>
            </div>
            <div class="full-width">
                <button type="submit" name="submit" class="btn-submit">
                    <span>Send Message</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </form>
</section>
    </div>
</main>

<script>
    // Navigation Toggle
    const menuBtn = document.getElementById('menuBtn');
    const navMenu = document.getElementById('navMenu');
    menuBtn.onclick = () => navMenu.classList.toggle('active');

    // Language Dropdown
    const langBtn = document.getElementById('langBtn');
    const langContent = document.getElementById('langContent');
    langBtn.onclick = (e) => {
        e.stopPropagation();
        langContent.classList.toggle('show');
    };
    window.onclick = () => langContent.classList.remove('show');

    // Language Search
    document.getElementById('langSearch').onkeyup = function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.lang-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(val) ? 'flex' : 'none';
        });
    };

    function changeLang(id) {
        window.location.href = `contact_us.php?language_id=${id}`;
    }
</script>

</body>
</html>
