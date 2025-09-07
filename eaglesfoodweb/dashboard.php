<?php
session_start();
include "db.php";

// Check login
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Get user info if logged in
if ($logged_in) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $email = $_SESSION['email'];
    $pastor_name = $_SESSION['pastor_name'];
    $phone = $_SESSION['phone'];
    $upload_count = $_SESSION['upload_count'];
    
    // Fetch only the current user's uploads
    $stmt = $conn->prepare("SELECT * FROM books_added WHERE user_id = ? ORDER BY upload_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $user_id = 0; // Casual User
    $user_name = "Guest";
    $email = "Not Available";
    $pastor_name = "Not Available";
    $phone = "Not Available";
    $upload_count = 0;
    
    // Fetch all uploads for guests
    $result = $conn->query("SELECT * FROM books_added ORDER BY upload_date DESC");
}

// Process uploads
$uploads = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $uploads[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Eagles Food</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    
    .dashboard-header {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 1rem 0;
      margin-bottom: 2rem;
    }
    
    .card {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      margin-bottom: 1.5rem;
    }
    
    .card-header {
      background-color: rgba(102, 126, 234, 0.1);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      font-weight: 600;
    }
    
    .profile-icon {
      font-size: 3rem;
      color: #667eea;
      margin-bottom: 1rem;
    }
    
    .logout-btn {
      background: #f72585;
      border: none;
    }
    
    .logout-btn:hover {
      background: #e91e63;
    }
    
    .table th {
      background-color: #667eea;
      color: white;
    }
    
    .profile-icon-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
    }
    
    .profile-icon-link {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .profile-icon-link:hover {
      transform: scale(1.1);
      color: white;
    }
    
    @media (max-width: 768px) {
      .container-custom {
        padding: 0 15px;
      }
      
      .card {
        margin-bottom: 1rem;
      }
      
      .profile-icon-container {
        bottom: 70px;
        right: 15px;
      }
      
      .profile-icon-link {
        width: 50px;
        height: 50px;
      }
    }
  </style>
</head>
<body>
  <!-- Profile Icon (shown only when logged in) -->
  <?php if ($logged_in): ?>
  <div class="profile-icon-container">
    <a href="dashboard.php" class="profile-icon-link" title="Go to Dashboard">
      <i class="fas fa-user"></i>
    </a>
  </div>
  <?php endif; ?>

  <header class="dashboard-header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <img src="images/appicon.png" alt="Eagle's Food Logo" height="40" class="me-2" />
          <h1 class="h4 mb-0">Eagles Food Dashboard</h1>
        </div>
        <a href="mainpage.php" target="_blank" class="btn btn-light btn-sm">
          <i class="fas fa-home me-1"></i> Home
        </a>
      </div>
    </div>
  </header>

  <div class="container container-custom">
    <div class="row">
      <!-- Profile Info -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="profile-icon">
              <i class="fas fa-user-circle"></i>
            </div>
            <h5 class="card-title"><?php echo htmlspecialchars($user_name); ?></h5>
            <p class="card-text">
              <small class="text-muted">
                <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($email); ?><br>
                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($pastor_name); ?><br>
                <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($phone); ?><br>
                <i class="fas fa-upload me-1"></i> Uploads: <?php echo htmlspecialchars($upload_count); ?>
              </small>
            </p>
            <?php if ($logged_in): ?>
              <a href="logout.php" class="btn btn-danger logout-btn w-100">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
              </a>
            <?php else: ?>
              <a href="login_page.php" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-1"></i> Login
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Upload Form -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-transparent">
            <h5 class="mb-0">Contribute a Sermon</h5>
          </div>
          <div class="card-body">
            <form id="sermonForm" action="webin/upload_sermon.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

              <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="believer_name" class="form-control" 
                  value="<?php echo $logged_in ? htmlspecialchars($user_name) : ''; ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Language</label>
                <select name="message_language" class="form-select" required>
                  <option value="">-- Select Language --</option>
                  <option value="english">English</option>
                  <option value="telugu">Telugu</option>
                  <option value="hindi">Hindi</option>
                  <option value="tamil">Tamil</option>
                  <option value="french">French</option>
                  <option value="spanish">Spanish</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Sermon Title</label>
                <input type="text" name="message_title" id="sermonTitle" class="form-control" required>
                <div id="duplicateAlert" class="alert alert-warning mt-2" style="display:none;" role="alert">
                  <i class="fas fa-exclamation-triangle me-1"></i> This sermon title already exists.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Upload File(s)</label>
                <input type="file" name="message_book[]" class="form-control" multiple required
                  accept=".pdf,.doc,.docx,.txt,.odt,.rtf,.html,.xml">
                <div class="form-text">Accepted formats: PDF, DOC, DOCX, TXT, ODT, RTF, HTML, XML</div>
              </div>

              <button type="submit" class="btn btn-success">
                <i class="fas fa-upload me-1"></i> Submit
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Uploaded Table -->
    <div class="card mt-4">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo $logged_in ? 'Your Uploaded Sermons' : 'All Uploaded Sermons'; ?></h5>
        <input type="text" id="searchBox" class="form-control form-control-sm w-25" placeholder="Search sermons...">
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-striped align-middle mb-0" id="uploadsTable">
            <thead>
              <tr>
                <th>#</th>
                <th>Believer</th>
                <th>Language</th>
                <th>Title</th>
                <th>File</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($uploads) > 0): ?>
                <?php foreach ($uploads as $i => $up): ?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo htmlspecialchars($up['believer_name']); ?></td>
                    <td>
                      <span class="badge bg-primary">
                        <?php echo htmlspecialchars($up['message_language']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($up['message_title']); ?></td>
                    <td>
                      <a href="uploads/books/<?php echo htmlspecialchars($up['message_book']); ?>" 
                         target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View
                      </a>
                    </td>
                    <td>
                      <small><?php echo htmlspecialchars($up['upload_date']); ?></small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <i class="fas fa-inbox fs-1 text-muted d-block mb-2"></i>
                    <span class="text-muted">No uploads yet.</span>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav class="d-flex justify-content-center mt-3">
          <ul class="pagination" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

    // Pagination + Search
    const rowsPerPage = 5;
    let currentPage = 1;

    function paginateTable() {
      const rows = $('#uploadsTable tbody tr:visible');
      const totalRows = rows.length;
      const totalPages = Math.ceil(totalRows / rowsPerPage);

      // Hide all
      rows.hide();

      // Show selected page
      const start = (currentPage - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      rows.slice(start, end).show();

      // Render pagination
      let pagHTML = '';
      
      // Previous button
      pagHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                  <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>`;
      
      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        pagHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                  </li>`;
      }
      
      // Next button
      pagHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                  <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>`;
                
      $('#pagination').html(pagHTML);
    }

    // Handle pagination click
    $(document).on('click', '#pagination .page-link', function(e) {
      e.preventDefault();
      currentPage = parseInt($(this).data('page'));
      paginateTable();
    });

    // Search filter
    $('#searchBox').on('keyup', function() {
      const value = $(this).val().toLowerCase();
      $('#uploadsTable tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
      currentPage = 1;
      paginateTable();
    });

    // Init
    $(document).ready(function() {
      paginateTable();
    });
  </script>
</body>
</html>