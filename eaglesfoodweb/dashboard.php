<?php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login_page.php");
    exit;
}

// Fetch details from session (Populated by the new login logic above)
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #764ba2;
            --bg: #f8f9fc;
            --white: #ffffff;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --danger: #ef233c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body { background-color: var(--bg); color: var(--text-dark); display: flex; min-height: 100vh; overflow-x: hidden; }

        /* --- Sidebar --- */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #4361ee 0%, #764ba2 100%);
            color: white; padding: 30px 20px;
            position: fixed; height: 100vh; z-index: 1000;
        }

        .sidebar h2 { font-size: 1.2rem; margin-bottom: 40px; text-align: center; font-weight: 800; }

        .nav-link {
            display: flex; align-items: center; color: rgba(255,255,255,0.8);
            text-decoration: none; padding: 12px 15px; border-radius: 8px;
            margin-bottom: 10px; transition: 0.3s;
        }

        .nav-link i { margin-right: 15px; width: 20px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.2); color: white; }

        /* --- Main Content --- */
        .main-content { flex: 1; margin-left: 260px; padding: 30px; width: 100%; }

        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .welcome-msg h1 { font-size: 1.5rem; }

        /* --- Stats Grid --- */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white); padding: 20px; border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; align-items: center;
        }

        .stat-icon {
            width: 45px; height: 45px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-right: 15px;
        }

        .blue { background: #e0e7ff; color: #4361ee; }
        .purple { background: #f3e8ff; color: #764ba2; }
        .pink { background: #ffe4e6; color: #ef233c; }

        .stat-info h3 { font-size: 1.1rem; }
        .stat-info span { color: var(--text-light); font-size: 0.8rem; }

        /* --- Profile Card --- */
        .profile-container {
            background: var(--white); padding: 25px; border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .profile-header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }

        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f9f9f9; flex-wrap: wrap; }
        .info-label { width: 140px; font-weight: 600; color: var(--text-light); font-size: 0.9rem; }
        .info-value { flex: 1; font-size: 0.9rem; min-width: 150px; }

        .logout-btn { background: var(--danger); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; }

        /* --- RESPONSIVE BREAKPOINTS --- */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%; height: 65px; bottom: 0; top: auto;
                display: flex; justify-content: space-around; padding: 0;
            }
            .sidebar h2 { display: none; }
            .sidebar nav { display: flex; width: 100%; justify-content: space-around; }
            .nav-link { flex-direction: column; padding: 5px; margin: 0; font-size: 0.7rem; border-radius: 0; width: 25%; }
            .nav-link i { margin-right: 0; margin-bottom: 4px; font-size: 1.2rem; }
            .nav-link span { display: block; }
            
            .main-content { margin-left: 0; padding: 20px; padding-bottom: 100px; }
            .top-bar .logout-btn { display: none; } /* Use bottom nav logout */
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2>EAGLES FOOD</h2>
        <nav>
            <a href="#" class="nav-link active"><i class="fas fa-home"></i> <span>Home</span></a>
            <a href="#" class="nav-link"><i class="fas fa-cloud-upload-alt"></i> <span>Uploads</span></a>
            <a href="#" class="nav-link"><i class="fas fa-user"></i> <span>Profile</span></a>
            <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="welcome-msg">
                <h1>Hi, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p>Welcome back to your dashboard.</p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($upload_count); ?></h3>
                    <span>Total Uploads</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-user-tie"></i></div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($pastor_name); ?></h3>
                    <span>Pastor</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pink"><i class="fas fa-phone"></i></div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($phone); ?></h3>
                    <span>Contact</span>
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <h2>Account Details</h2>
                <i class="fas fa-user-circle" style="font-size: 1.5rem; color: var(--primary);"></i>
            </div>
            
            <div class="info-row">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?php echo htmlspecialchars($user_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Pastor Name</div>
                <div class="info-value"><?php echo htmlspecialchars($pastor_name); ?></div>
            </div>
        </div>
    </main>

</body>
</html>
