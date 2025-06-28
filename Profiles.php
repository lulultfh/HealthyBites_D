<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = $success = "";

// Proses update jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $photo = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo);

        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, address=?, photo=? WHERE id=?");
        $stmt->bind_param("sssssi", $username, $email, $phone, $address, $photo, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $phone, $address, $user_id);
    }

    if ($stmt->execute()) {
        $success = "Profil berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui profil.";
    }
}

$stmt = $conn->prepare("SELECT username, email, phone, address, photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil <?= htmlspecialchars($user['username']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">


  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(255,255,255,0.4);
      backdrop-filter: blur(4px);
      z-index: -1;
    }
    body {
      background: url('melon.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      padding-top: 140px;
    }
    .card {
      background: rgba(255, 255, 255, 0.95);
      width: 400px;
      padding: 35px 30px;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
      text-align: center;
      margin: 0 auto 50px;
      margin-top: 160px;
    }
    /* style foto profil tidak diubah */
    .card img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #81C408;
      margin-top: -100px;
      background: white;
      cursor: pointer; /* kasih cursor pointer supaya terasa bisa diklik */
    }
    .card h2 {
      margin-top: 15px;
      color: #333;
    }
    .card p.username {
      color: #777;
      margin-bottom: 20px;
    }
    .info {
      text-align: left;
      margin: 12px 0;
      font-size: 15px;
      color: #444;
      display: flex;
    }
    .info label {
      width: 80px;
      font-weight: 600;
    }
    .btn {
      margin-top: 15px;
      background: #81C408;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background: #6bab06;
    }
    input, textarea {
      width: 100%;
      padding: 8px 10px;
      margin-top: 6px;
      margin-bottom: 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .alert {
      margin-bottom: 15px;
    }
    /* sembunyikan input file, tetap ada di DOM untuk fungsi upload */
    #photoInput {
      display: none;
    }
  </style>
</head>
<body>

<!-- Navbar Start -->
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

<form class="card" method="POST" enctype="multipart/form-data">
  <!-- label agar klik foto memicu input file -->
  <label for="photoInput">
    <img id="profilePic" src="<?= $user['photo'] ? 'uploads/' . htmlspecialchars($user['photo']) : 'img/profil.jpg' ?>" alt="Foto Profil" title="Klik untuk ganti foto profil">
  </label>

  <h2><?= htmlspecialchars($user['username']) ?></h2>
  <p class="username">@<?= htmlspecialchars($user['username']) ?></p>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <!-- input file tersembunyi -->
  <input type="file" name="photo" id="photoInput" accept="image/*" />

  <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required placeholder="Username" />
  <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Email" />
  <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Nomor Telepon" />
  <textarea name="address" placeholder="Alamat"><?= htmlspecialchars($user['address']) ?></textarea>

  <button type="submit" class="btn">Simpan Perubahan</button>
</form>

<script>
  const photoInput = document.getElementById('photoInput');
  const profilePic = document.getElementById('profilePic');

  photoInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if(file){
      profilePic.src = URL.createObjectURL(file);
    }
  });
</script>

</body>
</html>
