<?php
include "db.php";
$language_id = isset($_GET['lang']) ? intval($_GET['lang']) : 0;
$sql = "SELECT * FROM sermons_list WHERE language_id = $language_id";
$res = mysqli_query($conn, $sql);

// Get languages for dropdown
$sql_languages = "SELECT `language_id`, `language_name`, `last_updated` FROM `sermon_language` ORDER BY `language_name`";
$result_languages = mysqli_query($conn, $sql_languages);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Eagles Food - Sermon Reading</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --primary: #1976d2;
            --secondary: #78909c;
            --accent: #d78407;
            --bg-body: #b9dae4;
            --bg-card: #eeeeee;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header & Navigation */
        header {
            background: var(--white);
            box-shadow: var(--shadow);
            padding: 10px 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
        .logo-img { height: 45px; border-radius: 50%; }
        .logo-text { font-size: 1.4rem; font-weight: 700; color: #333; }

        .menu { display: flex; gap: 10px; align-items: center; }
        .menu a {
            text-decoration: none;
            color: #444;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .menu a:hover, .menu a.active {
            background: var(--primary);
            color: var(--white);
        }

        /* Language Dropdown Custom Styling */
        .language-dropdown { position: relative; width: 220px; }
        .lang-btn {
            background: #f8f9fa;
            border: 1px solid #ddd;
            width: 100%;
            padding: 8px 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .lang-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            box-shadow: var(--shadow);
            border-radius: 6px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1100;
        }
        .lang-content.show { display: block; }
        .lang-item {
            padding: 10px;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #eee;
        }
        .lang-item:hover { background: #f1f1f1; }

        /* Layout Grid */
        .main-container {
            max-width: 1300px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 0 15px;
        }

        .section-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        /* Search & Table */
        #searchInput {
            margin-bottom: 15px;
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid #ccc;
        }

        .active-row { background-color: #bbdefb !important; }

        /* App Info Badges */
        .app-badge-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .app-badge-container img {
            max-width: 120px;
            height: auto;
            transition: transform 0.2s;
        }
        .app-badge-container img:hover { transform: scale(1.05); }

        /* Responsive */
        @media (max-width: 992px) {
            .main-container { grid-template-columns: 1fr; }
            .menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: var(--shadow);
            }
            .menu.show { display: flex; }
            .menu-toggle { display: block; background: none; border: none; font-size: 1.5rem; }
        }

        @media (min-width: 993px) {
            .menu-toggle { display: none; }
        }

        /* Language Grid for Default View */
        .lang-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .lang-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: var(--shadow);
            transition: 0.3s;
        }
        .lang-card:hover { background: var(--primary); color: white; transform: translateY(-5px); }
    </style>
</head>

<body>

    <header>
        <a href="index.php" class="logo">
            <img src="images/appicon.png" alt="Logo" class="logo-img" />
            <span class="logo-text">Eagles Food</span>
        </a>

        <button class="menu-toggle" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <div class="menu" id="mainMenu">
            <a href="index.php"><i class="fas fa-house"></i> Home</a>

            <div class="language-dropdown">
                <div class="lang-btn" id="langSelectBtn">
                    <span><i class="fas fa-globe"></i> Language</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="lang-content" id="langDropdown">
                    <div class="p-2"><input type="text" id="langSearch" class="form-control form-control-sm" placeholder="Search..."></div>
                    <?php 
                    mysqli_data_seek($result_languages, 0);
                    while ($l = mysqli_fetch_assoc($result_languages)): ?>
                        <a href="?lang=<?= $l['language_id'] ?>" class="lang-item">
                            <strong><?= htmlspecialchars($l['language_name']) ?></strong>
                            <small>Updated: <?= date('M Y', strtotime($l['last_updated'])) ?></small>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <a href="teachings_list.php"><i class="fas fa-book"></i> Teachings</a>
            <a href="contact_us.php"><i class="fas fa-envelope"></i> Contact Us</a>
        </div>
    </header>

    <div class="container py-4">
        <?php if ($language_id == 0): ?>
            <div class="text-center">
                <h3>Select Your Preferred Language</h3>
                <div class="lang-grid">
                    <?php 
                    mysqli_data_seek($result_languages, 0);
                    while ($l = mysqli_fetch_assoc($result_languages)): ?>
                        <a href="?lang=<?= $l['language_id'] ?>" class="lang-card">
                            <i class="fas fa-language fa-2x mb-2"></i>
                            <h5><?= htmlspecialchars($l['language_name']) ?></h5>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="main-container">
                <div class="section-card">
                    <h4 class="mb-3">📖 Sermons List</h4>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by title, date or city...">
                    <div class="table-responsive">
                        <table id="sermonsTable" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>City</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr class="sermon-row" 
                                        data-id="<?= $row['sermon_id'] ?>" 
                                        data-title="<?= htmlspecialchars($row['sermon_title']) ?>"
                                        data-date="<?= $row['date'] ?>"
                                        data-city="<?= htmlspecialchars($row['city']) ?>">
                                        <td><?= $row['sermon_id'] ?></td>
                                        <td><?= htmlspecialchars($row['sermon_title']) ?></td>
                                        <td><?= $row['date'] ?></td>
                                        <td><?= htmlspecialchars($row['city']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-outline-dark mt-3" onclick="exportToCSV()">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </div>

                <div class="section-card" id="sidePanel">
                    <h5>Sermon Details</h5>
                    <div id="sermonPlaceholder" class="text-muted italic">Select a sermon from the list to view actions.</div>
                    
                    <div id="sermonActions" style="display:none;">
                        <div id="activeSermonInfo" class="alert alert-info py-2"></div>
                        <div class="d-grid gap-2">
                            <a href="#" id="readOnlineBtn" target="_blank" class="btn btn-primary">
                                <i class="fas fa-book-open"></i> Read Online
                            </a>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button class="btn btn-outline-danger w-100" onclick="downloadPDF()">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-outline-success w-100" onclick="printA4()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-center">
                        <p class="small fw-bold mb-2">Get the Mobile App</p>
                        <div class="app-badge-container">
                            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="iOS"></a>
                            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Android"></a>
                        </div>
                    </div>

                    <div class="card mt-3 bg-white border-0">
                        <div class="card-body text-center">
                            <p class="small mb-2">Have a message book missing?</p>
                            <a href="add_message.php" class="btn btn-sm btn-outline-primary">Add Message</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        let sermonId = null;

        $(document).ready(function() {
            // Mobile Menu Toggle
            $('#menuBtn').click(function() { $('#mainMenu').toggleClass('show'); });

            // Dropdown Toggle
            $('#langSelectBtn').click(function(e) {
                e.stopPropagation();
                $('#langDropdown').toggleClass('show');
            });

            $(document).click(function() { $('#langDropdown').removeClass('show'); });

            // Language Search
            $('#langSearch').on('keyup', function() {
                let val = $(this).val().toLowerCase();
                $('.lang-item').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1)
                });
            });

            // DataTable init
            if ($('#sermonsTable').length) {
                const table = $('#sermonsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    dom: 'tp', // Hides the default search box
                });

                $('#searchInput').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#sermonsTable tbody').on('click', 'tr', function() {
                    $('tr').removeClass('active-row');
                    $(this).addClass('active-row');

                    sermonId = $(this).data('id');
                    const title = $(this).data('title');
                    const date = $(this).data('date');
                    const city = $(this).data('city');

                    $('#sermonPlaceholder').hide();
                    $('#sermonActions').show();
                    $('#activeSermonInfo').html(`<strong>${title}</strong><br><small>${date} • ${city}</small>`);
                    $('#readOnlineBtn').attr('href', `message_read.php?sermon_id=${sermonId}`);
                });
            }
        });

        async function downloadPDF() {
            if (!sermonId) return;
            const res = await fetch(`get_sermon_content.php?id=${sermonId}`);
            const data = await res.json();
            
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(18);
            doc.text(data.title, 10, 20);
            doc.setFontSize(12);
            doc.text(`Date: ${data.date} | City: ${data.city}`, 10, 30);
            
            const splitText = doc.splitTextToSize(data.content, 180);
            doc.text(splitText, 10, 45);
            doc.save(`${data.title}.pdf`);
        }

        async function printA4() {
            if (!sermonId) return;
            window.open(`message_print.php?sermon_id=${sermonId}`, '_blank');
        }

        function exportToCSV() {
            let csv = "ID,Title,Date,City\n";
            $('#sermonsTable tbody tr').each(function() {
                let row = [];
                $(this).find('td').each(function() { row.push(`"${$(this).text()}"`); });
                csv += row.join(",") + "\n";
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sermons.csv';
            a.click();
        }
    </script>
</body>
</html>
