<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete testimonial
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Testimonial deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete testimonial";
    }
    
    $stmt->close();
}

header("Location: home_admin.php#testimonial");
exit();
?>