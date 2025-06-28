<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Cari user di database berdasarkan username
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect ke halaman utama
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Healthy Bites - Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: url('https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

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

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .form-footer label {
            font-weight: normal;
        }

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

        a {
            color: #76B900;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h3>Login to HealthyBites</h3>

            <?php if (!empty($error)) : ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <!-- Username input -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>
                <!-- Password input -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <!-- Remember me dan lupa password -->
                <div class="form-footer">
                    <div>
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>
                <!-- Tombol login -->
                <button type="submit" class="btn">Login</button>
            </form>
            <!-- Link register -->
            <p class="text-center mt-3 mb-0">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>

</html>
