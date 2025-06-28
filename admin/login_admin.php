<?php
// login_admin.php - HealthyBites Admin Login Page
session_start(); // Start the session at the very beginning

// =========================================================
// PENTING: PENGATURAN DEBUGGING INI HARUS DIHAPUS DI PRODUKSI!
ini_set('display_errors', 1);
error_reporting(E_ALL);
// =========================================================

$error_message = ''; // Initialize error message

// Database connection details (pastikan sama dengan file PHP Anda yang lain)
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // e.g., 'root'
define('DB_PASS', ''); // e.g., '' (empty for no password)
define('DB_NAME', 'healthybites');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = trim($_POST['username'] ?? ''); // Mengambil input username
    $input_password = $_POST['password'] ?? ''; // Plain-text password dari form

    // Basic validation
    if (empty($input_username) || empty($input_password)) {
        $error_message = "Username and password are required.";
    } else {
        try {
            // Establish database connection using PDO
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Perubahan Utama di Sini: Ambil hashed password dari DB, lalu verifikasi
            $stmt = $pdo->prepare("SELECT id_admin, username, password FROM admin WHERE username = :username");
            $stmt->bindParam(':username', $input_username);
            $stmt->execute();
            
            $admin = $stmt->fetch(PDO::FETCH_ASSOC); // Ambil data admin

            // Verifikasi password menggunakan password_verify()
            if ($admin && password_verify($input_password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['id_admin'] = $admin['id_admin']; 
                $_SESSION['username'] = $admin['username'];
                $_SESSION['role'] = 'admin'; 

                // Redirect based on redirect_url or to default page
                if (isset($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    header("Location: $redirect_url");
                    exit();
                } else {
                    header("Location: home_admin.php");
                    exit();
                }

            } else {
                // Invalid credentials (username tidak ditemukan atau password salah)
                $error_message = "Invalid username or password.";
            }

        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
            // Di produksi, log error ini daripada menampilkannya langsung ke user.
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Healthy Bites - Admin Login</title> 
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Reset dan dasar styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Background dan font */
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
        }

        /* Container form */
        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Card/form box styling */
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        h3 {
            text-align: center;
            color: #76B900;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: .5rem;
            color: #333;
            font-weight: 600;
        }

        /* Input styling */
        input[type="text"], /* Untuk username */
        input[type="password"] {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        /* Bagian bawah form */
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .form-footer label {
            font-weight: normal;
        }

        /* Tombol Login */
        .btn {
            display: inline-block;
            width: 100%;
            padding: 0.6rem 1rem;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 0.375rem;
            background-color: #76B900;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: #5f9900;
        }

        /* Link styling */
        a {
            color: #76B900;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Utility classes */
        .text-center {
            text-align: center;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        /* Error message styling */
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h3>Login to HealthyBites Admin</h3> 
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="login_admin.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label> 
                    <input type="text" id="username" name="username" placeholder="Enter your username" required> 
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-footer">
                    <div>
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <p class="text-center mt-3 mb-0">Don't have an account? <a href="register_admin.php">Register here</a></p>
        </div>
    </div>
</body>

</html>