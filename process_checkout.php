<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $notes = $_POST['notes'] ?? '';
    $payment_method = $_POST['payment'];
    $shipping_method = $_POST['shipping'];
    
    // Calculate shipping cost
    $shipping_cost = ($shipping_method == 'flat') ? 15000 : 0;
    
    // Get cart items and calculate total
    $cart_query = "SELECT p.id, p.harga, c.quantity 
                   FROM cart c 
                   JOIN product p ON c.product_id = p.id 
                   WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();
    
    $total = 0;
    while ($item = $cart_items->fetch_assoc()) {
        $total += $item['harga'] * $item['quantity'];
    }
    $total += $shipping_cost;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order
        $order_query = "INSERT INTO orders (user_id, first_name, last_name, address, city, postcode, phone, email, notes, payment_method, shipping_method, total_amount)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("issssssssssd", $user_id, $first_name, $last_name, $address, $city, $postcode, $phone, $email, $notes, $payment_method, $shipping_method, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Insert order items
        $cart_items->data_seek(0); // Reset pointer
        while ($item = $cart_items->fetch_assoc()) {
            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                           VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['harga']);
            $stmt->execute();
        }
        
        // Set initial order status
        $status_query = "INSERT INTO order_status (order_id, status) VALUES (?, 'pending')";
        $stmt = $conn->prepare($status_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($clear_cart);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Update session cart count
        $_SESSION['cart_count'] = 0;
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
} else {
    header("Location: checkout.php");
    exit();
}
?>