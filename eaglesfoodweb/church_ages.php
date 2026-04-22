<?php
include "db.php";
$language_id = isset($_GET['lang']) ? intval($_GET['lang']) : 0;
$sql = "SELECT * FROM church_ages WHERE language_id = $language_id";
$res = mysqli_query($conn, $sql);

// Get languages for dropdown
$sql_languages = "SELECT `language_id`, `language_name`, `last_updated` FROM `sermon_language` ORDER BY `language_name`";
$result_languages = mysqli_query($conn, $sql_languages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Seven Church Ages - William Marion Branham</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1976d2;
            --primary-hover: #1565c0;
            --bg-color: #b9dae4;
            --panel-bg: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
        }

        /* --- Navigation --- */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 10px 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-img {
            height: 45px;
            width: auto;
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }

        .menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }

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
            background-color: var(--primary);
            color: white !important;
        }

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
        }

       /* --- Optimized Language Grid --- */
.language-grid {
    display: grid;
    /* Reduced min-width from 220px to 160px for smaller tabs */
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
    gap: 12px;
    width: 100%;
    max-width: 1000px;
    padding: 15px;
}

.language-card {
    background: white;
    border-radius: 8px;
    /* Reduced padding from 25px to 12px */
    padding: 12px; 
    text-decoration: none;
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.language-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background-color: #f8f9fa;
}

.language-card i {
    /* Reduced icon size from 2.5rem to 1.5rem */
    font-size: 1.5rem; 
    color: var(--primary);
    margin-bottom: 8px;
}

.language-card h5 {
    /* Smaller font for the language name */
    font-size: 0.95rem; 
    margin-bottom: 4px;
    font-weight: 600;
}

.language-card small {
    /* Extra small font for the update text */
    font-size: 0.75rem; 
}

        /* --- Layout --- */
        .main-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            padding: 20px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .table-section {
            flex: 1 1 650px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .side-panel {
            flex: 1 1 320px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sticky-side {
            position: sticky;
            top: 90px;
        }

        .side-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* --- Table Styling --- */
        #sermonsTable tbody tr {
            transition: background 0.2s;
            cursor: pointer;
        }

        #sermonsTable tbody tr.active-row {
            background-color: #e3f2fd !important;
            border-left: 4px solid var(--primary);
        }

        .app-badge {
            transition: transform 0.2s;
            max-width: 120px;
        }

        .app-badge:hover {
            transform: scale(1.05);
        }

        /* --- Language Dropdown --- */
        .language-dropdown {
            position: relative;
        }

        .language-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 250px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1001;
        }

        .language-dropdown.active .language-dropdown-content {
            display: block;
        }

        /* --- Mobile Responsiveness --- */
        @media (max-width: 992px) {
            .menu-toggle { display: block; }
            .menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 65px;
                left: 0;
                width: 100%;
                background: white;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }
            .menu.active { display: flex; }
            .main-container { flex-direction: column; }
            .side-panel { flex: none; width: 100%; }
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo">
            <img src="images/appicon.png" alt="Logo" class="logo-img" />
            <span class="logo-text">Eagles Food</span>
        </a>

        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="menu" id="mainMenu">
            <a href="index.php"><i class="fas fa-house"></i> Home</a>
            
            <div class="language-dropdown" id="languageDropdown">
                <a href="javascript:void(0)" class="dropdown-trigger">
                    <i class="fas fa-globe"></i> Language <i class="fas fa-chevron-down ms-1" style="font-size: 0.7rem"></i>
                </a>
                <div class="language-dropdown-content p-2">
                    <input type="text" id="langSearch" class="form-control form-control-sm mb-2" placeholder="Search...">
                    <div id="langList" style="max-height: 300px; overflow-y: auto;">
                        <?php 
                        mysqli_data_seek($result_languages, 0);
                        while($lang = mysqli_fetch_assoc($result_languages)): 
                        ?>
                            <a href="?lang=<?= $lang['language_id'] ?>" class="dropdown-item py-2 border-bottom">
                                <?= htmlspecialchars($lang['language_name']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <a href="seals.php"><i class="fas fa-scroll"></i> Seven Seals</a>
            <a href="teachings_list.php"><i class="fas fa-book"></i> Teachings</a>
            <a href="contact_us.php"><i class="fas fa-envelope"></i> Contact</a>
        </nav>
    </header>

    <main class="py-4">
        <?php if ($language_id == 0): ?>
            <div class="container text-center">
                <h2 class="mb-4">Select Your Language</h2>
                <div class="language-grid mx-auto">
                    <?php 
                    mysqli_data_seek($result_languages, 0);
                    while ($language = mysqli_fetch_assoc($result_languages)): 
                    ?>
                        <a href="?lang=<?= $language['language_id'] ?>" class="language-card">
                            <i class="fas fa-globe-africa"></i>
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($language['language_name']) ?></h5>
                            <small class="text-muted">Updated: <?= date('M Y', strtotime($language['last_updated'])) ?></small>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="main-container">
                <div class="table-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">📖 Sermon Books</h4>
                        <button class="btn btn-sm btn-outline-success" onclick="exportToCSV()">
                            <i class="fas fa-file-csv me-1"></i> Export
                        </button>
                    </div>
                    
                    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by title or date...">

                    <div class="table-responsive">
                        <table id="sermonsTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sermon Title</th>
                                    <th>Date Preached</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr class="sermon-row" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-date="<?= $row['date'] ?>">
                                        <td><?= $row['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                                        <td><?= date('d M, Y', strtotime($row['date'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="side-panel">
                    <div class="sticky-side">
                        <div class="side-card mb-3">
                            <h5 class="fw-bold text-primary mb-3">Sermon Details</h5>
                            <div id="sermonPlaceholder" class="text-muted small">
                                <i class="fas fa-arrow-left me-1"></i> Click a sermon from the list to view options.
                            </div>
                            
                            <div id="sermonActions" style="display:none;">
                                <h6 id="activeSermonTitle" class="fw-bold mb-1"></h6>
                                <p id="activeSermonDate" class="text-muted small mb-3"></p>
                                
                                <div class="d-grid gap-2">
                                    <a href="" id="readOnlineBtn" class="btn btn-primary">
                                        <i class="fas fa-book-open me-2"></i> Read Online
                                    </a>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button onclick="downloadPDF()" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button onclick="printA4()" class="btn btn-outline-dark btn-sm w-100">
                                                <i class="fas fa-print me-1"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="side-card text-center">
                            <h6 class="fw-bold">Mobile App</h6>
                            <p class="small text-muted">Study on your mobile device.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" class="app-badge" alt="Play Store">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/67/App_Store_%28iOS%29.svg" class="app-badge" alt="App Store">
                            </div>
                        </div>

                        <div class="side-card mt-3 bg-light border text-center">
                            <h6 class="fw-bold">Missing a book?</h6>
                            <p class="small">Help us complete the collection.</p>
                            <a href="add_message.php" class="btn btn-sm btn-primary">Contribute</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let selectedSermonId = null;

        $(document).ready(function() {
            // Menu Toggles
            $('#menuToggle').click(() => $('#mainMenu').toggleClass('active'));
            $('.dropdown-trigger').click((e) => {
                e.stopPropagation();
                $('#languageDropdown').toggleClass('active');
            });
            $(document).click(() => $('.language-dropdown').removeClass('active'));

            // DataTable Setup
            if ($('#sermonsTable').length) {
                const table = $('#sermonsTable').DataTable({
                    pageLength: 10,
                    dom: 'tpr', // Hide default search/length
                });

                $('#searchInput').on('keyup', function() {
                    table.search(this.value).draw();
                });

               // Row Click logic
$('#sermonsTable tbody').on('click', 'tr', function() {
    $('#sermonsTable tr').removeClass('active-row');
    $(this).addClass('active-row');
    
    // Extract data from the clicked row attributes
    const sermonId = $(this).attr('data-id');
    const sermonTitle = $(this).attr('data-title');
    const sermonDate = $(this).attr('data-date');

    selectedSermonId = sermonId;

    // Show the side panel actions
    $('#sermonPlaceholder').hide();
    $('#sermonActions').show();
    
    // Update labels
    $('#activeSermonTitle').text(sermonTitle);
    $('#activeSermonDate').text("Preached: " + sermonDate);
    
    // FIX: Set the Read Online URL correctly
    $('#readOnlineBtn').attr('href', 'churchages_read.php?id=' + sermonId);
});
            }

            // Language Search
            $('#langSearch').on('keyup', function() {
                const val = $(this).val().toLowerCase();
                $('#langList a').each(function() {
                    $(this).toggle($(this).text().toLowerCase().includes(val));
                });
            });
        });

        // PDF and Print Logic
        async function downloadPDF() {
            if (!selectedSermonId) return alert('Select a sermon first.');
            // Implementation follows your existing fetch logic
            window.location.href = `get_churchage_pdf.php?id=${selectedSermonId}`;
        }

        async function printA4() {
            if (!selectedSermonId) return alert('Select a sermon first.');
            const response = await fetch(`get_churchage.php?id=${selectedSermonId}`);
            const data = await response.json();
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html><head><title>${data.title}</title>
                <style>body{font-family:serif; padding:40px; line-height:1.6;} h1{text-align:center;}</style>
                </head><body>
                <h1>${data.title}</h1>
                <p><strong>Date:</strong> ${data.date}</p><hr>
                <div>${data.content}</div>
                <script>window.onload = () => { window.print(); window.close(); }<\/script>
                </body></html>
            `);
            printWindow.document.close();
        }

        function exportToCSV() {
            let csv = "ID,Title,Date\n";
            $('#sermonsTable tbody tr').each(function() {
                const row = $(this).find('td');
                csv += `"${row[0].innerText}","${row[1].innerText}","${row[2].innerText}"\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('download', 'sermons_list.csv');
            a.click();
        }
    </script>
</body>
</html>
