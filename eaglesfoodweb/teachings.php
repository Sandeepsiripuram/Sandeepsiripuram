<?php
include "db.php";
session_start();

$admin_url = "https://eaglesfood.info/admin/";

$selected_lang_id = isset($_GET['lang']) ? intval($_GET['lang']) : 1;
$teaching_id = isset($_GET['teaching_id']) ? intval($_GET['teaching_id']) : 1;

// Fetch Languages for Searchable Dropdown
$sql_languages = "SELECT * FROM teaching_languages ORDER BY language_name ASC";
$res_languages = mysqli_query($conn, $sql_languages);
$all_languages = mysqli_fetch_all($res_languages, MYSQLI_ASSOC);

// 1. Fetch Main Teaching Content
$sql_teaching = "SELECT t.*, l.language_name FROM teachings_card t 
                 JOIN teaching_languages l ON t.language_id = l.language_id 
                 WHERE t.teaching_id = ? AND t.language_id = ?";
$stmt = $conn->prepare($sql_teaching);
$stmt->bind_param("ii", $teaching_id, $selected_lang_id);
$stmt->execute();
$teaching = $stmt->get_result()->fetch_assoc();

if (!$teaching) { 
    $stmt = $conn->prepare("SELECT t.*, l.language_name FROM teachings_card t JOIN teaching_languages l ON t.language_id = l.language_id WHERE t.teaching_id = ? LIMIT 1");
    $stmt->bind_param("i", $teaching_id);
    $stmt->execute();
    $teaching = $stmt->get_result()->fetch_assoc();
}

// 2. Update View Count
$conn->query("UPDATE teachings_card SET views = views + 1 WHERE teaching_id = " . intval($teaching_id));

// 3. Fetch Sections
$sql_sections = "SELECT * FROM teaching_content_list WHERE teaching_id = ? AND language_id = ? ORDER BY teaching_content_id ASC";
$stmt = $conn->prepare($sql_sections);
$stmt->bind_param("ii", $teaching_id, $selected_lang_id);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 4. Sidebar Content
$sql_side_heading = "SELECT * FROM teaching_sidepanel_heading WHERE teaching_id = ? AND language_id = ?";
$stmt = $conn->prepare($sql_side_heading);
$stmt->bind_param("ii", $teaching_id, $selected_lang_id);
$stmt->execute();
$side_heading = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($teaching['title']) ?> - Eagles Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a; --secondary: #3b82f6; --accent: #f59e0b;
            --bg: #f8fafc; --white: #ffffff; --text: #334155; --border: #e2e8f0;
        }

        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.8; scroll-behavior: smooth; }
        
        /* Navbar */
        .site-header { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 0.75rem 5%; display: flex; justify-content: space-between; align-items: center; 
            border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 1000;
        }
        .logo { font-weight: 800; color: var(--primary); text-decoration: none; font-size: 1.25rem; letter-spacing: -0.5px; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--text); font-weight: 500; font-size: 0.9rem; transition: 0.2s; display: flex; align-items: center; gap: 6px; }
        .nav-links a:hover { color: var(--secondary); }

        /* Language Selector */
        .lang-dropdown { position: relative; }
        .lang-trigger { background: #f1f5f9; border: 1px solid var(--border); padding: 7px 14px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .lang-menu { display: none; position: absolute; right: 0; top: 110%; background: var(--white); min-width: 240px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 10px; border: 1px solid var(--border); overflow: hidden; }
        .lang-menu.active { display: block; }
        .lang-search-wrapper { padding: 12px; border-bottom: 1px solid var(--border); }
        .lang-search { width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; box-sizing: border-box; outline: none; }
        .lang-list { max-height: 200px; overflow-y: auto; }
        .lang-item { padding: 10px 15px; display: block; text-decoration: none; color: var(--text); font-size: 0.9rem; transition: 0.2s; }
        .lang-item:hover { background: #eff6ff; color: var(--secondary); }

        /* Reading Progress */
        .progress-bar { position: fixed; top: 0; left: 0; height: 3px; background: var(--secondary); width: 0%; z-index: 2001; }

        /* Layout Container */
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 340px; gap: 40px; }

        /* Main Article */
        .main-article { background: var(--white); padding: 50px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); border: 1px solid var(--border); }
        
        /* Back Button */
        .btn-back { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #64748b; font-size: 0.9rem; font-weight: 600; margin-bottom: 2rem; transition: 0.2s; }
        .btn-back:hover { color: var(--secondary); transform: translateX(-4px); }

        /* Article Heading Section */
        .article-header { text-align: center; margin-bottom: 3.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 2rem; }
        .article-title { font-size: 2.5rem; font-weight: 800; color: var(--primary); margin: 0 0 15px 0; line-height: 1.25; }
        .article-meta { display: flex; justify-content: center; align-items: center; gap: 24px; font-size: 0.85rem; color: #94a3b8; font-weight: 500; }
        .meta-item { display: flex; align-items: center; gap: 6px; }

        /* Content Body */
        .article-body { font-family: 'Lora', serif; font-size: 1.15rem; color: #1e293b; }
        .content-section { margin-bottom: 4.5rem; }
        .content-section h2 { font-family: 'Inter', sans-serif; font-size: 1.65rem; color: var(--primary); border-left: 4px solid var(--secondary); padding-left: 1rem; margin: 0 0 1.5rem 0; font-weight: 700; }
        
        /* Image Alignment */
        .content-image-wrapper { margin: 2.5rem 0; text-align: center; background: #f8fafc; padding: 10px; border-radius: 16px; border: 1px solid #f1f5f9; }
        .content-image-wrapper img { max-width: 100%; height: auto; border-radius: 12px; display: block; margin: 0 auto; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        /* Table of Contents */
        .toc { background: #f8fafc; border: 1px solid var(--border); padding: 25px; border-radius: 16px; margin-bottom: 3rem; }
        .toc h3 { margin: 0 0 15px 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; font-family: 'Inter', sans-serif; }
        .toc ul { list-style: none; padding: 0; margin: 0; }
        .toc li { margin-bottom: 12px; }
        .toc a { text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; font-family: 'Inter', sans-serif; font-size: 0.95rem; }
        .toc a:hover { color: var(--secondary); padding-left: 5px; }

        /* Sidebar */
        .sidebar { position: sticky; top: 100px; height: fit-content; }
        .sidebar-card { background: var(--white); border-radius: 20px; overflow: hidden; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        .sidebar-card h2 { background: #f8fafc; border-bottom: 1px solid var(--border); color: var(--primary); margin: 0; padding: 20px; font-size: 1.1rem; font-weight: 700; text-align: center; }
        .infobox td { padding: 15px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
        .label { font-weight: 700; color: #94a3b8; width: 30%; }

        /* Responsiveness */
        @media (max-width: 1100px) {
            .container { grid-template-columns: 1fr; gap: 30px; }
            .sidebar { position: relative; top: 0; width: 100%; max-width: 500px; margin: 0 auto; }
            .main-article { padding: 35px 25px; }
        }

        @media (max-width: 768px) {
            .site-header { padding: 0.75rem 1rem; }
            .nav-links span { display: none; }
            .article-title { font-size: 1.85rem; }
            .article-meta { flex-direction: column; gap: 8px; }
            .lang-menu { position: fixed; top: 60px; left: 10px; right: 10px; min-width: auto; }
        }
    </style>
</head>
<body>
    <div class="progress-bar" id="readingProgress"></div>
    
    <header class="site-header">
        <a href="index.php" class="logo">EAGLES FOOD</a>
        <nav class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a>
            <a href="seals.php"><i class="fas fa-scroll"></i> <span>Seals</span></a>
            <a href="church_ages.php"><i class="fas fa-church"></i> <span>Ages</span></a>
            <a href="detail_message_page.php"><i class="fas fa-envelope"></i> <span>Messages</span></a>
            
            <div class="lang-dropdown">
                <div class="lang-trigger" id="langTrigger">
                    <i class="fas fa-globe"></i> <span><?= htmlspecialchars($teaching['language_name']) ?></span>
                </div>
                <div class="lang-menu" id="langMenu">
                    <div class="lang-search-wrapper">
                        <input type="text" class="lang-search" id="langSearch" placeholder="Search language...">
                    </div>
                    <div class="lang-list">
                        <?php foreach($all_languages as $lang): ?>
                            <a href="teachings.php?teaching_id=<?= $teaching_id ?>&lang=<?= $lang['language_id'] ?>" class="lang-item" data-name="<?= strtolower($lang['language_name']) ?>">
                                <?= htmlspecialchars($lang['language_name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <article class="main-article">
            <a href="teachings_list.php?lang=<?= $selected_lang_id ?>" class="btn-back">
                <i class="fas fa-long-arrow-alt-left"></i> Back to Teachings
            </a>

            <div class="article-header">
                <h1 class="article-title"><?= htmlspecialchars($teaching['title']) ?></h1>
                <div class="article-meta">
                    <div class="meta-item"><i class="far fa-calendar-alt"></i> Updated <?= date('M d, Y', strtotime($teaching['last_updated'])) ?></div>
                    <div class="meta-item"><i class="far fa-eye"></i> <?= number_format($teaching['views']) ?> Views</div>
                    <div class="meta-item"><i class="fas fa-language"></i> <?= htmlspecialchars($teaching['language_name']) ?></div>
                </div>
            </div>

            <?php if(!empty($sections)): ?>
            <nav class="toc">
                <h3>Contents</h3>
                <ul>
                    <?php foreach($sections as $s): ?>
                        <li><a href="#sec-<?= $s['teaching_content_id'] ?>"><i class="fas fa-chevron-right" style="font-size: 0.7rem; opacity: 0.5; margin-right: 8px;"></i> <?= htmlspecialchars($s['teaching_title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="article-body">
                <?php foreach($sections as $s): 
                    $stmt = $conn->prepare("SELECT * FROM teaching_content_description WHERE teaching_content_id = ?");
                    $stmt->bind_param("i", $s['teaching_content_id']);
                    $stmt->execute();
                    $det = $stmt->get_result()->fetch_assoc();
                ?>
                <section class="content-section" id="sec-<?= $s['teaching_content_id'] ?>">
                    <h2><?= htmlspecialchars($s['teaching_title']) ?></h2>
                    <?php if($det): ?>
                        <p><?= nl2br(htmlspecialchars($det['Description_1'])) ?></p>
                        
                        <?php if(!empty($det['image1'])): ?>
                            <div class="content-image-wrapper">
                                <img src="<?= $admin_url . htmlspecialchars($det['image1']) ?>" alt="<?= htmlspecialchars($s['teaching_title']) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <p><?= nl2br(htmlspecialchars($det['description_2'])) ?></p>
                    <?php endif; ?>
                </section>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </article>

        <aside class="sidebar">
            <?php if($side_heading): ?>
            <div class="sidebar-card">
                <h2>Quick Summary</h2>
                <?php if(!empty($side_heading['image'])): ?>
                    <img src="<?= $admin_url . htmlspecialchars($side_heading['image']) ?>" style="width: 100%; display: block;">
                <?php endif; ?>
                <table class="infobox" style="width: 100%; border-collapse: collapse;">
                    <tr><td class="label">Topic</td><td><?= htmlspecialchars($side_heading['title']) ?></td></tr>
                    <tr><td class="label">Category</td><td>Bible Teachings</td></tr>
                </table>
            </div>
            <?php endif; ?>
        </aside>
    </div>

    <script>
        // Reading Progress Logic
        window.onscroll = function() {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            document.getElementById("readingProgress").style.width = scrolled + "%";
        };

        // Searchable Language Dropdown Logic
        const trigger = document.getElementById('langTrigger');
        const menu = document.getElementById('langMenu');
        const searchInput = document.getElementById('langSearch');
        const langItems = document.querySelectorAll('.lang-item');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('active');
            if(menu.classList.contains('active')) searchInput.focus();
        });

        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            langItems.forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(term) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', () => menu.classList.remove('active'));
    </script>
</body>
</html>
