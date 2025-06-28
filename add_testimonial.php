<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Verify order belongs to user and is delivered
$order_query = "SELECT o.id, os.status 
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
                WHERE o.id = ? AND o.user_id = ? AND os.status = 'delivered'";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found or not eligible for testimonial");
}

// Get order items
$items_query = "SELECT oi.product_id, p.namaProduct, p.image 
                FROM order_items oi
                JOIN product p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    // Check if testimonial already exists for this product in this order
    $check_query = "SELECT id FROM testimonials WHERE user_id = ? AND order_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iii", $user_id, $order_id, $product_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $error = "You've already submitted a testimonial for this product in this order.";
    } else {
        // Insert testimonial
        $insert_query = "INSERT INTO testimonials (user_id, product_id, order_id, rating, comment)
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiiis", $user_id, $product_id, $order_id, $rating, $comment);
        
        if ($stmt->execute()) {
            // Update order status to completed if all items have testimonials
            $items_count_query = "SELECT COUNT(*) as count FROM order_items WHERE order_id = ?";
            $stmt = $conn->prepare($items_count_query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $items_count = $stmt->get_result()->fetch_assoc()['count'];
            
            $testimonials_count_query = "SELECT COUNT(*) as count FROM testimonials WHERE order_id = ?";
            $stmt = $conn->prepare($testimonials_count_query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $testimonials_count = $stmt->get_result()->fetch_assoc()['count'];
            
            if ($items_count == $testimonials_count) {
                $status_query = "INSERT INTO order_status (order_id, status) VALUES (?, 'completed')";
                $stmt = $conn->prepare($status_query);
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
            }
            
            header("Location: order_details.php?order_id=$order_id");
            exit();
        } else {
            $error = "Error submitting testimonial. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Testimonial - HealthyBites</title>
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
        <!-- Navbar end-->

    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Add Testimonial</h1>
    </div>
    <!-- Single Page Header End -->

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-light rounded p-5">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <p class="mb-4">Thank you for your order! Please share your experience with the products you received.</p>
                    
                    <div class="row g-4">
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <?php
                            // Check if testimonial already exists for this item
                            $check_query = "SELECT id FROM testimonials WHERE user_id = ? AND order_id = ? AND product_id = ?";
                            $stmt = $conn->prepare($check_query);
                            $stmt->bind_param("iii", $user_id, $order_id, $item['product_id']);
                            $stmt->execute();
                            $has_testimonial = $stmt->get_result()->num_rows > 0;
                            ?>
                            
                            <div class="col-12">
                                <div class="card <?= $has_testimonial ? 'border-success' : '' ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="img/products/<?= htmlspecialchars($item['image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['namaProduct']) ?>" 
                                                 class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            <h5 class="card-title mb-0"><?= htmlspecialchars($item['namaProduct']) ?></h5>
                                        </div>
                                        
                                        <?php if ($has_testimonial): ?>
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle me-2"></i> You've already submitted a testimonial for this product.
                                            </div>
                                        <?php else: ?>
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Rating</label>
                                                    <div class="rating-input">
                                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                                            <input type="radio" id="rating-<?= $item['product_id'] ?>-<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i == 5 ? 'checked' : '' ?>>
                                                            <label for="rating-<?= $item['product_id'] ?>-<?= $i ?>"><i class="fas fa-star"></i></label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="comment-<?= $item['product_id'] ?>" class="form-label">Your Review</label>
                                                    <textarea class="form-control" id="comment-<?= $item['product_id'] ?>" name="comment" rows="3" required></textarea>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary">
                                                    Submit Testimonial
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="order_details.php?order_id=<?= $order_id ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">Our customers appreciate our commitment to quality, exceptional service, and innovative solutions that truly make a difference.</p>
                            <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Condition</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
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
    
    <style>
        /* Rating input styling */
        .rating-input {
            display: flex;
            justify-content: center;
            direction: rtl;
        }
        .rating-input input {
            display: none;
        }
        .rating-input label {
            color: #ddd;
            font-size: 1.5rem;
            padding: 0 5px;
            cursor: pointer;
        }
        .rating-input input:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label {
            color: #ffc107;
        }
    </style>
    
    <script>
        // Highlight active nav link
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => {
                if (link.textContent.trim() === 'Testimonial' || link.href.includes('testimoni.php')) {
                    link.classList.add('active');
                    link.classList.add('text-primary');
                } else {
                    link.classList.remove('active');
                    link.classList.remove('text-primary');
                }
            });
        });
    </script>
</body>
</html>