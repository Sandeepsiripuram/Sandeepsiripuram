<?php
include "db.php";
if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }

$admin_url = "https://eaglesfood.info/admin/";
$selected_lang_id = isset($_GET['lang']) ? intval($_GET['lang']) : 1;

// Fetch Languages
$sql_languages = "SELECT `language_id`, `language_name` FROM `teaching_languages` ORDER BY language_name ASC";
$res_languages = mysqli_query($conn, $sql_languages);
$languages = mysqli_fetch_all($res_languages, MYSQLI_ASSOC);

// Pagination
$per_page = 8; // Increased because cards are smaller
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Fetch Teachings
$sql_teachings = "SELECT * FROM `teachings_card` WHERE `language_id` = $selected_lang_id LIMIT $offset, $per_page";
$res_teachings = mysqli_query($conn, $sql_teachings);

$sql_count = "SELECT COUNT(*) as total FROM `teachings_card` WHERE `language_id` = $selected_lang_id";
$count_res = mysqli_query($conn, $sql_count);
$total_teachings = mysqli_fetch_assoc($count_res)['total'];
$total_pages = ceil($total_teachings / $per_page);

// Get current language name for the trigger display
$current_lang_name = "Select Language";
foreach($languages as $l) {
    if($l['language_id'] == $selected_lang_id) {
        $current_lang_name = $l['language_name'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachings - Eagles Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e293b;
            --secondary: #3b82f6;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --white: #ffffff;
            --text-main: #475569;
            --text-heading: #0f172a;
            --border: #e2e8f0;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
        }

        /* --- Header & Navigation --- */
        header {
            background: var(--white);
            padding: 0.75rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .logo-group { display: flex; align-items: center; text-decoration: none; }
        .logo-text { font-size: 1.2rem; font-weight: 800; color: var(--primary); letter-spacing: -0.5px; }

        .nav-links { display: flex; align-items: center; gap: 1.5rem; }
        .nav-links a { 
            text-decoration: none; 
            color: var(--text-main); 
            font-weight: 500; 
            font-size: 0.85rem; 
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-links a:hover { color: var(--secondary); }

        /* --- Searchable Language Dropdown --- */
        .lang-dropdown { position: relative; display: inline-block; }
        .lang-trigger {
            background: #f1f5f9;
            border: 1px solid var(--border);
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--primary);
        }
        .lang-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 115%;
            background: var(--white);
            min-width: 250px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border-radius: 10px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .lang-menu.active { display: block; animation: slideIn 0.2s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        .lang-search-wrapper { padding: 12px; background: #f8fafc; border-bottom: 1px solid var(--border); }
        .lang-search {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.85rem;
            box-sizing: border-box;
            outline: none;
        }
        .lang-search:focus { border-color: var(--secondary); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }
        .lang-list { max-height: 250px; overflow-y: auto; }
        .lang-item {
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            color: var(--text-main);
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .lang-item:hover { background: #eff6ff; color: var(--secondary); }
        .lang-item.selected { background: #eff6ff; color: var(--secondary); font-weight: 600; }

        /* --- Main Content --- */
        .container { max-width: 1200px; margin: 3rem auto; padding: 0 20px; }
        .section-header { text-align: center; margin-bottom: 3rem; }
        .section-header h1 { font-size: 2.25rem; font-weight: 800; color: var(--text-heading); margin: 0; }
        .section-header p { color: #64748b; margin-top: 0.5rem; }

        /* --- Grid & Cards --- */
        .teaching-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 20px; 
        }

        .card { 
            background: var(--white); 
            border-radius: 12px; 
            overflow: hidden; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            border: 1px solid var(--border);
            display: flex; 
            flex-direction: column; 
            text-decoration: none; 
            color: inherit;
            height: 100%;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.1); border-color: var(--secondary); }
        
        .card-img-wrapper { position: relative; width: 100%; height: 160px; overflow: hidden; background: #e2e8f0; }
        .card-img { width: 100%; height: 100%; object-fit: cover; }

        .card-body { padding: 16px; flex-grow: 1; }
        .card-body h3 { 
            margin: 0 0 8px; 
            color: var(--text-heading); 
            font-size: 1.05rem; 
            line-height: 1.4; 
            font-weight: 700;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .card-body p { 
            color: #64748b; font-size: 0.85rem; line-height: 1.5; margin: 0;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        
        .card-footer { 
            padding: 12px 16px; background: #fcfcfc; border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; font-size: 0.75rem; color: #94a3b8; font-weight: 500;
        }

        /* --- Pagination --- */
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 50px; }
        .page-link { 
            padding: 8px 16px; background: var(--white); border: 1px solid var(--border); 
            text-decoration: none; color: var(--primary); border-radius: 8px; 
            font-size: 0.9rem; font-weight: 600; transition: all 0.2s;
        }
        .page-link:hover { border-color: var(--secondary); color: var(--secondary); }
        .page-link.active { background: var(--secondary); color: var(--white); border-color: var(--secondary); }

        /* --- Responsive Adjustments --- */
        @media (max-width: 1024px) {
            .nav-links span { display: none; }
            .nav-links a i { font-size: 1.2rem; }
        }

        @media (max-width: 768px) {
            header { padding: 0.75rem 1.5rem; }
            .nav-links { gap: 1rem; }
            .section-header h1 { font-size: 1.75rem; }
            .lang-menu { position: fixed; top: 60px; left: 10px; right: 10px; min-width: auto; }
        }

        @media (max-width: 480px) {
            .teaching-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo-group">
            <span class="logo-text">EAGLES FOOD</span>
        </a>

        <nav class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a>
            <a href="seals.php"><i class="fas fa-scroll"></i> <span>Seals</span></a>
            <a href="church_ages.php"><i class="fas fa-church"></i> <span>Church Ages</span></a>
            <a href="detail_message_page.php"><i class="fas fa-envelope"></i> <span>Messages</span></a>
            
            <div class="lang-dropdown">
                <div class="lang-trigger" id="langTrigger">
                    <i class="fas fa-globe"></i> <span><?= htmlspecialchars($current_lang_name) ?></span> <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </div>
                <div class="lang-menu" id="langMenu">
                    <div class="lang-search-wrapper">
                        <input type="text" class="lang-search" id="langSearch" placeholder="Search language..." autocomplete="off">
                    </div>
                    <div class="lang-list" id="langList">
                        <?php foreach($languages as $l): ?>
                            <a href="?lang=<?= $l['language_id'] ?>" class="lang-item <?= $selected_lang_id == $l['language_id'] ? 'selected' : '' ?>" data-name="<?= strtolower($l['language_name']) ?>">
                                <?= htmlspecialchars($l['language_name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="section-header">
            <h1>Teachings</h1>
            <p>Explore inspired messages and spiritual insights.</p>
        </div>

        <div class="teaching-grid">
            <?php if (mysqli_num_rows($res_teachings) > 0): ?>
                <?php while($t = mysqli_fetch_assoc($res_teachings)): ?>
                    <a href="teachings.php?teaching_id=<?= $t['teaching_id'] ?>&lang=<?= $selected_lang_id ?>" class="card">
                        <div class="card-img-wrapper">
                            <img src="<?= $admin_url . htmlspecialchars($t['image']) ?>" class="card-img" alt="Teaching" onerror="this.src='https://via.placeholder.com/300x160?text=Eagles+Food'">
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($t['title']) ?></h3>
                            <p><?= htmlspecialchars($t['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <span><i class="far fa-eye"></i> <?= number_format($t['views']) ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?= date('M Y', strtotime($t['last_updated'])) ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px 20px; color: #94a3b8; background: #fff; border-radius: 12px; border: 1px dashed var(--border);">
                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No teachings found in this language.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?lang=<?= $selected_lang_id ?>&page=<?= $page - 1 ?>" class="page-link"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?lang=<?= $selected_lang_id ?>&page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="?lang=<?= $selected_lang_id ?>&page=<?= $page + 1 ?>" class="page-link"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Language Dropdown Logic
        const trigger = document.getElementById('langTrigger');
        const menu = document.getElementById('langMenu');
        const searchInput = document.getElementById('langSearch');
        const langItems = document.querySelectorAll('.lang-item');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('active');
            if (menu.classList.contains('active')) {
                searchInput.focus();
            }
        });

        // Search Filter
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            langItems.forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(term) ? 'block' : 'none';
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && e.target !== trigger) {
                menu.classList.remove('active');
            }
        });

        // Stop propagation inside menu search
        searchInput.addEventListener('click', (e) => e.stopPropagation());
    </script>
</body>
</html>
