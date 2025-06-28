<?php
// register_admin.php - HealthyBites Admin Registration Page

// =========================================================
// PENTING: PENGATURAN DEBUGGING INI HARUS DIHAPUS DI PRODUKSI!
ini_set('display_errors', 1);
error_reporting(E_ALL);
// =========================================================

session_start(); // Start the session at the very beginning

$error_message = '';   // Initialize error message

// Database connection details (pastikan sama dengan login.php atau konfigurasi umum Anda)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Ganti dengan username database Anda jika berbeda
define('DB_PASS', '');          // Ganti dengan password database Anda jika berbeda
define('DB_NAME', 'healthybites');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // FIX 1: Tangkap input 'admin_name' dari HTML dengan nama yang benar
    $input_admin_name = trim($_POST['admin_name'] ?? ''); 
    $input_username = trim($_POST['username'] ?? '');
    $input_email = trim($_POST['email'] ?? '');
    $input_password = $_POST['password'] ?? '';
    $input_confirm_password = $_POST['confirm_password'] ?? '';
    $terms_agreed = isset($_POST['terms_check']);

    // 1. Basic validation: Check if all fields are filled
    // FIX 1: Periksa variabel yang benar ($input_admin_name)
    if (empty($input_admin_name) || empty($input_username) || empty($input_email) || empty($input_password) || empty($input_confirm_password) || !$terms_agreed) {
        $error_message = "Semua field wajib diisi dan Anda harus menyetujui Syarat & Ketentuan.";
    } 
    // 2. Validate password match
    else if ($input_password !== $input_confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } 
    // 3. Password strength (optional, but recommended)
    else if (strlen($input_password) < 6) {
        $error_message = "Password harus memiliki minimal 6 karakter.";
    }
    else {
        try {
            // Establish database connection
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4. Check if username or email already exists in 'admin' table
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $input_username);
            $stmt->bindParam(':email', $input_email);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $error_message = "Username atau Email sudah terdaftar sebagai Admin. Silakan gunakan yang lain.";
            } else {
                // 5. Hash the password (CRITICAL for security!)
                $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);

                // 6. Insert new admin into the 'admin' table
                // FIX 2: Sesuaikan nama kolom di INSERT query menjadi 'nama_admin'
                $stmt = $pdo->prepare("INSERT INTO admin (nama_admin, username, email, password) VALUES (:nama_admin, :username, :email, :password)");
                $stmt->bindParam(':nama_admin', $input_admin_name); // Bind new admin name
                $stmt->bindParam(':username', $input_username);
                $stmt->bindParam(':email', $input_email);
                $stmt->bindParam(':password', $hashed_password); // Store the hashed password
                
                if ($stmt->execute()) {
                    // Redirect to login_admin.php
                    header("Location: login_admin.php"); 
                    exit(); // Penting untuk menghentikan eksekusi script setelah redireksi
                } else {
                    $error_message = "Terjadi kesalahan saat registrasi admin. Silakan coba lagi.";
                }
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HealthyBites - Register Admin</title> <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 

    <style>
        /* Base styles consistent with login.php but adapting for register page layout */
        body {
            font-family: 'Poppins', sans-serif; 
            background: url('https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
        }

        /* Register form specific styling */
        .form-register {
            max-width: 500px;
            margin: auto;
            margin-top: 50px; /* Adjust margin-top as needed */
            padding: 30px;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        /* Button styling */
        .btn-green {
            background-color: #81c408;
            color: white;
            padding: 0.6rem 1rem; /* Consistent with login button size */
            font-weight: 600;    /* Consistent with login button font */
            font-size: 1rem;
            border: none;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }
        .btn-green:hover {
            background-color: #6bab06;
            color: white; /* Ensure text remains white on hover */
        }
        
        /* Heading and text colors */
        .text-success {
            color: #76B900 !important; /* Overriding Bootstrap's default green to match your theme */
            font-weight: bold; /* Your example had style="font: bold;" but CSS font property is complex, using font-weight */
        }

        /* Form labels */
        .form-label {
            display: block;
            margin-bottom: .5rem;
            color: #333;
            font-weight: 600; 
            text-align: left; /* Sudah diubah sebelumnya, memastikan rata kiri */
        }

        /* Input styling (consistent with login.php where possible) */
        .form-control {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        
        /* Specifically for the checkbox label ("I agree to the Terms & Conditions") */
        .form-check-label {
            text-align: left !important; /* Menggunakan !important untuk memastikan rata kiri */
            /* Anda mungkin juga perlu menyesuaikan padding-left jika teks terlalu dekat dengan checkbox */
            /* padding-left: 0.5rem; */
        }

        /* General link styling */
        a {
            color: #76B900;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Message styling */
        .success-message { /* Ini tidak akan ditampilkan karena sudah di-redirect */
            color: green;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }
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
        <div class="form-register">
            <h3 class="text-center mb-4 text-success">HealthyBites - Register Admin</h3>

            <?php 
            // $success_message tidak lagi ditampilkan di sini karena akan langsung redirect
            /*
            if (!empty($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; 
            */
            ?>

            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="register_admin.php" method="POST" novalidate> 
                <div class="mb-3">
                    <label for="adminName" class="form-label">Nama Admin</label>
                    <input type="text" class="form-control" id="adminName" name="admin_name" required value="<?php echo htmlspecialchars($input_admin_name ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required value="<?php echo htmlspecialchars($input_username ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($input_email ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheck" name="terms_check" <?php echo ($terms_agreed ?? false) ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="termsCheck">
                        I agree to the <a href="#">Terms & Conditions</a>
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-green">Register</button>
                </div>

                <p class="text-center mt-3">Already have an account? <a href="login_admin.php">Login here</a></p>
            </form>
        </div>
    </div>

</body>
</html>