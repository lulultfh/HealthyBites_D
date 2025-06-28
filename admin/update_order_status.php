<?php
session_start();
include '../config.php';

// Periksa session admin lebih ketat
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Simpan URL yang diminta
    header("Location: login_admin.php");
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    die("Invalid Order ID");
}

// Dapatkan status terkini dengan query yang lebih aman
$status_query = "SELECT status FROM order_status WHERE order_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($status_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$current_status = $result->num_rows > 0 ? $result->fetch_assoc()['status'] : 'processing';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    
    // Validasi status
    $allowed_statuses = ['processing', 'shipped', 'delivered', 'completed'];
    if (!in_array($new_status, $allowed_statuses)) {
        die("Invalid status");
    }

    // Tambahkan status baru
    $update_query = "INSERT INTO order_status (order_id, status) VALUES (?, ?)";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("is", $order_id, $new_status);
    
    if ($stmt->execute()) {
        $_SESSION['status_update_success'] = true;
        header("Location: order_details_admin.php?id=$order_id");
        exit();
    } else {
        die("Error updating status");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Update Status Pesanan #<?= $order_id ?></h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status Saat Ini:</label>
                        <div class="alert alert-info">
                            <?= ucfirst($current_status) ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">Pilih Status Baru:</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="processing" <?= $current_status == 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $current_status == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $current_status == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="completed" <?= $current_status == 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="order_details_admin.php?id=<?= $order_id ?>" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>