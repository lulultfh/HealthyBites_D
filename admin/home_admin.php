<?php
session_start();
include '../config.php';

// Ambil semua produk
$sql = "SELECT * FROM product";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - HealthyBites</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; }
        .navbar-brand { color: #FF8C00 !important; font-weight: 700; }
        .nav-link { color: #228B22 !important; font-weight: 600; }
        .nav-link:hover { color: #FF8C00 !important; }
        .section-title, h2.text-success { color: #228B22; margin-top: 30px; font-weight: 700; }
        .btn-warning {
            background-color: #FF8C00;
            border-color: #FF8C00;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #e67600;
            border-color: #e67600;
            color: white;
        }
        .hero-admin {
            background-color: #f8f9fa;
            padding: 60px 0;
            text-align: center;
        }
        .welcome-sidebar {
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.05);
            margin-bottom: 2rem;
        }
        .testimonial-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            padding-bottom: 10px;
        }
        .testimonial-card {
            flex: 1 1 300px;
            max-width: 320px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 8px rgb(0 0 0 / 0.1);
            padding: 1rem;
        }
        .testimonial-card p { font-style: italic; }
        .testimonial-card h5 { color: #FF8C00; font-weight: 700; margin-top: 1rem; }
        #testimonial h2, p { text-align: center; }
        .table thead { background-color: #e6f2d9; }
        .table-bordered th, .table-bordered td {
            border: 1px solid #c1dca3;
            vertical-align: middle;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">HealthyBites Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#manage">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="#orders">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonial">Testimonial</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil_admin.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="login_admin.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div id="home" class="container mt-5 pt-5">
        <!-- Welcome Section -->
        <div class="welcome-sidebar">
            <h2 class="text-success">Welcome, Admin!</h2>
            <p>Kelola konten dan produk HealthyBites dari satu tempat.</p>
        </div>

        <!-- Manage Product Section -->
        <div id="manage" class="mb-5">
            <h2 class="text-success">Manage Product</h2>
            <a href="managementProduct.php" class="btn btn-warning mb-3"><i class="fas fa-plus"></i> Tambah Produk</a>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $nama = htmlspecialchars($row['namaProduct']);
                                $harga = number_format($row['harga'], 0, ',', '.');
                                $stok = htmlspecialchars($row['jumlah']);
                                $image = htmlspecialchars($row['image']);

                                echo "<tr>";
                                echo "<td>{$id}</td>";
                                echo "<td>";
                                if (!empty($image)) {
                                    echo "<img src='../img/products/{$image}' class='product-img' alt='{$nama}'>";
                                } else {
                                    echo "<span class='text-muted'>No Image</span>";
                                }
                                echo "</td>";
                                echo "<td>{$nama}</td>";
                                echo "<td>Rp {$harga}</td>";
                                echo "<td>{$stok}</td>";
                                echo "<td class='text-center'>
                                    <a href='managementProduct.php?action=edit&id={$id}' class='btn btn-sm btn-warning' title='Edit'><i class='fas fa-edit'></i></a>
                                    <a href='delete_product.php?action=delete&id={$id}' onclick=\"return confirm('Yakin ingin menghapus produk ini?');\" class='btn btn-sm btn-danger' title='Hapus'><i class='fas fa-trash'></i></a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted">Tidak ada produk ditemukan.</td></tr>';
                        }
                        ?>
                            <?php if (isset($_GET['success'])): ?>
                            <script>alert("<?= htmlspecialchars($_GET['success']) ?>");</script>
                            <?php endif; ?>

                            <?php if (isset($_GET['error'])): ?>
                                <script>alert("<?= htmlspecialchars($_GET['error']) ?>");</script>
                            <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Management Section -->
        <div id="orders" class="mb-5">
            <h2 class="text-success">Manage Orders</h2>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders_sql = "SELECT o.id, o.first_name, o.last_name, o.created_at, o.total_amount, os.status 
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
                                      ORDER BY o.created_at DESC";
                        $orders_result = $conn->query($orders_sql);
                        
                        if ($orders_result && $orders_result->num_rows > 0) {
                            while ($order = $orders_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$order['id']}</td>";
                                echo "<td>{$order['first_name']} {$order['last_name']}</td>";
                                echo "<td>" . date('M j, Y', strtotime($order['created_at'])) . "</td>";
                                echo "<td>Rp" . number_format($order['total_amount'], 0, ',', '.') . "</td>";
                                echo "<td>";
                                echo "<span class='px-2 py-1 rounded-full text-xs ";
                                echo $order['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                     ($order['status'] == 'delivered' ? 'bg-blue-100 text-blue-800' : 
                                     ($order['status'] == 'shipped' ? 'bg-purple-100 text-purple-800' : 
                                     ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')));
                                echo "'>";
                                echo ucfirst($order['status']);
                                echo "</span>";
                                echo "</td>";
                                echo "<td class='text-center'>";
                                echo "<a href='order_details_admin.php?id={$order['id']}' class='btn btn-sm btn-primary' title='View'><i class='fas fa-eye'></i></a> ";
                                echo "<a href='update_order_status.php?id={$order['id']}' class='btn btn-sm btn-warning' title='Update Status'><i class='fas fa-sync-alt'></i></a> ";
                                echo "<a href='delete_order.php?id={$order['id']}' 
                                       onclick='return confirm(\"Are you sure you want to delete this order? This cannot be undone.\");' 
                                       class='btn btn-sm btn-danger' title='Delete'>
                                       <i class='fas fa-trash'></i>
                                     </a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted">No orders found.</td></tr>';
                        }
                        ?>

                        <?php
                        if (isset($_SESSION['error'])) {
                            echo "<script>alert('" . addslashes($_SESSION['error']) . "');</script>";
                            unset($_SESSION['error']);
                        }

                        if (isset($_SESSION['message'])) {
                            echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
                            unset($_SESSION['message']);
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Testimonial Section -->
        <div id="testimonial" class="mb-5">
            <h2 class="text-success fw-bold">Testimoni Pelanggan</h2>
            <p>Apa kata mereka tentang layanan kami</p>
            
            <?php
            // Get all testimonials from buyers
            $testimonial_query = "SELECT t.*, p.namaProduct as product_name 
                                FROM testimonials t
                                JOIN product p ON t.product_id = p.id
                                ORDER BY t.created_at DESC";
            $testimonial_result = $conn->query($testimonial_query);
            ?>
            
            <div class="testimonial-container">
                <?php if ($testimonial_result && $testimonial_result->num_rows > 0): ?>
                    <?php while ($testimonial = $testimonial_result->fetch_assoc()): ?>
                        <div class="testimonial-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5><?= htmlspecialchars($testimonial['name'] ?? 'Anonymous') ?></h5>
                                    <small>Product: <?= htmlspecialchars($testimonial['product_name']) ?></small>
                                </div>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i > $testimonial['rating'] ? '-empty' : '' ?> text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="mb-2"><?= htmlspecialchars($testimonial['comment']) ?></p>
                            <small class="text-muted"><?= date('M j, Y', strtotime($testimonial['created_at'])) ?></small>
                            <div class="mt-2 text-end">
                                <a href="delete_testimonial.php?id=<?= $testimonial['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this testimonial?');"
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">No testimonials found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>