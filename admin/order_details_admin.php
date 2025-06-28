<?php
session_start();
include '../config.php';

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit();
}

$order_id = $_GET['id'];

// Get order details
$order_query = "SELECT o.*, os.status 
                FROM orders o
                LEFT JOIN (
                    SELECT order_id, status 
                    FROM order_status 
                    WHERE (order_id, created_at) IN (
                        SELECT order_id, MAX(created_at) 
                        FROM order_status 
                        GROUP BY order_id
                    )
                ) os ON o.id = os.order_id
                WHERE o.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Get order items
$items_query = "SELECT oi.*, p.namaProduct, p.image 
                FROM order_items oi
                JOIN product p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

// Get status history
$status_query = "SELECT status, created_at FROM order_status WHERE order_id = ? ORDER BY created_at";
$stmt = $conn->prepare($status_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$status_history = $stmt->get_result();

// MODIFIED: Get testimonials - adjusted query to match actual table structure
$testimonials_query = "SELECT t.*, p.namaProduct 
                       FROM testimonials t
                       JOIN product p ON t.product_id = p.id
                       WHERE t.order_id = ?";
$stmt = $conn->prepare($testimonials_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$testimonials = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.875em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Order #<?= htmlspecialchars($order['id']) ?></h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name']) ?> <?= htmlspecialchars($order['last_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['postcode']) ?></p>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $items->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['namaProduct']) ?></td>
                                        <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td>Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge 
                                <?= $order['status'] == 'completed' ? 'bg-success' : 
                                   ($order['status'] == 'delivered' ? 'bg-primary' : 
                                   ($order['status'] == 'shipped' ? 'bg-info' : 
                                   ($order['status'] == 'processing' ? 'bg-warning' : 'bg-secondary'))) ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </p>
                        <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                        <p><strong>Shipping Method:</strong> <?= $order['shipping_method'] == 'flat' ? 'Flat Rate (Rp15,000)' : 'Free Shipping' ?></p>
                        <hr>
                        <p><strong>Subtotal:</strong> Rp<?= number_format($order['total_amount'] - ($order['shipping_method'] == 'flat' ? 15000 : 0), 0, ',', '.') ?></p>
                        <p><strong>Shipping:</strong> Rp<?= $order['shipping_method'] == 'flat' ? '15,000' : '0' ?></p>
                        <p><strong>Total:</strong> Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></p>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5>Status History</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($status = $status_history->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span><?= ucfirst($status['status']) ?></span>
                                        <small class="text-muted"><?= date('M j, Y g:i A', strtotime($status['created_at'])) ?></small>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                
                <?php if ($testimonials->num_rows > 0): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h5>Customer Testimonials</h5>
                    </div>
                    <div class="card-body">
                        <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
                            <div class="mb-3 p-3 border rounded bg-white">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong><?= isset($testimonial['first_name']) ? htmlspecialchars($testimonial['first_name']) : 'Anonymous' ?></strong>
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $testimonial['rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="mb-1"><em><?= htmlspecialchars($testimonial['namaProduct']) ?></em></p>
                                <p><?= htmlspecialchars($testimonial['comment']) ?></p>
                                <small class="text-muted"><?= date('M j, Y', strtotime($testimonial['created_at'])) ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <a href="home_admin.php" class="btn btn-secondary mt-3 mb-5">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>