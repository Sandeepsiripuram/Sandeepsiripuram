<?php
include "db.php";
$language_id = isset($_GET['language_id']) ? intval($_GET['language_id']) : 0;
$sql = "SELECT * FROM seven_seals WHERE language_id = $language_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: seals.php");
    exit();
}
$sermon = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sermon['title']) ?> - Reader</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #f5f7fa; --card: #ffffff; --text: #2d3436; --primary: #0288d1; }
        body.dark-mode { --bg: #121212; --card: #1e1e1e; --text: #e0e0e0; }
        body.sepia-mode { --bg: #f4ecd8; --card: #fcf5e5; --text: #5b4636; }

        body { margin: 0; font-family: "Noto Sans Telugu", sans-serif; background: var(--bg); color: var(--text); transition: 0.3s; }
        
        .nav-header { 
            background: var(--primary); color: white; padding: 12px 20px; 
            display: flex; align-items: center; position: sticky; top: 0; z-index: 100;
        }
        .back-link { color: white; text-decoration: none; margin-right: 15px; font-size: 1.2rem; }
        
        .reading-area { 
            max-width: 850px; margin: 20px auto; background: var(--card); 
            padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            min-height: 85vh; line-height: 2; font-size: 20px;
        }

        .controls {
            position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%);
            background: var(--primary); padding: 10px 20px; border-radius: 30px;
            display: flex; gap: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 200;
        }
        .btn-tool { background: transparent; border: none; color: white; cursor: pointer; font-size: 1.1rem; }
        
        .paragraph { margin-bottom: 25px; text-align: justify; }

        @media (max-width: 768px) {
            .reading-area { margin: 0; padding: 20px; border-radius: 0; }
            .controls { width: 90%; justify-content: space-around; bottom: 15px; }
        }
    </style>
</head>
<body>

    <div class="nav-header">
        <a href="seals.php?lang=<?= $sermon['language_id'] ?>" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <div class="flex-grow-1">
            <small style="opacity: 0.8;"><?= $sermon['date'] ?></small>
            <div style="font-weight: 600;"><?= htmlspecialchars($sermon['title']) ?></div>
        </div>
    </div>

    <div class="reading-area" id="readerContent">
        <?php
        $paragraphs = explode("\n\n", str_replace(["\r\n", "\r"], "\n", $sermon['content']));
        foreach ($paragraphs as $para) {
            if (trim($para) !== '') echo "<div class='paragraph'>" . htmlspecialchars($para) . "</div>";
        }
        ?>
    </div>

    <div class="controls">
        <button class="btn-tool" onclick="changeFontSize(2)"><i class="fas fa-search-plus"></i></button>
        <button class="btn-tool" onclick="changeFontSize(-2)"><i class="fas fa-search-minus"></i></button>
        <button class="btn-tool" onclick="setTheme('light')"><i class="fas fa-sun"></i></button>
        <button class="btn-tool" onclick="setTheme('sepia')"><i class="fas fa-coffee"></i></button>
        <button class="btn-tool" onclick="setTheme('dark')"><i class="fas fa-moon"></i></button>
    </div>

    <script>
        let fSize = 20;
        function changeFontSize(s) {
            fSize = Math.max(14, Math.min(40, fSize + s));
            document.getElementById('readerContent').style.fontSize = fSize + 'px';
        }
        function setTheme(t) {
            document.body.className = t === 'light' ? '' : t + '-mode';
        }
    </script>
</body>
</html>
