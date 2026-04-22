<?php
session_start();
header('Content-Type: application/json');
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT user_id, user_name, user_password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Match raw password with hashed DB password
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            
            echo json_encode(['success' => true, 'redirect' => 'index.php']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not registered.']);
    }
    exit;
}
?>
