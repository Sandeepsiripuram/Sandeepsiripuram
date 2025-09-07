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
  <title>Eagles Food-Sermon Reading</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Stylesheets & Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jsPDF for PDF creation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

    /* Header Styles */
    header {
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
      color: #333;
      padding: 8px 15px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .menu a:hover {
      background-color: rgb(78, 156, 234);
      color: black;
    }
    
    .menu a.active {
      background-color: rgb(78, 156, 234);
      color: white;
    }
     
    /* Enhanced Language Dropdown */
    .language-dropdown {
      position: relative;
      display: inline-block;
      min-width: 250px;
    }

    .language-dropdown-btn {
      background-color: transparent;
      color: #333;
      padding: 8px 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
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
      border-radius: 4px;
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

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
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
      border-radius: 4px;
      font-size: 0.9rem;
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
      color: #333;
    }

    .tab-card.active {
      border-color: rgb(215, 132, 7);
      background-color: rgba(230, 240, 255, 0.95);
    }
    
    .main-container {
      background-color: rgb(187, 220, 230);
      padding: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      width: 100%;
    }

    .table-section {
      flex: 1 1 65%;
      min-width: 0; /* Important for flexbox constraints */
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      background-color: #eeeeee;
    }

    .side-panel {
      background-color: #eeeeee;
      flex: 1 1 30%;
      min-width: 0; /* Important for flexbox constraints */
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
      font-size: 22px;
      font-weight: bold;
      white-space: nowrap;
    }

    .language-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
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
      width: 100%;
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
      max-width: 100%;
      height: auto;
    }
    
    .download-link:hover .download-badge {
      transform: scale(1.05);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    
    .download-link:active .download-badge {
      transform: scale(0.98);
    }

    /* Improved table responsiveness */
    .table-container {
      overflow-x: auto;
      width: 100%;
    }

    #sermonsTable {
      width: 100%;
    }

    /* Improved mobile styles */
    @media (max-width: 1200px) {
      .table-section, .side-panel {
        flex: 1 1 100%;
      }
      
      .main-container {
        flex-direction: column;
      }
    }

    @media (max-width: 992px) {
      .language-dropdown {
        min-width: 200px;
      }
      
      .logo-text {
        font-size: 18px;
      }
      
      .logo-img {
        height: 40px;
      }
    }

    @media (max-width: 768px) {
      header {
        padding: 10px 5%;
      }
      
      .menu-toggle {
        display: block;
      }
      
      .menu {
        display: none;
        flex-direction: column;
        width: 100%;
        margin-top: 15px;
        gap: 8px;
      }
      
      .menu.active {
        display: flex;
      }
      
      .language-dropdown {
        width: 100%;
        min-width: auto;
      }
      
      .main-container {
        padding: 10px;
        gap: 15px;
      }
      
      .table-section, .side-panel {
        padding: 12px;
        width: 100%;
        flex: 1 1 100%;
      }
      
      .logo-text {
        font-size: 16px;
      }

      .logo-img {
        height: 35px;
      }
      
      .language-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
      }
      
      .app-download-links {
        flex-direction: column;
        gap: 1rem !important;
      }
      
      .content-area {
        padding: 10px;
      }
      
      .language-item {
        padding: 8px 10px;
        font-size: 0.9rem;
      }
      
      /* Improve table appearance on mobile */
      #sermonsTable th, 
      #sermonsTable td {
        padding: 6px 8px;
        font-size: 14px;
      }
      
      #sermonsTable th:nth-child(1),
      #sermonsTable td:nth-child(1) {
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      
      #sermonsTable th:nth-child(2),
      #sermonsTable td:nth-child(2) {
        min-width: 150px;
      }
    }

    @media (max-width: 576px) {
      .logo-text {
        font-size: 16px;
      }
      .main-container {
        padding: 10px;
      
      }

      .logo-img {
        height: 35px;
      }
      
      .language-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      }
      
      .app-download-links {
        flex-direction: column;
        gap: 1rem !important;
      }
      
      .download-badge {
        width: 120px !important;
      }
      
      .content-area {
        padding: 10px 5px;
      }
      
      .table-section, .side-panel {
        padding: 10px;
      }
      
      /* Further improve table for very small screens */
      #sermonsTable {
        font-size: 13px;
      }
      
      #sermonsTable th, 
      #sermonsTable td {
        padding: 4px 6px;
      }
      
      #sermonsTable th:nth-child(3),
      #sermonsTable td:nth-child(3),
      #sermonsTable th:nth-child(4),
      #sermonsTable td:nth-child(4) {
        display: none;
      }
    }
  </style>
</head>

<body>

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

            
            <a href="seals.php" target="myTab"><i class="fas fa-scroll"></i> Seven Seals</a>
            <a href="church_ages.php" target="myTab"><i class="fas fa-church"></i> Seven Church Ages</a>
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
          <input type="text" id="searchInput" placeholder="Search sermons...">

          <div class="table-container">
            <table id="sermonsTable" class="display">
              <thead>
                <tr>
                  <th>Sermon Number</th>
                  <th>Title</th>
                  <th>Date</th>
                  <th>Location</th>
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
          <button class="btn btn-primary my-2 w-100-mobile" onclick="exportToCSV()">Export to CSV</button>
        </div>

        <!-- Side Panel -->
        <div class="side-panel" id="sidePanel">
          <h4>Available Formats</h4>
          <p id="sermonInfo"><em>Select a sermon to view details</em></p>

          <div class="sermon-actions" id="sermonActions">
            <div class="read-online mb-2">
              <a href="#" id="readOnlineBtn" target="myTab" class="btn btn-primary w-100-mobile">
                <i class="fas fa-book-open"></i> Read Message Online
              </a>
            </div>

            <div class="format-options mt-3">
              <strong>Formats:</strong><br>
              <button class="btn btn-sm btn-outline-primary w-100-mobile" onclick="downloadPDF()">Download PDF</button>
              <button class="btn btn-sm btn-outline-success w-100-mobile mt-2" onclick="printA4()">A4 Print</button>
            </div>

          </div>

         <div class="card mt-3">
    <div class="card-body text-center p-3">
        <?php
        include "db.php";
        
        $sql = "SELECT * FROM `sidepanel_appinfo`";
        $res = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_assoc($res)) {
                ?>
                <div class="app-info-item mb-3 p-3 bg-light rounded">
                    <h6 class="fw-bold text-primary mb-2"><?php echo htmlspecialchars($row['title']); ?></h6>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($row['description']); ?></p>
                    
                    <div class="app-download-links d-flex justify-content-center flex-wrap gap-3">
                        <?php if(!empty($row['image1']) && !empty($row['link1'])): ?>
                            <a href="<?php echo htmlspecialchars($row['link1']); ?>" target="myTab" class="download-link">
                                <img src="http://localhost/eaglesfood_adminpanel/<?php echo htmlspecialchars($row['image1']); ?>" 
                                     width="100" 
                                     alt="App Store"
                                     class="download-badge shadow-sm">
                            </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($row['image2']) && !empty($row['link2'])): ?>
                            <a href="<?php echo htmlspecialchars($row['link2']); ?>" target="myTab" class="download-link">
                                <img src="http://localhost/eaglesfood_adminpanel/<?php echo htmlspecialchars($row['image2']); ?>" 
                                     width="120" 
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

            <div class="card mt-3">
              <div class="card-body text-center">
                <h6>Contribute Message Books</h6>
                <p>Some of the Books remained without in the App, then you can send the Book in PDF Format or Other formats</p>
                <a href="add_message.php" class="btn btn-sm btn-primary w-100-mobile">Add Message Book</a>
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
    let sermonId = null;

    $(document).ready(function () {
      <?php if ($language_id > 0): ?>
        const table = $('#sermonsTable').DataTable({
          pageLength: 5,
          lengthChange: false,
          ordering: true,
          info: false,
          responsive: true
        });

        $('#searchInput').on('keyup', function () {
          table.search(this.value).draw();
        });

        $('#sermonsTable tbody').on('click', 'tr', function () {
          $('#sermonsTable tbody tr').removeClass('active-row');
          $(this).addClass('active-row');

          const title = $(this).data('title');
          const date = $(this).data('date');
          const city = $(this).data('city');
          sermonId = $(this).data('id'); // updated sermonId here
          const preached_by = "Rev. William Marion Branham";

          $('#sermonInfo').html(`
            <strong>${title}</strong><br>
            Date: ${date}<br>
            City: ${city}<br>
            Preached by: ${preached_by}
          `);

          $('#readOnlineBtn').attr('href', `message_read.php?sermon_id=${sermonId}`);
          $('#downloadLink').attr('href', `content/pdf/${sermonId}.pdf`);

          $('#sermonActions').show();
        });
      <?php endif; ?>
    });

    async function downloadPDF() {
      if (!sermonId) return alert('Please select a sermon.');

      const response = await fetch(`get_sermon_content.php?id=${sermonId}`);
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

      const response = await fetch(`get_sermon_content.php?id=${sermonId}`);
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
          <h4> ${city}</h4>
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
      const rows = [["Sermon Number", "Title", "Date", "City"]];
      document.querySelectorAll("#sermonsTable tbody tr").forEach(tr => {
        const row = Array.from(tr.children).map(td => {
          const content = td.textContent ? td.textContent.trim() : '';
          return `"${content.replace(/"/g, '""')}"`;
        });
        rows.push(row);
      });

      const csvContent = rows.map(r => r.join(",")).join("\n");
      const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
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
    
    // Mobile menu toggle functionality
    document.getElementById('menuToggle').addEventListener('click', function() {
      const menu = document.getElementById('mainMenu');
      menu.classList.toggle('active');
      
      // Toggle between hamburger and close icon
      const icon = this.querySelector('i');
      if (icon.classList.contains('fa-bars')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });

    // Language dropdown functionality
    const languageDropdown = document.getElementById('languageDropdown');
    const languageSearch = document.getElementById('languageSearch');
    const languageList = document.getElementById('languageList');
    const languageItems = languageList.querySelectorAll('.language-item');

    // Toggle dropdown
    languageDropdown.querySelector('.language-dropdown-btn').addEventListener('click', function(e) {
      e.stopPropagation();
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
        // Restore original items if they were replaced
        if (languageList.children.length === 1) {
          languageItems.forEach(item => {
            item.style.display = 'flex';
          });
        }
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

    // Add utility class for mobile
    document.querySelectorAll('.w-100-mobile').forEach(el => {
      if (window.innerWidth < 768) {
        el.classList.add('w-100');
        el.classList.add('mb-1');
      }
    });
  </script>
  
</body>

</html>