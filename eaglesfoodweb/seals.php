<?php
include "db.php";
$language_id = isset($_GET['lang']) ? intval($_GET['lang']) : 0;
$sql = "SELECT * FROM seven_seals WHERE language_id = $language_id";
$res = mysqli_query($conn, $sql);

// Get languages for dropdown
$sql_languages = "SELECT `language_id`, `language_name`, `last_updated` FROM `sermon_language` ORDER BY `language_name`";
$result_languages = mysqli_query($conn, $sql_languages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Revelation of Seven Seals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1976d2;
            --bg-body: #f0f4f8;
            --accent: #0288d1;
        }

        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg-body); }

        header {
            background: white; padding: 15px 5%; position: sticky; top: 0; z-index: 1000;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .logo-text { font-size: 1.5rem; font-weight: 800; color: #333; }
        .menu a { text-decoration: none; color: #444; padding: 8px 15px; border-radius: 5px; transition: 0.3s; font-weight: 500; }
        .menu a:hover { background: #e3f2fd; color: var(--primary); }

        /* Language Grid styling */
        .lang-container { max-height: 60vh; overflow-y: auto; padding: 10px; }
        .lang-card { 
            transition: all 0.3s ease; border: none; border-radius: 12px; 
            background: white; text-align: center; cursor: pointer;
        }
        .lang-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); background: var(--primary); color: white !important; }
        .lang-card:hover i { color: white !important; }

        .main-container { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
        .section-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        @media (max-width: 992px) {
            .main-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <span class="logo-text"><i class="fas fa-dove text-primary"></i> Eagles Food</span>
        </div>
        <div class="menu d-none d-md-flex">
            <a href="index.php">Home</a>
            <a href="detail_message_page.php">Messages</a>
            <a href="contact_us.php">Contact</a>
        </div>
    </header>

    <div class="container py-4">
        <?php if ($language_id == 0): ?>
            <div class="section-card text-center">
                <h2 class="mb-4">Select Revelation Language</h2>
                <div class="col-md-6 mx-auto mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" id="langSearch" class="form-control" placeholder="Search language...">
                    </div>
                </div>
                <div class="row g-3 lang-container" id="langGrid">
                    <?php mysqli_data_seek($result_languages, 0); 
                    while ($lang = mysqli_fetch_assoc($result_languages)): ?>
                        <div class="col-6 col-md-3 lang-item">
                            <a href="?lang=<?= $lang['language_id'] ?>" class="card lang-card p-4 h-100 text-decoration-none text-dark">
                                <i class="fas fa-language fa-2x mb-2 text-primary"></i>
                                <h6 class="mb-0"><?= htmlspecialchars($lang['language_name']) ?></h6>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="main-container">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="fas fa-scroll text-primary"></i> Seven Seals</h4>
                        <a href="seals.php" class="btn btn-sm btn-outline-secondary">Change Language</a>
                    </div>
                    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by title...">
                    <div class="table-responsive">
                        <table id="sermonsTable" class="table table-hover">
                            <thead><tr><th>No.</th><th>Sermon Title</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr class="sermon-row" style="cursor:pointer" data-id="<?= $row['language_id'] ?>" data-title="<?= htmlspecialchars($row['title']) ?>" data-date="<?= $row['date'] ?>">
                                        <td><?= $row['language_id'] ?></td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= $row['date'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section-card">
                    <div id="detailsPlaceholder" class="text-center py-5 text-muted">
                        <i class="fas fa-mouse-pointer fa-3x mb-3"></i>
                        <p>Click a sermon to view options</p>
                    </div>
                    <div id="sermonActions" style="display:none">
                        <h5 id="viewTitle" class="text-primary fw-bold"></h5>
                        <p id="viewMeta" class="small text-muted mb-4"></p>
                        <div class="d-grid gap-2">
                            <button onclick="readOnline()" class="btn btn-primary"><i class="fas fa-book-open"></i> Read Online</button>
                            <button onclick="downloadPDF()" class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i> Download PDF</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        let selectedId = null;

        $(document).ready(function() {
            // Language Search
            $("#langSearch").on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $("#langGrid .lang-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Sermons Table
            const table = $('#sermonsTable').DataTable({ dom: 't', pageLength: -1 });
            $('#searchInput').on('keyup', function() { table.search(this.value).draw(); });

            $('.sermon-row').on('click', function() {
                $('.sermon-row').removeClass('table-primary');
                $(this).addClass('table-primary');
                selectedId = $(this).data('id');
                $('#viewTitle').text($(this).data('title'));
                $('#viewMeta').text("Date: " + $(this).data('date'));
                $('#detailsPlaceholder').hide();
                $('#sermonActions').fadeIn();
            });
        });

        function readOnline() {
            if(selectedId) window.location.href = `seals_read.php?language_id=${selectedId}`;
        }
    </script>
</body>
</html>
