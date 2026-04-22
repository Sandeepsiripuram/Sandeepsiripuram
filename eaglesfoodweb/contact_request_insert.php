<?php
include 'db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    
    // 1. Validate Email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address'); window.history.back();</script>";
        exit();
    }

    // 2. Handle Optional Phone Number
    $phone = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
    if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
        echo "<script>alert('Please enter a valid phone number (10-15 digits)'); window.history.back();</script>";
        exit();
    }

    // 3. Prepare and Bind
    // Note: Ensure your database column is named 'Message' (capital M) or 'message' (lowercase)
    $stmt = $conn->prepare("INSERT INTO contact_request (fullname, email, phone_number, subject, Message) VALUES (?, ?, ?, ?, ?)");
    
    // Assigning variables to ensure they match the bind_param
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt->bind_param("sssss", $fullname, $email, $phone, $subject, $message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href='contact_us.php';</script>";
    } else {
        // This will tell you exactly why the database rejected it (e.g., column name typo)
        echo "<script>alert('Database Error: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }
    
    $stmt->close();
}
?>
