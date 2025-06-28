<?php
session_start();
include '../config.php';

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit();
}

$order_id = $_GET['id'];

// Start transaction
$conn->begin_transaction();

try {
    // Delete testimonials first
    $delete_testimonials = "DELETE FROM testimonials WHERE order_id = ?";
    $stmt = $conn->prepare($delete_testimonials);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Delete order status history
    $delete_status = "DELETE FROM order_status WHERE order_id = ?";
    $stmt = $conn->prepare($delete_status);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Delete order items
    $delete_items = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($delete_items);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Finally delete the order
    $delete_order = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_order);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $conn->commit();
    $_SESSION['message'] = "Order deleted successfully";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Failed to delete order: " . $e->getMessage();
}

header("Location: home_admin.php");
exit();
?>