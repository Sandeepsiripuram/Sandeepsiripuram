<?php
include "db.php";
$sermon_id = isset($_GET['sermon_id']) ? intval($_GET['sermon_id']) : 0;
$sql = "SELECT * FROM sermons_content WHERE sermon_id = $sermon_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: index.php");
    exit();
}

$sermon = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sermon['sermon_title']) ?> - Eagles Food</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #0288d1;
            --bg: #f8f9fa;
            --card: #ffffff;
            --text: #2d3436;
            --header-text: #ffffff;
        }

        /* Reading Themes */
        body.dark-mode {
            --bg: #121212;
            --card: #1e1e1e;
            --text: #e0e0e0;
        }
        body.sepia-mode {
            --bg: #f4ecd8;
            --card: #fcf5e5;
            --text: #5b4636;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Noto Sans Telugu", sans-serif;
            background-color: var(--bg);
            color: var(--text);
            transition: background 0.3s, color 0.3s;
            line-height: 1.8;
        }

        /* Header UI */
        .top-nav {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .back-btn { color: white; text-decoration: none; font-size: 1.2rem; }

        .sermon-meta {
            background: var(--card);
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .sermon-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            margin: 0 0 10px 0;
            color: var(--primary);
        }

        .location-tag {
            font-size: 0.9rem;
            opacity: 0.8;
            font-style: italic;
        }

        /* Reading Container */
        .content-container {
            max-width: 850px; /* Optimal reading width */
            margin: 0 auto 50px auto;
            padding: 30px;
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            min-height: 80vh;
        }

        /* Controls Bar */
        .reading-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 15px;
            position: sticky;
            top: 55px;
            background: var(--bg);
            z-index: 999;
        }

        .control-btn {
            background: var(--card);
            border: 1px solid #ddd;
            color: var(--text);
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .control-btn:hover { border-color: var(--primary); }

        .paragraph {
            margin-bottom: 25px;
            white-space: pre-line;
            font-size: 20px; /* Default */
        }

        /* Mobile Tweaks */
        @media (max-width: 768px) {
            .content-container {
                margin: 0;
                padding: 15px;
                border-radius: 0;
            }
            .sermon-title { font-size: 1.2rem; }
            .reading-controls { top: 50px; padding: 10px; gap: 8px; }
            .control-btn span { display: none; } /* Icon only on mobile */
        }
    </style>
</head>

<body>
    <header class="top-nav">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div style="font-weight: 600;">Eagles Food</div>
        <div style="width: 20px;"></div> </header>

    <div class="sermon-meta">
        <h1 class="sermon-title"><?= htmlspecialchars($sermon['sermon_title']) ?></h1>
        <div class="location-tag">
            <i class="fas fa-calendar-alt"></i> 
            <?= htmlspecialchars($sermon['date'] . '-' . $sermon['month'] . '-' . $sermon['year']) ?> 
            &nbsp; | &nbsp; 
            <i class="fas fa-map-marker-alt"></i> 
            <?= htmlspecialchars($sermon['city'] . ', ' . $sermon['state']) ?>
        </div>
    </div>

    <div class="reading-controls">
        <button class="control-btn" onclick="changeFontSize(2)"><i class="fas fa-plus"></i><span> Zoom</span></button>
        <button class="control-btn" onclick="changeFontSize(-2)"><i class="fas fa-minus"></i><span> Shrink</span></button>
        <button class="control-btn" onclick="toggleTheme('light')"><i class="fas fa-sun"></i></button>
        <button class="control-btn" onclick="toggleTheme('sepia')"><i class="fas fa-coffee"></i></button>
        <button class="control-btn" onclick="toggleTheme('dark')"><i class="fas fa-moon"></i></button>
    </div>

    <main class="content-container" id="readerBody">
        <?php
        // Improved paragraph splitting to handle different line endings
        $content = str_replace(["\r\n", "\r"], "\n", $sermon['content']);
        $paragraphs = explode("\n\n", $content);
        
        foreach ($paragraphs as $index => $para) {
            $trimmed = trim($para);
            if ($trimmed !== '') {
                // Wrap in divs with ID for potential "scroll to paragraph" features
                echo "<div class='paragraph' id='p-$index'>" . htmlspecialchars($trimmed) . "</div>";
            }
        }
        ?>
    </main>

    <script>
        let currentSize = 20;
        const reader = document.getElementById('readerBody');

        function changeFontSize(step) {
            currentSize += step;
            if (currentSize < 14) currentSize = 14;
            if (currentSize > 40) currentSize = 40;
            
            const paras = document.querySelectorAll('.paragraph');
            paras.forEach(p => p.style.fontSize = currentSize + 'px');
            
            // Save preference
            localStorage.setItem('readerFontSize', currentSize);
        }

        function toggleTheme(theme) {
            document.body.classList.remove('dark-mode', 'sepia-mode');
            if (theme === 'dark') document.body.classList.add('dark-mode');
            if (theme === 'sepia') document.body.classList.add('sepia-mode');
            
            localStorage.setItem('readerTheme', theme);
        }

        // Initialize from saved preferences
        window.onload = () => {
            const savedSize = localStorage.getItem('readerFontSize');
            const savedTheme = localStorage.getItem('readerTheme');
            
            if (savedSize) {
                currentSize = parseInt(savedSize);
                const paras = document.querySelectorAll('.paragraph');
                paras.forEach(p => p.style.fontSize = currentSize + 'px');
            }
            if (savedTheme) toggleTheme(savedTheme);
        };
    </script>
</body>
</html>
