<?php
session_start();
include "db.php"; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login_page.php");
    exit;
}

$upload_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_sermon'])) {
    $user_id = $_SESSION['user_id'];
    
    $update_sql = "UPDATE users SET upload_count = upload_count + 1 WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['upload_count'] += 1;
        $upload_msg = "<div class='alert-success'><i class='fas fa-check-circle'></i> Sermon submitted successfully!</div>";
    } else {
        $upload_msg = "<div class='alert-danger'>Error updating records.</div>";
    }
}

$user_name    = $_SESSION['user_name'] ?? 'User'; 
$email        = $_SESSION['email'] ?? 'Not available';
$pastor_name  = $_SESSION['pastor_name'] ?? 'Not assigned';
$phone        = $_SESSION['phone'] ?? 'Not provided';
$upload_count = $_SESSION['upload_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Eagles Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #4361ee; --secondary: #764ba2; --bg: #f8f9fc;
            --white: #ffffff; --text-dark: #2d3436; --text-light: #636e72; --danger: #ef233c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg); color: var(--text-dark); display: flex; min-height: 100vh; }

        .sidebar {
            width: 260px; background: linear-gradient(180deg, #4361ee 0%, #764ba2 100%);
            color: white; padding: 30px 20px; position: fixed; height: 100vh; z-index: 1000;
        }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 40px; text-align: center; font-weight: 800; }
        .nav-link {
            display: flex; align-items: center; color: rgba(255,255,255,0.8);
            text-decoration: none; padding: 12px 15px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.2); color: white; }
        .nav-link i { margin-right: 15px; width: 20px; }

        .main-content { flex: 1; margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }

        .card { background: var(--white); padding: 25px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .stat-item { background: var(--white); padding: 20px; border-radius: 12px; display: flex; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .stat-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
        
        .blue { background: #e0e7ff; color: #4361ee; }
        .purple { background: #f3e8ff; color: #764ba2; }

        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; color: #444; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
        
        /* Select2 override to match theme */
        .select2-container--default .select2-selection--single { height: 42px; border: 1px solid #ddd; border-radius: 8px; padding-top: 5px; }

        .submit-btn { background: var(--primary); color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; margin-top: 10px; }
        .submit-btn:hover { background: #3046bd; transform: translateY(-2px); }

        .alert-success { padding: 12px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745; }

        .app-badges img { width: 120px; margin: 5px; transition: 0.2s; }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: 70px; bottom: 0; top: auto; padding: 0; display: flex; align-items: center; }
            .sidebar h2 { display: none; }
            .sidebar nav { display: flex; width: 100%; justify-content: space-around; }
            .nav-link { flex-direction: column; font-size: 0.7rem; padding: 5px; margin: 0; }
            .nav-link i { margin: 0 0 4px 0; font-size: 1.2rem; }
            .main-content { margin-left: 0; padding: 15px; padding-bottom: 100px; width: 100%; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>EAGLES FOOD</h2>
        <nav>
            <a href="index.php" class="nav-link"><i class="fas fa-house"></i> <span>Home</span></a>
            <a href="#" class="nav-link active"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="welcome-msg">
                <h1>Hi, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p style="color: var(--text-light);">Ministry Dashboard</p>
            </div>
            <a href="logout.php" style="color: var(--danger); text-decoration: none; font-weight: bold;"><i class="fas fa-power-off"></i> Logout</a>
        </div>

        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon blue"><i class="fas fa-cloud-upload-alt"></i></div>
                <div><h3><?php echo $upload_count; ?></h3><p style="font-size: 0.7rem;">Contributions</p></div>
            </div>
            <div class="stat-item">
                <div class="stat-icon purple"><i class="fas fa-user-tie"></i></div>
                <div><h3><?php echo htmlspecialchars($pastor_name); ?></h3><p style="font-size: 0.7rem;">Pastor</p></div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="upload-section">
                <div class="card">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-file-upload"></i> Contribute a Sermon</h3>
                    <?php echo $upload_msg; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Sermon Title</label>
                            <input type="text" name="message_title" placeholder="e.g. The Deep Calleth To The Deep" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;" class="form-row-mobile">
                            <div class="form-group">
                                <label>Language (Searchable)</label>
                                <select name="message_language" class="language-select" required>
                                    <option value="">Search Language...</option>
                                    <optgroup label="Common">
                                        <option value="English">English</option>
                                        <option value="Telugu">Telugu</option>
                                        <option value="Hindi">Hindi</option>
                                        <option value="French">French</option>
                                        <option value="Spanish">Spanish</option>
                                    </optgroup>
                                    <optgroup label="All Languages">
                                        <option value="Afrikaans">Afrikaans</option>
                                        <option value="Amharic">Amharic</option>
                                        <option value="Arabic">Arabic</option>
                                        <option value="Bengali">Bengali</option>
                                        <option value="Chinese">Chinese (Mandarin)</option>
                                        <option value="Dutch">Dutch</option>
                                        <option value="German">German</option>
                                        <option value="Greek">Greek</option>
                                        <option value="Gujarati">Gujarati</option>
                                        <option value="Hausa">Hausa</option>
                                        <option value="Hebrew">Hebrew</option>
                                        <option value="Igbo">Igbo</option>
                                        <option value="Indonesian">Indonesian</option>
                                        <option value="Italian">Italian</option>
                                        <option value="Japanese">Japanese</option>
                                        <option value="Kannada">Kannada</option>
                                        <option value="Korean">Korean</option>
                                        <option value="Malay">Malay</option>
                                        <option value="Malayalam">Malayalam</option>
                                        <option value="Marathi">Marathi</option>
                                        <option value="Odiya">Odiya</option>
                                        <option value="Oromo">Oromo</option>
                                        <option value="Persian">Persian (Farsi)</option>
                                        <option value="Portuguese">Portuguese</option>
                                        <option value="Punjabi">Punjabi</option>
                                        <option value="Russian">Russian</option>
                                        <option value="Shona">Shona</option>
                                        <option value="Swahili">Swahili</option>
                                        <option value="Tamil">Tamil</option>
                                        <option value="Thai">Thai</option>
                                        <option value="Turkish">Turkish</option>
                                        <option value="Urdu">Urdu</option>
                                        <option value="Vietnamese">Vietnamese</option>
                                        <option value="Yoruba">Yoruba</option>
                                        <option value="Zulu">Zulu</option>
                                        <option value="Other">Other</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>File (PDF/DOCX)</label>
                                <input type="file" name="message_book[]" accept=".pdf,.docx,.txt" required>
                            </div>
                        </div>
                        <button type="submit" name="submit_sermon" class="submit-btn">Submit Contribution</button>
                    </form>
                </div>
            </div>

            <div class="info-sidebar">
                <div class="card app-section">
                    <p class="small fw-bold mb-3">Get Mobile App</p>
                    <div class="app-badges">
                        <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="iOS"></a>
                        <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Android"></a>
                    </div>
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    <a href="#" style="color: var(--primary); font-size: 0.85rem; font-weight: bold; text-decoration: none;"><i class="fas fa-question-circle"></i> Request Message Book</a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.language-select').select2({
                placeholder: "Type to search language...",
                width: '100%'
            });
        });
    </script>
</body>
</html>
