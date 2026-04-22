<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contribute Sermon - Eagles Food</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

  <style>
    :root {
      --primary-blue: #1976d2;
      --hover-blue: #1565c0;
      --bg-light: #b9dae4;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg-light);
      margin: 0;
      padding: 0;
      color: #333;
    }

    /* --- Header & Navigation --- */
    header {
      background: #ffffff;
      padding: 10px 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-img {
      height: 45px;
      width: auto;
    }

    .logo-text {
      font-size: 1.25rem;
      font-weight: 700;
      color: #222;
    }

    .menu {
      display: flex;
      gap: 5px;
    }

    .menu a {
      text-decoration: none;
      color: #444;
      padding: 8px 15px;
      border-radius: 5px;
      font-weight: 500;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .menu a:hover, .menu a.active {
      background-color: var(--primary-blue);
      color: white !important;
    }

    .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
    }

    /* --- Layout Container --- */
    .container-custom {
      display: flex;
      flex-direction: column;
      gap: 20px;
      padding: 30px 5%;
      max-width: 1200px;
      margin: auto;
    }

    .upload-form, .side-panel {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    /* --- Form Elements --- */
    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
      font-size: 0.95rem;
    }

    input[type="text"], select, input[type="file"] {
      width: 100%;
      padding: 12px;
      border-radius: 6px;
      border: 1px solid #ddd;
      transition: border-color 0.3s;
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
    }

    .submit-btn {
      background: var(--primary-blue);
      color: white;
      padding: 14px;
      border: none;
      width: 100%;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 10px;
    }

    .submit-btn:hover {
      background: var(--hover-blue);
    }

    .alert {
      color: #dc3545;
      font-size: 0.85rem;
      margin-top: 5px;
      font-weight: 500;
    }

    /* --- Side Panel --- */
    .card {
      border: none;
      background: #f8f9fa;
      border-left: 4px solid var(--primary-blue);
    }

    .app-badge {
      transition: transform 0.2s;
    }

    .app-badge:hover {
      transform: scale(1.05);
    }

    /* --- Responsive Queries --- */
    @media (max-width: 992px) {
      .menu-toggle {
        display: block;
      }

      .menu {
        display: none; /* Hidden by default on mobile */
        flex-direction: column;
        width: 100%;
        position: absolute;
        top: 70px;
        left: 0;
        background: white;
        padding: 20px;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
      }

      .menu.active {
        display: flex;
      }

      .menu a {
        width: 100%;
      }
    }

    @media (min-width: 768px) {
      .container-custom {
        flex-direction: row;
        align-items: flex-start;
      }

      .upload-form {
        flex: 2;
      }

      .side-panel {
        flex: 1;
        position: sticky;
        top: 100px;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">
      <img src="images/appicon.png" alt="Eagle's Food Logo" class="logo-img">
      <span class="logo-text">Eagles Food</span>
    </div>

    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
      <i class="fas fa-bars"></i>
    </button>

    <nav class="menu" id="mainMenu">
      <a href="index.php" class="active"><i class="fas fa-house"></i> Home</a>
      <a href="seals.php" target="_blank"><i class="fas fa-scroll"></i> Seven Seals</a>
      <a href="church_ages.php" target="_blank"><i class="fas fa-church"></i> Seven Church Ages</a>
      <a href="teachings_list.php"><i class="fas fa-book"></i> Teachings</a>
      <a href="contact_us.php"><i class="fas fa-envelope"></i> Contact Us</a>
    </nav>
  </header>

  <main class="container-custom">
    <section class="upload-form">
      <h4 class="mb-4 text-primary"><i class="fas fa-cloud-upload-alt me-2"></i>Contribute a Sermon</h4>
      <form id="sermonForm" action="webin/upload_sermon.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="yourName">Your Name</label>
          <input type="text" id="yourName" name="believer_name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
          <label for="language">Select Language</label>
          <select id="language" name="message_language" required>
            <option value="" disabled selected>-- Select Language --</option>
            <option value="english">English</option>
            <option value="telugu">Telugu</option>
            <option value="hindi">Hindi</option>
            <option value="tamil">Tamil</option>
            <option value="french">French</option>
            <option value="spanish">Spanish</option>
          </select>
        </div>

        <div class="form-group">
          <label for="sermonTitle">Sermon Title</label>
          <input type="text" id="sermonTitle" name="message_title" placeholder="e.g. The Deep Calleth To The Deep" required>
          <div id="duplicateAlert" class="alert" style="display:none;">
            <i class="fas fa-exclamation-circle"></i> This sermon title already exists in our database.
          </div>
        </div>

        <div class="form-group">
          <label for="uploadFile">Upload Sermon File(s)</label>
          <input type="file" id="uploadFile" name="message_book[]" multiple required accept=".pdf,.doc,.docx,.txt,.odt,.rtf,.html,.xml">
          <small class="text-muted mt-2 d-block">Supported: PDF, DOCX, TXT, HTML (Max 10MB)</small>
        </div>

        <button type="submit" name="submit" class="submit-btn">Submit Sermon</button>
      </form>
    </section>

    <aside class="side-panel">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Submission Help</h6>
          <p class="small mb-1">Upload missing sermons here.</p>
          <p class="small mb-1"><strong>Text only:</strong> PDF, DOCX, TXT, HTML.</p>
          <p class="small text-danger">Images will be rejected.</p>
        </div>
      </div>

      <div class="card">
        <div class="card-body text-center">
          <h6 class="fw-bold mb-3">Eagles Food Mobile App</h6>
          <p class="small text-muted">Study messages on the go.</p>
          <div class="d-flex flex-column gap-2 align-items-center">
            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/6/67/App_Store_%28iOS%29.svg" width="110" alt="App Store" class="app-badge"></a>
            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" width="130" alt="Google Play" class="app-badge"></a>
          </div>
        </div>
      </div>
    </aside>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Mobile Menu Toggle
      $('#menuToggle').on('click', function() {
        $('#mainMenu').toggleClass('active');
        $(this).find('i').toggleClass('fa-bars fa-times');
      });

      // Duplicate title check via AJAX
      $('#sermonTitle').on('blur', function() {
        const title = $(this).val().trim();
        if (title.length > 0) {
          $.post('check_sermon_title.php', { title }, function(data) {
            if (data.exists) {
              $('#duplicateAlert').fadeIn();
            } else {
              $('#duplicateAlert').fadeOut();
            }
          }, 'json');
        }
      });
    });
  </script>
</body>
</html>
