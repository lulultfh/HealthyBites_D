<?php
session_start();
include 'config.php';

// Hitung jumlah item di keranjang jika user sudah login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
    $_SESSION['cart_count'] = $cart_count;
} else {
    $_SESSION['cart_count'] = 0;
}

// Ambil semua testimoni
$testimoni_result = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC");

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message);
        $stmt->execute();
        header("Location: testimoni.php"); // Refresh untuk hindari resubmit
        exit;
    } else {
        $error = "Nama dan pesan tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Testimonial - HealthyBites</title>
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
        /* Custom style untuk highlight testimonial link */
        .nav-testimonial-active {
            color: #28a745 !important;
            font-weight: 600;
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
                    <a href="index.php" class="nav-item nav-link active">Home</a>
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

<!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6" style="font-style: normal; font-weight: 700;">
            <?php 
            if(!empty($search_query)) {
                echo "Search Results for: \"" . htmlspecialchars($search_query) . "\""; 
            } else {
                echo "Apa Kata Pelanggan kami?";
            }
            ?>
        </h1>
        <?php if(!empty($search_query)): ?>
        <p class="text-center search-results-count">
            Found <?php echo count($products); ?> product(s)
        </p>
        <?php endif; ?>
    </div>
    <!-- Single Page Header End -->

    <!-- Testimonial Start - Padding dikurangi dan margin-top dihilangkan -->
    <div class="container-fluid testimonial" style="padding-top: 30px; padding-bottom: 30px;">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="row g-3">
                <?php 
                $testimoni_query = "SELECT t.*, p.namaProduct as product_name 
                                   FROM testimonials t
                                   JOIN product p ON t.product_id = p.id
                                   ORDER BY t.created_at DESC";
                $testimoni_result = $conn->query($testimoni_query);
                
                if ($testimoni_result && $testimoni_result->num_rows > 0): 
                    while ($row = $testimoni_result->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0"><?= htmlspecialchars($row['name'] ?? 'Anonymous') ?></h5>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i > $row['rating'] ? '-empty' : '' ?> text-warning"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-1">Product: <?= htmlspecialchars($row['product_name']) ?></p>
                                    <p class="card-text mb-1"><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                                </div>
                                <div class="card-footer text-muted py-2 px-3">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; 
                else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No testimonials available yet.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

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

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <script>
        // Script untuk menandai link Testimonial sebagai aktif
        document.addEventListener('DOMContentLoaded', function() {
            // Cari semua link navbar
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            // Hapus class active dari semua link
            navLinks.forEach(link => {
                link.classList.remove('active');
                link.classList.remove('text-primary');
            });
            
            // Temukan link Testimonial dan tambahkan class active
            const testimonialLink = Array.from(navLinks).find(link => 
                link.textContent.trim() === 'Testimonial' || 
                link.getAttribute('href').includes('testimoni.php')
            );
            
            if (testimonialLink) {
                testimonialLink.classList.add('active');
                testimonialLink.classList.add('text-primary');
            }
        });
    </script>
</body>
</html>