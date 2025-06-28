<?php
session_start();
require_once 'config.php';

// Proses aksi keranjang (tambah/kurang/hapus)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'] ?? 0;

    switch ($action) {
        case 'increase':
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            break;
            
        case 'decrease':
            // Cek quantity saat ini
            $check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $check->bind_param("ii", $user_id, $product_id);
            $check->execute();
            $result = $check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['quantity'] > 1) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            } else {
                // Jika quantity = 1, hapus dari keranjang
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            }
            break;
            
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            break;
    }
    
    // Update session cart count
    $cart_count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
    $_SESSION['cart_count'] = $cart_count;
    
    header("Location: cart.php");
    exit();
}

// Ambil data keranjang
$user_id = $_SESSION['user_id'] ?? 0;
$query = "SELECT p.id, p.namaProduct, p.harga, p.image, c.quantity 
          FROM cart c 
          JOIN product p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get cart count for display
$cart_count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
$_SESSION['cart_count'] = $cart_count;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cart - HealthyBites</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* Style untuk tombol My Orders */
        .my-orders-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .my-orders-btn .btn {
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            display: flex;
            align-items: center;
            min-width: 150px;
            justify-content: center;
        }
        
        /* Style untuk gambar produk */
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #eee;
            border-radius: 4px !important;
        }
        
        /* Style untuk tombol aksi */
        .action-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

                  <!-- Navbar start -->
<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">123 Street, New York</a></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">Email@Example.com</a></small>
            </div>
            <div class="top-link pe-2">
                <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
            </div>
        </div>
    </div>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="index.php" class="navbar-brand"><h1 class="text-primary display-6">HealthyBites</h1></a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="shop.php" class="nav-item nav-link">Shop</a>
                    <a href="testimoni.php" class="nav-item nav-link">Testimonial</a>
                    <a href="login.php"class="nav-item nav-link">Logout</a>
                </div>
                <div class="d-flex m-3 me-0">
                    <a href="cart.php" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
                            <?php echo $_SESSION['cart_count'] ?? 0; ?>
                        </span>
                    </a>
                    <a href="Profiles.php" class="my-auto">
                        <i class="fas fa-user fa-2x"></i>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</div>
        <!-- Navbar End -->

    <!-- Modal Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Search End -->

    <!-- Tombol My Orders (Fixed Position) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="my-orders-btn">
    <a href="my_orders.php" class="btn bg-primary text-white hover:bg-primary-dark">
        My Orders
    </a>
    </div>
    <?php endif; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Keranjang Belanja</h1>
        <ol class="breadcrumb justify-content-center mb-0">
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $total = 0;
                        while ($row = $result->fetch_assoc()):
                            $subtotal = $row['harga'] * $row['quantity'];
                            $total += $subtotal;
                    ?>
                        <tr>
                            <th scope="row">
                                <div class="d-flex align-items-center">
                                    <img src="img/products/<?= htmlspecialchars($row['image']) ?>" 
                                         class="img-fluid me-5 product-img" 
                                         alt="<?= htmlspecialchars($row['namaProduct']) ?>">
                                    <p class="mb-0"><?= htmlspecialchars($row['namaProduct']) ?></p>
                                </div>
                            </th>
                            <td>
                                <p class="mb-0 mt-4">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
                            </td>
                            <td>
                                <form method="POST" class="d-flex align-items-center mt-4">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <button name="action" value="decrease" 
                                            class="btn btn-sm btn-minus rounded-circle bg-light border action-btn">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <span class="mx-2"><?= $row['quantity'] ?></span>
                                    <button name="action" value="increase" 
                                            class="btn btn-sm btn-plus rounded-circle bg-light border action-btn">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <p class="mb-0 mt-4">Rp<?= number_format($subtotal, 0, ',', '.') ?></p>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <button name="action" value="delete" 
                                            class="btn btn-md rounded-circle bg-light border mt-4 action-btn">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 d-flex justify-content-between align-items-center">
                <a href="shop.php" class="btn bg-primary text-white px-4 py-2 rounded-pill hover:bg-primary-dark">
                    <i class="fas fa-arrow-left me-2"></i> Lanjut Belanja
                </a>
                <div class="text-right">
                    <div class="text-xl fw-bold mb-3">
                        Total: Rp<?= number_format($total, 0, ',', '.') ?>
                    </div>
                    <a href="checkout.php" class="btn bg-secondary text-white px-4 py-2 rounded-pill hover:bg-secondary-dark">
                        <i class="fas fa-credit-card me-2"></i> Checkout
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-secondary"></i>
                </div>
                <h2 class="text-2xl fw-semibold mb-3">Keranjang Belanja Kosong</h2>
                <p class="mb-4">Silakan tambahkan produk dari toko kami</p>
                <a href="shop.php" class="btn bg-primary text-white px-4 py-2 rounded-pill hover:bg-primary-dark">
                    <i class="fas fa-store me-2"></i> Belanja Sekarang
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Cart Page End -->

       <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">Our customers appreciate our commitment to quality, exceptional service, and innovative solutions that truly make a difference.</p>
                            <!-- <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a> -->
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a >About Us</a>
                            <a >Contact Us</a>
                            <a >Privacy Policy</a>
                            <a >Terms & Condition</a>
                            <a >Return Policy</a>
                            <a >FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a >My Account</a>
                            <a >Shop details</a>
                            <a >Shopping Cart</a>
                            <a >Wishlist</a>
                            <a >Order History</a>
                            <a >International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: 1429 Netus Rd, NY 48247</p>
                            <p>Email: Example@gmail.com</p>
                            <p>Phone: +0123 4567 8910</p>
                            <p>Payment Accepted</p>
                            <img src="img/payment.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>HealthyBites</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->

 

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>