<?php
include "db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: church_ages.php");
    exit();
}

$stmt = $conn->prepare("SELECT title, date, content, language_id FROM church_ages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "Sermon not found.";
    exit();
}

$sermon = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sermon['title']) ?> - Eagles Food</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-color: #f4f7f9;
            --paper-color: #ffffff;
            --text-color: #2d3436;
            --accent-color: #1976d2;
            --meta-color: #636e72;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --paper-color: #2d2d2d;
            --text-color: #e0e0e0;
            --accent-color: #64b5f6;
            --meta-color: #b0b0b0;
        }

        body { 
            background-color: var(--bg-color); 
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            margin: 0;
            padding: 0;
        }

        /* Reading Progress Bar */
        .progress-container {
            position: fixed;
            top: 0;
            z-index: 1001;
            width: 100%;
            height: 4px;
            background: transparent;
        }
        .progress-bar {
            height: 4px;
            background: var(--accent-color);
            width: 0%;
        }

        /* Floating Controls */
        .reading-tools {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .tool-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent-color);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .tool-btn:hover { transform: scale(1.1); color: white; }

        /* Main Container */
        .sermon-wrapper {
            max-width: 850px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .sermon-paper {
            background: var(--paper-color);
            padding: 60px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            min-height: 100vh;
        }

        .meta-header {
            border-bottom: 2px solid rgba(0,0,0,0.05);
            margin-bottom: 40px;
            padding-bottom: 20px;
        }

        h1 {
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .date-badge {
            color: var(--meta-color);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sermon-content {
            font-family: 'Merriweather', serif;
            font-size: 1.2rem;
            line-height: 1.9;
            white-space: pre-line;
            word-wrap: break-word;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sermon-paper { padding: 30px 20px; border-radius: 0; }
            .sermon-wrapper { margin: 0; padding: 0; }
            h1 { font-size: 1.8rem; }
            .reading-tools { bottom: 20px; right: 20px; }
            .tool-btn { width: 45px; height: 45px; }
        }
    </style>
</head>
<body id="readingBody">

    <div class="progress-container">
        <div class="progress-bar" id="myBar"></div>
    </div>

    <div class="reading-tools">
        <button class="tool-btn" onclick="toggleTheme()" title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
        <button class="tool-btn" onclick="changeFontSize(1)" title="Increase Text">
            <i class="fas fa-plus"></i>
        </button>
        <button class="tool-btn" onclick="changeFontSize(-1)" title="Decrease Text">
            <i class="fas fa-minus"></i>
        </button>
        <button class="tool-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Scroll to Top">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>

    <div class="sermon-wrapper">
        <div class="mb-4 d-flex justify-content-between align-items-center px-3">
            <a href="church_ages.php?lang=<?= $sermon['language_id'] ?>" class="text-decoration-none text-muted">
                <i class="fas fa-chevron-left me-1"></i> Library
            </a>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>

        <article class="sermon-paper">
            <header class="meta-header">
                <h1><?= htmlspecialchars($sermon['title']) ?></h1>
                <div class="date-badge">
                    <i class="far fa-calendar-alt"></i>
                    <span>Preached on <?= date('F d, Y', strtotime($sermon['date'])) ?></span>
                </div>
            </header>

            <div class="sermon-content" id="sermonContent">
                <?= $sermon['content'] ?>
            </div>
            
            <footer class="mt-5 pt-5 text-center text-muted border-top">
                <p class="small">End of Sermon: <?= htmlspecialchars($sermon['title']) ?></p>
            </footer>
        </article>
    </div>

    <script>
        // Progress Bar Logic
        window.onscroll = function() { updateProgress() };
        function updateProgress() {
            var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            var scrolled = (winScroll / height) * 100;
            document.getElementById("myBar").style.width = scrolled + "%";
        }

        // Font Size Logic
        let currentSize = 1.2;
        function changeFontSize(delta) {
            currentSize += delta * 0.1;
            if(currentSize < 0.8) currentSize = 0.8;
            if(currentSize > 2.0) currentSize = 2.0;
            document.getElementById('sermonContent').style.fontSize = currentSize + 'rem';
        }

        // Theme Toggle
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('themeIcon');
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                icon.className = 'fas fa-moon';
            } else {
                body.setAttribute('data-theme', 'dark');
                icon.className = 'fas fa-sun';
            }
        }
    </script>
</body>
</html>
