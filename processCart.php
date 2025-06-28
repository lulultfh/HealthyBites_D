<?php
include 'config.php';

if (isset($_GET['add'])) {
    // Tambah produk ke cart
    $product_id = $_GET['add'];
    
    // Ambil data produk dari database
    $product_query = "SELECT * FROM products WHERE id = $product_id";
    $product_result = $conn->query($product_query);
    $product = $product_result->fetch_assoc();
    
    // Cek apakah produk sudah ada di cart
    $check_query = "SELECT * FROM cart WHERE product_id = $product_id";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Update quantity jika produk sudah ada
        $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE product_id = $product_id";
        $conn->query($update_query);
    } else {
        // Tambahkan produk baru ke cart
        $insert_query = "INSERT INTO cart (product_id, product_name, price, quantity, image) 
                         VALUES ('$product_id', '".$product['name']."', '".$product['price']."', 1, '".$product['image']."')";
        $conn->query($insert_query);
    }
    
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    
    if ($action == 'increase') {
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE id = $id");
    } elseif ($action == 'decrease') {
        // Cek quantity saat ini
        $current = $conn->query("SELECT quantity FROM cart WHERE id = $id")->fetch_assoc();
        if ($current['quantity'] > 1) {
            $conn->query("UPDATE cart SET quantity = quantity - 1 WHERE id = $id");
        } else {
            $conn->query("DELETE FROM cart WHERE id = $id");
        }
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM cart WHERE id = $id");
    }
    
    header("Location: cart.php");
    exit();
}
?>