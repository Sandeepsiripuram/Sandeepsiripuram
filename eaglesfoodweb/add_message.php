<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Contribute Sermon - Eagles Food</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: rgb(185, 218, 228);
      margin: 0;
      padding: 0;
    }

    header {
      background: #ffffff;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-img {
      height: 50px;
    }

    .logo-text {
      font-size: 20px;
      font-weight: bold;
    }

    .menu a {
      margin-left: 15px;
      color: #1976d2;
      font-weight: 500;
      text-decoration: none;
    }

    .container-custom {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 20px;
      max-width: 1200px;
      margin: auto;
    }

    .upload-form,
    .side-panel {
      flex: 1 1 100%;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
      display: block;
    }

    input[type="text"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .submit-btn {
      background: #007bff;
      color: white;
      padding: 12px;
      border: none;
      width: 100%;
      font-size: 16px;
      border-radius: 5px;
    }

    .submit-btn:hover {
      background: #0056b3;
    }

    .alert {
      color: red;
      font-weight: bold;
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

        .menu a:hover, .menu a.active {
            background-color:rgb(78, 156, 234);
            color: white;
        }

     .input-with-icon {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #777;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 5px; /* Add padding to make it more clickable */
  z-index: 1;
}
.toggle-password i {
  pointer-events: none;
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

    @media (min-width: 768px) {
      .upload-form {
        flex: 1 1 60%;
      }

      .side-panel {
        flex: 1 1 35%;
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
             <a href="seals.php" target="_blank"><i class="fas fa-scroll"></i> Seven Seals</a>
            <a href="church_ages.php" target="_blank"><i class="fas fa-church"></i> Seven Church Ages</a>
            <a href="teachings_list.php"><i class="fas fa-book"></i>Teachings</a>
            <a href="contact_us.php"><i class="fas fa-envelope"></i> Contact Us</a>
        </div>
    </header>

  <div class="container-custom">
    <div class="upload-form">
      <h4 class="text-center mb-4">Contribute a Sermon</h4>
     <form id="sermonForm" action="webin/upload_sermon.php" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label for="yourName">Your Name</label>
    <input type="text" id="yourName" name="believer_name" required />
  </div>

  <div class="form-group">
    <label for="language">Select Language</label>
    <select id="language" name="message_language" required>
      <option value="">-- Select Language --</option>
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
    <input type="text" id="sermonTitle" name="message_title" required />
    <div id="duplicateAlert" class="alert" style="display:none;">
      This sermon title already exists.
    </div>
  </div>

  <div class="form-group">
    <label for="uploadFile">Upload Sermon File(s)</label>
    <input type="file" id="uploadFile" name="message_book[]" multiple required accept=".pdf,.doc,.docx,.txt,.odt,.rtf,.html,.xml" />
  </div>

  <button type="submit" name="submit" class="submit-btn">Submit</button>
</form>
    </div>

    <div class="side-panel">
          <div class="card mt-3">
            <div class="card-body text-center">
      <h5 style="font-weight:bold;">Sermon Submission Help</h5>
      <p>If your sermon is not found in the list, you can upload it here.</p>
      <p>Only text document formats (PDF, DOCX, TXT,HTML,XML etc.) are allowed. Image files will be rejected.</p>
      <p>Please make sure the title is not already submitted.</p>
</div>
          </div>
       <div class="card mt-3">
            <div class="card-body text-center">
              <h6 style="font-weight:bold;">Eagles Food-Mobile App</h6>
              <p>Read, search and study the Messages on your mobile device.</p>
              <img src="https://upload.wikimedia.org/wikipedia/commons/6/67/App_Store_%28iOS%29.svg" width="100" alt="App Store">
              <br><br>
              <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" width="120" alt="Google Play">
            </div>
          </div>
    </div>
  </div>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Duplicate title check via AJAX
  $('#sermonTitle').on('blur', function () {
    const title = $(this).val().trim();
    if (title.length > 0) {
      $.post('check_sermon_title.php', { title }, function (data) {
        if (data.exists) {
          $('#duplicateAlert').show();
        } else {
          $('#duplicateAlert').hide();
        }
      }, 'json');
    }
  });
</script>

</body>

</html>
