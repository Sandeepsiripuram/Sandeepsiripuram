<?php
session_start();
include "db.php";

if (isset($_POST['submit'])) {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $pastor = mysqli_real_escape_string($conn, $_POST['pastor_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = $_POST['gender'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Secure Password Hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if user exists using Prepared Statement
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "This email is already registered!";
    } else {
        // Insert into database matching your specific columns
        $sql = "INSERT INTO users (user_name, pastor_name, gender, full_address, phone, email, user_password) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert = $conn->prepare($sql);
        $insert->bind_param("sssssss", $name, $pastor, $gender, $address, $phone, $email, $password);
        
        if ($insert->execute()) {
            header("Location: login_page.php?success=registered");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Eagles Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4361ee; --secondary: #7209b7; --dark: #2b2d42; }
        body { 
            margin: 0; font-family: 'Segoe UI', sans-serif; background: #f4f7fe;
            display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 15px;
        }
        .reg-container {
            background: white; display: flex; width: 100%; max-width: 900px; 
            border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .info-side {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            width: 35%; padding: 40px; color: white; display: flex; flex-direction: column; justify-content: center; text-align: center;
        }
        .form-side { width: 65%; padding: 40px; background: #fff; }
        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full { grid-column: span 2; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 0.85rem; font-weight: 700; color: var(--dark); margin-bottom: 5px; }
        input, select, textarea {
            padding: 10px 15px; border: 2px solid #edf2f7; border-radius: 10px; font-size: 0.95rem; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); outline: none; }
        .btn-reg {
            grid-column: span 2; padding: 14px; background: var(--primary);
            color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;
            font-size: 1rem; transition: 0.3s; margin-top: 10px;
        }
        .btn-reg:hover { background: var(--secondary); transform: translateY(-2px); }
        
        @media (max-width: 768px) {
            .reg-container { flex-direction: column; }
            .info-side { width: 100%; padding: 25px; }
            .form-side { width: 100%; padding: 25px; }
            .grid-form { grid-template-columns: 1fr; }
            .full, .btn-reg { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <div class="reg-container">
        <div class="info-side">
            <img src="images/appicon.png" style="width: 80px; margin: 0 auto 15px;">
            <h2>Eagles Food</h2>
            <p>Join our community and track your spiritual growth.</p>
        </div>
        <div class="form-side">
            <h2 style="margin-top: 0; color: var(--primary);">Create Account</h2>
            <?php if(isset($error)): ?>
                <p style="color:#ef233c; background:#ffeef0; padding:10px; border-radius:8px; font-size:0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </p>
            <?php endif; ?>
            <form method="POST" class="grid-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Pastor's Name</label>
                    <input type="text" name="pastor_name" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group full">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group full">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group full">
                    <label>Full Address</label>
                    <textarea name="address" rows="2"></textarea>
                </div>
                <button type="submit" name="submit" class="btn-reg">Sign Up</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                Already a member? <a href="login_page.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
