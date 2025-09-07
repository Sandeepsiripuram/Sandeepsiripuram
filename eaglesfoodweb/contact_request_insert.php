<?php
include 'db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    
    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address');</script>";
        exit();
    }

    // Validate phone number (if provided)
    if (!empty($_POST['phone_number']) && !preg_match('/^[0-9]{10,15}$/', $_POST['phone_number'])) {
        echo "<script>alert('Please enter a valid phone number');</script>";
        exit();
    }

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO contact_request (`fullname`, `email`, `phone_number`, `subject`, `Message`) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        exit();
    }
    
    $stmt->bind_param("sssss", $_POST['fullname'], $_POST['email'], $_POST['phone_number'], $_POST['subject'], $_POST['Message']);
    
    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href='contact_us.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close(); // Close the connection
}
?>