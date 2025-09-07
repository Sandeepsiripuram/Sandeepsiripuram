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

    <!-- Stylesheets & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: rgb(185, 218, 228);
            margin: 0;
            padding: 0;
        }

        header {
            background-color: white;
            box-shadow: 0 2px 10px var(--shadow);
            padding: 15px 5%;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            height: 45px;
            width: 45px;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .menu {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .menu a {
            text-decoration: none;
            color: var(--dark);
            padding: 8px 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .menu a.active {
            background-color:#4d97ea;
            color: white;
        }
        .menu a:hover{
              background-color:#4d97ea;
            color: black;
        }


        /* Enhanced Language Dropdown */
        .language-dropdown {
            position: relative;
            display: inline-block;
            min-width: 250px;
        }

        .language-dropdown-btn {
            background-color: transparent;
            color: var(--text-color);
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
            text-align: left;
        }

        .language-dropdown-btn:hover {
            border-color: #1976d2;
        }

        .language-dropdown-btn i {
            transition: transform 0.3s ease;
        }

        .language-dropdown.active .language-dropdown-btn i {
            transform: rotate(180deg);
        }

        .language-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            width: 100%;
            max-height: 400px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            border-radius: var(--border-radius);
            z-index: 1001;
            top: 100%;
            left: 0;
            margin-top: 5px;
            overflow: hidden;
        }

        .language-dropdown.active .language-dropdown-content {
            display: block;
            animation: fadeIn 0.3s ease;
        }

            .app-info-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .app-info-item:hover {
        background-color: #f8f9fa !important;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    
    .download-badge {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    
    .download-link:hover .download-badge {
        transform: scale(1.05);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    
    .download-link:active .download-badge {
        transform: scale(0.98);
    }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .language-search {
            padding: 10px;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            background: white;
            z-index: 2;
        }

        .language-search input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }

        .language-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .language-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .language-item:hover {
            background-color: #f5f5f5;
        }

        .language-item i {
            color: var(--primary-color);
            font-size: 1rem;
            min-width: 20px;
        }

        .language-item span {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .language-item small {
            color: #666;
            font-size: 0.8rem;
            margin-left: auto;
            white-space: nowrap;
        }

        .no-results {
            padding: 15px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-color);
        }

        .main-container {
            background-color: rgb(185, 218, 228);
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .table-section {
            flex: 1 1 65%;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #eeeeee;
        }

        .side-panel {
            background-color: #eeeeee;
            flex: 1 1 30%;
            padding: 15px;
            border-radius: 8px;
            min-height: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #searchInput {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #sermonsTable tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        #sermonsTable tbody tr.active-row {
            background-color: #d1e7dd !important;
        }

        .read-online img,
        .download-option img {
            width: 24px;
        }

        .format-options button {
            margin: 5px 5px 0 0;
            padding: 6px 12px;
            border: 1px solid #1976d2;
            background: #fff;
            color: #1976d2;
            border-radius: 4px;
            cursor: pointer;
        }

        .sermon-actions {
            display: none;
            margin-top: 15px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            height: 60px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            white-space: nowrap;
        }


        .language-dropdown {
            position: relative;
            display: inline-block;
        }

        .language-dropdown-btn {
            background-color: #f8f9fa;
            color: #333;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .language-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
        }

        .language-dropdown-content a {
            color: #333;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .language-dropdown-content a:hover {
            background-color: #e9ecef;
        }

        .language-dropdown:hover .language-dropdown-content {
            display: block;
        }

        .language-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .language-item i {
            color: #1976d2;
        }

        .language-item small {
            font-size: 0.8em;
            color: #666;
            margin-left: auto;
        }

        .content-area {
            min-height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .language-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            width: 100%;
            max-width: 800px;
        }

        .language-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .language-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .language-card i {
            font-size: 24px;
            color: #1976d2;
            margin-bottom: 8px;
        }

        .language-card h4 {
            margin: 5px 0;
            color: #333;
        }

        .language-card p {
            margin: 0;
            font-size: 0.8em;
            color: #666;
        }

        .hidden {
            display: none;
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .menu a {
                padding: 8px 10px;
                font-size: 0.9rem;
            }

            .logo-text {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 10px 15px;
            }

            .menu {
                display: none;
                width: 100%;
                flex-direction: column;
                gap: 5px;
                padding-top: 15px;
            }

            .menu.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }

            .language-dropdown-content {
                position: static;
                width: 100%;
                box-shadow: none;
                border: 1px solid #eee;
            }

            .main-container {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .logo-text {
                font-size: 1.2rem;
            }

            .logo-img {
                height: 40px;
            }

            .language-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        @media (max-width: 600px) {
            .logo-text {
                font-size: 18px;
            }

            .logo-img {
                height: 30px;
            }

            .menu button {
                font-size: 14px;
            }

            .language-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }

            .menu {
                justify-content: center;
            }
        }
    </style>
</head>

<body>




    <!-- Header -->
    <header>
        <div class="logo">
            <img src="images\appicon.png" alt="Eagle's Food Logo" class="logo-img" />
            <span class="logo-text">Eagles Food</span>
        </div>

        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="menu" id="mainMenu">
            <a href="mainpage.php" class="active"><i class="fas fa-house"></i> Home</a>

            <!-- Enhanced Language Dropdown with Search -->
            <div class="language-dropdown" id="languageDropdown">
                <button class="language-dropdown-btn">
                    <span><i class="fas fa-globe"></i> Select Language</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="language-dropdown-content">
                    <div class="language-search">
                        <input type="text" id="languageSearch" placeholder="Search languages...">
                    </div>
                    <div class="language-list" id="languageList">
                        <?php if ($result_languages && mysqli_num_rows($result_languages) > 0): ?>
                            <?php while ($language = mysqli_fetch_assoc($result_languages)): ?>
                                <a href="?lang=<?= $language['language_id'] ?>" class="language-item">
                                    <i class="fas fa-language"></i>
                                    <span><?= htmlspecialchars($language['language_name']) ?></span>
                                    <small><?= date('M Y', strtotime($language['last_updated'])) ?></small>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-results">No languages available</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <a href="detail_message_page.php" target="_blank"><i class="fas fa-book-open"></i> Read Messages</a>
            <a href="seals.php" target="_blank"><i class="fas fa-scroll"></i> Seven Seals</a>
            <a href="teachings_list.php"><i class="fas fa-book"></i>Teachings</a>
            <a href="contact_us.php"><i class="fas fa-envelope"></i> Contact Us</a>
        </div>
    </header>


    <!-- Main Content Area -->
    <div class="content-area">
        <?php if ($language_id == 0): ?>
            <!-- Show language selection cards when no language is selected -->
            <h3>Select a Language</h3>
            <div class="language-grid">
                <?php
                // Reset pointer for languages result
                mysqli_data_seek($result_languages, 0);
                if ($result_languages && mysqli_num_rows($result_languages) > 0):
                    while ($language = mysqli_fetch_assoc($result_languages)):
                ?>
                        <a href="?lang=<?= $language['language_id'] ?>" class="language-card">
                            <i class="fas fa-globe"></i>
                            <h4><?= htmlspecialchars($language['language_name']) ?></h4>
                            <p>Updated: <?= date('M Y', strtotime($language['last_updated'])) ?></p>
                        </a>
                    <?php
                    endwhile;
                else:
                    ?>
                    <p>No languages available</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Show sermon list when language is selected -->
            <div class="main-container">
                <!-- Table Section -->
                <div class="table-section">
                    <h4>📖 Sermons List</h4>
                    <input  id="searchInput" placeholder="Search sermons...">

                    <div class="table-responsive">
                        <table id="sermonsTable" class="display">
                            <thead>
                                <tr>
                                    <th>Sermon Number</th>
                                    <th>Title</th>
                                    <th>Date</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr class="sermon-row"
                                        data-id="<?= $row['language_id'] ?>"
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-date="<?= $row['date'] ?>">
                                        <td><?= $row['language_id'] ?></td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= $row['date'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary my-2" onclick="exportToCSV()">Export to CSV</button>
                </div>

                <!-- Side Panel -->
                <div class="side-panel" id="sidePanel">
                    <h4>Available Formats</h4>
                    <p id="sermonInfo"><em>Select a sermon to view details</em></p>

                    <div class="sermon-actions" id="sermonActions">
                        <div class="read-online mb-2">
                            <a href="#" id="readOnlineBtn" target="_blank" class="btn btn-primary">
                                <i class="fas fa-book-open"></i> Read Message Online
                            </a>
                        </div>

                        <div class="format-options mt-3">
                            <strong>Formats:</strong><br>
                            <button class="btn btn-sm btn-outline-primary" onclick="downloadPDF()">Download PDF</button>
                            <button class="btn btn-sm btn-outline-success" onclick="printA4()">A4 Print</button>
                        </div>
                    </div>

                   <div class="card mt-3">
    <div class="card-body text-center p-4">
        <?php
        include "db.php";
        
        $sql = "SELECT * FROM `sidepanel_appinfo`";
        $res = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_assoc($res)) {
                ?>
                <div class="app-info-item mb-4 p-3 bg-light rounded">
                    <h6 class="fw-bold text-primary mb-3"><?php echo htmlspecialchars($row['title']); ?></h6>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($row['description']); ?></p>
                    
                    <div class="app-download-links d-flex justify-content-center gap-4">
                        <?php if(!empty($row['image1']) && !empty($row['link1'])): ?>
                            <a href="<?php echo htmlspecialchars($row['link1']); ?>" target="_blank" class="download-link">
                                <img src="http://localhost/eaglesfood_adminpanel/<?php echo htmlspecialchars($row['image1']); ?>" 
                                     width="100" 
                                     alt="App Store"
                                     class="download-badge shadow-sm">
                            </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($row['image2']) && !empty($row['link2'])): ?>
                            <a href="<?php echo htmlspecialchars($row['link2']); ?>" target="_blank" class="download-link">
                                <img src="http://localhost/eaglesfood_adminpanel/<?php echo htmlspecialchars($row['image2']); ?>" 
                                     width="100" 
                                     alt="Google Play"
                                     class="download-badge shadow-sm">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-muted p-3">No app information available</p>';
        }
        
        mysqli_close($conn);
        ?>
    </div>
    </div>
                   

                        <div class="card mt-3">
                            <div class="card-body text-center">
                                <h6>Contribute Message Books</h6>
                                <p>Some of the Books remained without in the App, then you can send the Book in PDF Format or Other formats</p>
                                <a href="add_message.php" class="btn btn-sm btn-primary">Add Message Book</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End main-container -->
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($language_id > 0): ?>
                const table = $('#sermonsTable').DataTable({
                    pageLength: 5,
                    lengthChange: false,
                    ordering: true,
                    info: false
                });
  $('#searchInput').on('keyup', function () {
          table.search(this.value).draw();
        });

                $('#sermonsTable tbody').on('click', 'tr', function() {
                    $('#sermonsTable tbody tr').removeClass('active-row');
                    $(this).addClass('active-row');

                    const title = $(this).data('title');
                    const date = $(this).data('date');
                    const city = "Rev.William Marion Branham";
                    const language_id = $(this).data('id');

                    $('#sermonInfo').html(`<strong>${title}</strong><br>Date: ${date}<br>Preached By: ${city}`);
                    $('#readOnlineBtn').attr('href', `churchages_read.php?language_id=${language_id}`);
                    $('#downloadLink').attr('href', `sermons/pdf/${language_id}.pdf`);

                    // Show the actions panel
                    $('#sermonActions').show();
                });
            <?php endif; ?>
        });

    async function downloadPDF() {
    if (!sermonId) return alert('Please select a sermon.');

    const response = await fetch(`get_churchage.php?id=${id}`);
    const data = await response.json();

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    const marginLeft = 15;
    const verticalOffset = 20;
    const pageHeight = doc.internal.pageSize.height;
    const lineHeight = 8;
    let y = verticalOffset;

    const title = data.title || "Sermon";
    const date = (data.date || "").split(" ")[0]; // Remove time
    const city = data.city || "";

    const text = `${title}\nDate: ${date}<br>\nCity: ${city}<br>\n\n${data.content}`;

    const lines = doc.splitTextToSize(text, 180);

    for (let i = 0; i < lines.length; i++) {
      if (y + lineHeight > pageHeight - 20) {
        doc.addPage();
        y = verticalOffset;
      }
      doc.text(lines[i], marginLeft, y);
      y += lineHeight;
    }

    const safeTitle = title.replace(/[^a-z0-9]/gi, '_').toLowerCase();
    doc.save(`${date}-${safeTitle}.pdf`);
  }

  async function printA4() {
    if (!sermonId) return alert('Please select a sermon.');

    const response = await fetch(`get_churchage.php?id=${id}`);
    const data = await response.json();

    const title = data.title || "Sermon";
    const date = (data.date || "").split(" ")[0];
    const city = data.city || "";
    const content = data.content.replace(/\n/g, '<br>');

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
      <head>
        <title>${title}</title>
        <style>
          body { font-family: Arial, sans-serif,poppins,noto serif telugu; margin: 40px; line-height: 1.6; color: #000; }
          h1, h2 { margin-bottom: 10px;text-align:center; }
          p { white-space: pre-wrap; }
        </style>
      </head>
      <body>
        <h2>${title}</h2>
        <h4> ${date}</h4>
        <h4> Preached By :Rev. William Marion Branham</h4>
        <hr>
        <p>${content}</p>
        <script>
          window.onload = function() {
            window.print();
          }
        <\/script>
      </body>
      </html>
    `);

    printWindow.document.close();
  }

        function exportToCSV() {
            const rows = [
                ["Sermon Number", "Title", "Date", "City"]
            ];
            document.querySelectorAll("#sermonsTable tbody tr").forEach(tr => {
                const row = Array.from(tr.children).map(td => {
                    const content = td.textContent ? td.textContent.trim() : '';
                    return `"${content.replace(/"/g, '""')}"`;
                });
                rows.push(row);
            });

            const csvContent = rows.map(r => r.join(",")).join("\n");
            const blob = new Blob(["\uFEFF" + csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.href = url;
            link.download = "sermons.csv";
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();

            setTimeout(() => {
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }, 100);
        }
    </script>


    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('mainMenu').classList.toggle('active');
        });

        // Language dropdown functionality
        const languageDropdown = document.getElementById('languageDropdown');
        const languageSearch = document.getElementById('languageSearch');
        const languageList = document.getElementById('languageList');
        const languageItems = languageList.querySelectorAll('.language-item');

        // Toggle dropdown
        languageDropdown.querySelector('.language-dropdown-btn').addEventListener('click', function() {
            languageDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!languageDropdown.contains(event.target)) {
                languageDropdown.classList.remove('active');
            }
        });

        // Language search functionality
        languageSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasResults = false;

            languageItems.forEach(item => {
                const languageName = item.querySelector('span').textContent.toLowerCase();
                if (languageName.includes(searchTerm)) {
                    item.style.display = 'flex';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show no results message if no matches
            const noResults = languageList.querySelector('.no-results');
            if (!hasResults && !noResults) {
                languageList.innerHTML = '<div class="no-results">No languages found</div>';
            } else if (hasResults && noResults) {
                noResults.remove();
            }
        });

          // Click handler for language items
        languageItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Update dropdown button text
                const languageName = this.querySelector('span').textContent;
                languageDropdown.querySelector('.language-dropdown-btn span').innerHTML = 
                    `<i class="fas fa-globe"></i> ${languageName}`;
                
                // Close dropdown
                languageDropdown.classList.remove('active');
            });
        });
    </script>

    <script>
        // Smooth scrolling for table of contents links
        document.querySelectorAll('.toc a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            });
        });

        // Responsive header menu
        const menu = document.querySelector('.menu');
        const menuItems = menu.querySelectorAll('a');

        window.addEventListener('resize', () => {
            if (window.innerWidth < 600) {
                menuItems.forEach(item => {
                    item.style.width = '100%';
                    item.style.margin = '5px 0';
                });
            } else {
                menuItems.forEach(item => {
                    item.style.width = '';
                    item.style.margin = '';
                });
            }
        });
    </script>
</body>

</html>