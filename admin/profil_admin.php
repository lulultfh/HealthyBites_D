<?php
session_start();
include '../config.php';

// Cek apakah sudah login admin
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$id_admin = $_SESSION['id_admin'];

// Ambil data profil admin dari DB
$query = "SELECT username, email, address, photo FROM admin WHERE id_admin = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Jika data tidak ditemukan, redirect ke login
if (!$admin) {
    header("Location: login_admin.php");
    exit();
}

$username = $admin['username'];
$email = $admin['email'];
$address = $admin['address'];
$photo = !empty($admin['photo']) ? $admin['photo'] : 'foto_profil.jpg';

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $success = $error = '';

    // Cek jika ada file foto yang diupload
    if (isset($_FILES['photoInput']) && $_FILES['photoInput']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        
        // Pastikan folder uploads ada dan bisa ditulisi
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $error = "Gagal membuat folder uploads.";
            }
        }
        
        // Generate nama file yang unik
        $originalName = basename($_FILES["photoInput"]["name"]);
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $filename = uniqid('profile_', true) . '.' . $fileExtension;
        $targetFile = $uploadDir . $filename;
        
        // Validasi file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 11 * 1024 * 1024; // 11MB
        
        if (!in_array($fileExtension, $allowedTypes)) {
            $error = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
        } elseif ($_FILES["photoInput"]["size"] > $maxFileSize) {
            $error = "Ukuran file terlalu besar. Maksimal 11MB.";
        } else {
            if (move_uploaded_file($_FILES["photoInput"]["tmp_name"], $targetFile)) {
                // Hapus foto lama jika bukan foto default
                if ($photo !== 'foto_profil.jpg' && file_exists($uploadDir . $photo)) {
                    @unlink($uploadDir . $photo);
                }
                
                // Update DB
                $updatePhoto = "UPDATE admin SET photo = ? WHERE id_admin = ?";
                $stmtPhoto = $conn->prepare($updatePhoto);
                $stmtPhoto->bind_param("si", $filename, $id_admin);
                if ($stmtPhoto->execute()) {
                    $photo = $filename;
                    $success = "Foto profil berhasil diperbarui.";
                } else {
                    $error = "Gagal menyimpan informasi foto ke database.";
                    @unlink($targetFile); // Hapus file yang sudah diupload jika gagal update DB
                }
            } else {
                $error = "Gagal mengupload foto. Pastikan folder uploads ada dan memiliki izin yang tepat.";
            }
        }
    }

    // Update data admin hanya jika tidak ada error sebelumnya
    if (empty($error)) {
        $updateQuery = "UPDATE admin SET username = ?, email = ?, address = ? WHERE id_admin = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $username, $email, $address, $id_admin);

        if ($stmt->execute()) {
            $success = empty($success) ? "Profil berhasil diperbarui." : $success;
        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        h2 {
            text-align: center;
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
        .card img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #81C408;
            margin-top: -100px;
            background: white;
            cursor: pointer;
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
            flex-direction: column;
        }
        .info label {
            font-weight: 600;
            margin-bottom: 6px;
        }
        .info input,
        .info textarea {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        .info input:focus,
        .info textarea:focus {
            border-color: #81C408;
            outline: none;
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
        .alert {
            margin-bottom: 15px;
        }
        #photoInput {
            display: none;
        }
    </style>
</head>

<body class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="home_admin.php" class="btn btn-secondary px-3 py-2" style="width: auto;">Kembali ke Dashboard</a>
    </div>
    <h2>Edit Profil Admin</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div class="card">
            <img src="uploads/<?= htmlspecialchars($photo) ?>?<?= time() ?>" alt="Foto Profil" id="profilePic">
            <h2><?= htmlspecialchars($username) ?></h2>
            <p class="username">@<?= htmlspecialchars($username) ?></p>

            <div class="info">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>
            <div class="info">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="info">
                <label for="address">Alamat</label>
                <textarea id="address" name="address" rows="3"><?= htmlspecialchars($address) ?></textarea>
            </div>

            <input type="file" id="photoInput" name="photoInput" accept="image/*" onchange="previewImage(this)">

            <button type="submit" class="btn">Simpan Perubahan</button>
            
        </div>
    </form>

    <script>
        // Fungsi untuk menampilkan preview gambar
        function previewImage(input) {
            if (input.files && input.files[0]) {
                // Validasi ukuran file (client-side)
                if (input.files[0].size > 11534336) { // 11MB dalam bytes
                    alert('Ukuran file terlalu besar! Maksimal 11MB');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePic').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fungsi untuk validasi form sebelum submit
        function validateForm() {
            const fileInput = document.getElementById('photoInput');
            if (fileInput.files.length > 0 && fileInput.files[0].size > 11534336) {
                alert('Ukuran file terlalu besar! Maksimal 11MB');
                return false;
            }
            return true;
        }

        // Klik gambar untuk memilih file
        document.getElementById('profilePic').addEventListener('click', function() {
            document.getElementById('photoInput').click();
        });
    </script>
</body>
</html>