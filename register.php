<?php
require_once 'config.php'; // File ini harus berisi koneksi ke $conn

$registrationSuccess = false;
$registrationError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $termsChecked = isset($_POST['termsCheck']);

    // Validasi input
    if (empty($username) || empty($email) || empty($passwordRaw) || empty($confirmPassword)) {
        $registrationError = "Please fill all fields.";
    } elseif ($passwordRaw !== $confirmPassword) {
        $registrationError = "Passwords do not match.";
    } elseif (!$termsChecked) {
        $registrationError = "You must agree to the terms.";
    } else {
        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $registrationSuccess = true;
        } else {
            $registrationError = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HealthyBites - Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      padding: 40px 20px;
    }

    .form-register {
      max-width: 500px;
      margin: auto;
      margin-top: 50px;
      padding: 30px;
      border-radius: 15px;
      background-color: #ffffff;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .btn-green {
      background-color: #81c408;
      color: white;
    }
    .btn-green:hover {
      background-color: #6bab06;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="form-register">
    <h3 class="text-center mb-4 text-success">HealthyBites - Register</h3>

    <?php if ($registrationError): ?>
      <div class="alert alert-danger"><?php echo $registrationError; ?></div>
    <?php elseif ($registrationSuccess): ?>
      <div class="alert alert-success">Registration successful! <a href="login.php">Click here to login</a></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input name="username" type="text" class="form-control" id="username" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input name="email" type="email" class="form-control" id="email" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input name="password" type="password" class="form-control" id="password" required>
      </div>

      <div class="mb-3">
        <label for="confirmPassword" class="form-label">Confirm Password</label>
        <input name="confirmPassword" type="password" class="form-control" id="confirmPassword" required>
      </div>

      <div class="form-check mb-3">
        <input name="termsCheck" class="form-check-input" type="checkbox" id="termsCheck" required>
        <label class="form-check-label" for="termsCheck">
          I agree to the <a href="#">Terms & Conditions</a>
        </label>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-green">Register</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
