<?php
include "db.php";
$language_id = isset($_GET['language_id']) ? intval($_GET['language_id']) : 0;
$sql = "SELECT * FROM church_ages WHERE language_id = $language_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: index.php"); // Redirect if no sermon found
    exit();
}

$sermon = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="te">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eagles Food - Church Ages</title>
    <style>
        body {
            margin: 0;
            font-family: "Noto Sans Telugu", "poppins", sans-serif;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #0288d1;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }

         /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0288d1;
            padding: 10px 20px;
            color: white;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            gap: 15px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
        }

        /* Mobile menu button */
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* Mobile dropdown menu */
        .nav-links-mobile {
            display: none;
            flex-direction: column;
            background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
            padding: 10px;
        }
        

        .nav-links-mobile a {
            color: #e9ecef;
            text-decoration: none;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 1.1rem;
             transition: all 0.3s ease;

        }

        /* Sub-header */
        .sub-header {
            background-color: #e0e0e0;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            font-weight: 500;
        }
        /* Content */
        .content-container {
            padding: 20px;
            max-width: 1400px;
            margin: auto;
            background: white;
            font-size: 20px;
            line-height: 2.0;
            direction: ltr;
            overflow-y: auto;
            max-height: 75vh;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .zoom-controls {
            text-align: center;
            margin: 10px 0;
        }

        .zoom-controls button {
            font-size: 20px;
            padding: 5px 15px;
            margin: 0 10px;
            cursor: pointer;
            background: #0288d1;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .paragraph {
            margin-bottom: 20px;
            white-space: pre-line;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar .nav-links {
                display: none;
            }

            .menu-toggle {
                display: block;
            }

            .nav-links-mobile.active {
                display: flex;
            }

            .content-container {
                font-size: 18px;
                line-height: 1.8;
                max-height: 70vh;
            }
        }

        @media (max-width: 480px) {
            .content-container {
                font-size: 16px;
                padding: 15px;
            }

            .zoom-controls button {
                font-size: 18px;
                padding: 5px 10px;
            }
        }
    </style>
</head>

<body>
  <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Eagles Food</div>
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
        <div class="nav-links">
            <a href="mainpage.php">Home</a>
            <a href="message_read.php">Sermons</a>
            <a href="teachings.php">Teachings</a>
            <a href="about.php">About</a>
            <a href="contact_us.php">Contact</a>
        </div>
    </nav>

    <!-- Mobile Nav -->
    <div class="nav-links-mobile" id="mobileMenu">
        <a href="mainpage.php">Home</a>
        <a href="message_read.php">Sermons</a>
        <a href="teachings.php">Teachings</a>
        <a href="about.php">About</a>
        <a href="contact_us.php">Contact</a>
    </div>

    <div class="sub-header">
        <?= htmlspecialchars($sermon['date']) ?> -
        <?= htmlspecialchars($sermon['title']) ?>
    </div>

    <div class="zoom-controls">
        <button onclick="changeFontSize(1)">🔍+</button>
        <button onclick="changeFontSize(-1)">🔍-</button>
    </div>


    <div class="content-container" id="messageContent">
        <?php
        $paragraphs = explode("\n\n", $sermon['content']);
        foreach ($paragraphs as $para) {
            if (trim($para) !== '') {
                echo "<div class='paragraph'>" . htmlspecialchars($para) . "</div>";
            }
        }
        ?>
    </div>

    <script>
        // Font zoom logic
        let currentFontSize = 20;
        const container = document.getElementById('messageContent');

        function changeFontSize(change) {
            currentFontSize += change;
            container.style.fontSize = currentFontSize + 'px';
        }
          // Toggle mobile menu
        function toggleMenu() {
            document.getElementById("mobileMenu").classList.toggle("active");
        }
    </script>
</body>

</html>