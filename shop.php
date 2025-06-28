<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "healthybites");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Helper function to format Rupiah
function format_rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

// Handle Add to Cart
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    $user_id = $_SESSION['user_id'] ?? 0;
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity if exists
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        // Add new product to cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $product_id);
    }
    $stmt->execute();
    
    // Update cart count in session
    $cart_count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
    $_SESSION['cart_count'] = $cart_count;
    
    header("Location: cart.php");
    exit();
}

// Handle Search - Only search by product name
$search_query = "";
$where_clause = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($conn->real_escape_string($_GET['search']));
    $where_clause = "WHERE namaProduct LIKE '%$search_query%'";
}

// Get cart count for display
$user_id = $_SESSION['user_id'] ?? 0;
$cart_count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
$_SESSION['cart_count'] = $cart_count;

// Get products from database with search filter
$products = [];
$sql = "SELECT * FROM product";
if (!empty($where_clause)) {
    $sql .= " " . $where_clause;
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $image_path = 'img/products/' . $row['image'];
        // Check if image file exists, otherwise use default
        $actual_image = (file_exists($image_path) && is_file($image_path)) ? $row['image'] : 'default-product.jpg';
        
        $products[] = [
            'id_produk' => $row['id'] ?? 0,
            'produk_name' => $row['namaProduct'] ?? 'Unknown Product',
            'price' => $row['harga'] ?? 0,
            'image' => $actual_image,
            'deskripsi' => $row['deskripsi'] ?? 'No description available',
            'jumlah' => $row['jumlah'] ?? 0
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HealthyBites - Shop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Raleway:wght@700;800&display=swap" rel="stylesheet"> 

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
        body {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            font-style: normal;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Raleway', sans-serif;
            font-weight: 700;
            font-style: normal;
        }
        .admin-only {
            display: <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'block' : 'none'; ?>;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        .page-header {
            margin-bottom: 40px;
        }
        .btn, .nav-item, .breadcrumb-item, .footer-item, .footer-item a {
            font-weight: 600;
            font-style: normal;
        }
        .modal-title, .fruite-item h4 {
            font-weight: 700;
            font-style: normal;
        }
        i.fas, i.fab, i.far {
            font-style: normal !important;
        }
        .fruite-item {
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: white;
        }
        .fruite-img {
            position: relative;
            overflow: hidden;
            background-color: #f8f9fa;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            padding: 10px;
        }
        .fruite-item:hover .product-image {
            transform: scale(1.05);
        }
        .product-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }
        .product-description {
            flex-grow: 1;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #555;
        }
        .product-footer {
            margin-top: auto;
        }
        .category-badge {
            position: absolute;
            top: 10px; 
            left: 10px;
            font-size: 0.8rem;
            z-index: 1;
        }
        .text-ellipsis {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .no-results {
            text-align: center;
            padding: 50px 0;
        }
        .search-results-count {
            font-size: 1.2rem;
            color: #fff;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
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
                        <a href="shop.php" class="nav-item nav-link active">Shop</a>
                        <a href="testimoni.php" class="nav-item nav-link">Testimonial</a>
                        <a href="login.php"class="nav-item nav-link">Logout</a>
                    </div>
                    <div class="d-flex m-3 me-0">
                        <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Search by product name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <form action="shop.php" method="GET" class="input-group w-75 mx-auto d-flex">
                        <input type="search" name="search" class="form-control p-3" placeholder="Enter product name..." aria-describedby="search-icon-1" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Search End -->

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6" style="font-style: normal; font-weight: 700;">
            <?php 
            if(!empty($search_query)) {
                echo "Search Results for: \"" . htmlspecialchars($search_query) . "\""; 
            } else {
                echo "Our Fresh Products";
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

    <!-- Products Section Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <?php if(empty($products) && !empty($search_query)): ?>
                <div class="no-results">
                    <h3>No products found for "<?php echo htmlspecialchars($search_query); ?>"</h3>
                    <a href="shop.php" class="btn btn-primary mt-3">View All Products</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach($products as $product): ?>
                    <div class="fruite-item">
                        <div class="fruite-img">
                            <img src="img/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['produk_name']); ?>"
                                 onerror="this.onerror=null;this.src='img/products/default-product.jpg';">
                        </div>
                        <div class="product-content">
                            <h4 class="text-ellipsis"><?php echo htmlspecialchars($product['produk_name']); ?></h4>
                            <p class="product-description text-ellipsis"><?php echo htmlspecialchars($product['deskripsi']); ?></p>
                            <div class="product-footer d-flex justify-content-between align-items-center">
                                <p class="text-dark fs-5 fw-bold mb-0"><?php echo format_rupiah($product['price']); ?></p>
                                <a href="shop.php?add_to_cart=<?php echo $product['id_produk']; ?>" 
                                   class="btn btn-primary rounded-pill px-3">
                                   <i class="fa fa-shopping-bag me-2" style="font-style: normal;"></i> Add to cart
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Add Product Button (Admin Only) -->
                    <div class="col-12 text-center mt-5 admin-only">
                        <a href="add_product.php" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus me-2" style="font-style: normal;"></i> Add New Product
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Products Section End -->

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