<?php
session_start();
include "db.php"; // Ensure $conn is defined here

// 1. HANDLE LOGIN LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    // Select all necessary columns for the session
    $stmt = $conn->prepare("SELECT user_id, user_name, user_password, pastor_name, phone, upload_count FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // password_verify compares raw input with the $2y$... hash in DB
        if (password_verify($password, $user['user_password'])) {
            
            // Store EVERYTHING the dashboard needs in the Session
            $_SESSION['loggedin']     = true;
            $_SESSION['user_id']      = $user['user_id'];
            $_SESSION['user_name']    = $user['user_name'];
            $_SESSION['email']        = $email;
            $_SESSION['pastor_name']  = $user['pastor_name'] ?? 'Not Assigned';
            $_SESSION['phone']        = $user['phone'] ?? 'Not Provided';
            $_SESSION['upload_count'] = $user['upload_count'] ?? 0;

            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not registered.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eagles Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4361ee; --bg: #f0f2f5; --glass: rgba(255, 255, 255, 0.95); }
        body { 
            margin: 0; font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(45deg, #4361ee, #7209b7);
            height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .login-card {
            background: var(--glass); backdrop-filter: blur(10px);
            width: 100%; max-width: 400px; padding: 35px;
            border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .logo-box img { width: 80px; margin-bottom: 15px; border-radius: 50%; }
        .form-group { text-align: left; margin-bottom: 20px; position: relative; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 0.9rem; }
        .input-icon { position: relative; }
        .input-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #4361ee; }
        input {
            width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #eee;
            border-radius: 12px; box-sizing: border-box; font-size: 1rem; outline: none; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); background: #fff; }
        .btn-primary {
            width: 100%; padding: 14px; background: var(--primary); border: none;
            color: white; border-radius: 12px; font-weight: 700; cursor: pointer;
            transition: 0.3s; font-size: 1rem; margin-top: 10px;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-primary:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        .switch-link { margin-top: 25px; font-size: 0.9rem; color: #666; }
        .switch-link a { color: var(--primary); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-box">
            <img src="images/1963_cloud.png" alt="Logo" onerror="this.src='https://via.placeholder.com/80'">
            <h2 style="margin: 10px 0; color: #2d3436;">Welcome Back</h2>
            <p style="color: #636e72; margin-bottom: 25px;">Login to your ministry account</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="name@domain.com" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" id="loginBtn">Sign In</button>
        </form>

        <div class="switch-link">
            Don't have an account? <a href="registration.php">Register here</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').onsubmit = async (e) => {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerText;
            
            btn.disabled = true;
            btn.innerText = "Verifying...";
            
            try {
                // Fetch sends to the SAME file ('')
                const res = await fetch('', { 
                    method: 'POST', 
                    body: new FormData(e.target) 
                });
                
                const data = await res.json();
                
                if(data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            } catch (error) {
                alert("Connection error. Please check your internet.");
                btn.disabled = false;
                btn.innerText = originalText;
            }
        };
    </script>
</body>
</html>
