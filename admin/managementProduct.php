<?php
session_start();
include '../config.php';

// Ensure only admin can access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit();
}

// Initialize variables
$product = [
    'id' => 0,
    'namaProduct' => '',
    'harga' => '',
    'jumlah' => '',
    'deskripsi' => '',
    'image' => ''
];
$isEdit = false;
$success_msg = $error_msg = '';

// Handle edit case
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $isEdit = true;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $product['namaProduct'] = trim($_POST['namaProduct']);
    $product['harga'] = (float)$_POST['harga'];
    $product['jumlah'] = (int)$_POST['jumlah'];
    $product['deskripsi'] = trim($_POST['deskripsi']);
    $product['id'] = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    // Handle image upload
$uploadErrors = [];
if (!empty($_FILES['image']['name'])) {
    $uploadDir = "../img/products/";
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 11 * 1024 * 1024; // Diubah dari 2MB menjadi 11MB
        
        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors[] = "File upload error: " . $_FILES['image']['error'];
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
        if (!in_array($mime, $allowedTypes)) {
            $uploadErrors[] = "Only JPG, PNG, and GIF files are allowed.";
        }
        
        // Check file size
        if ($_FILES['image']['size'] > $maxSize) {
            $uploadErrors[] = "File size must be less than 2MB.";
        }
        
        if (empty($uploadErrors)) {
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Delete old image if exists
                if (!empty($product['image']) && file_exists($uploadDir . $product['image'])) {
                    unlink($uploadDir . $product['image']);
                }
                $product['image'] = $imageName;
            } else {
                $uploadErrors[] = "Failed to move uploaded file.";
            }
        }
    } elseif ($product['id'] > 0 && empty($_FILES['image']['name'])) {
        // Keep existing image if not uploading new one during edit
        $product['image'] = $_POST['existing_image'];
    }
    
    if (!empty($uploadErrors)) {
        $error_msg = implode("<br>", $uploadErrors);
    } else {
        // Save to database
        if ($product['id'] > 0) {
            // Update existing product
            $stmt = $conn->prepare("UPDATE product SET namaProduct=?, harga=?, jumlah=?, deskripsi=?, image=? WHERE id=?");
            $stmt->bind_param("sdissi",
                $product['namaProduct'],
                $product['harga'],
                $product['jumlah'],
                $product['deskripsi'],
                $product['image'],
                $product['id']
            );
        } else {
            // Insert new product - ensure image is not empty for new products
            if (empty($product['image'])) {
                $error_msg = "Product image is required for new products";
            } else {
                $stmt = $conn->prepare("INSERT INTO product (namaProduct, harga, jumlah, deskripsi, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sdiss",
                    $product['namaProduct'],
                    $product['harga'],
                    $product['jumlah'],
                    $product['deskripsi'],
                    $product['image']
                );
            }
        }
        
        if (empty($error_msg)) {
            if ($stmt->execute()) {
                $success_msg = $product['id'] > 0 ? "Produk berhasil diperbarui" : "Produk berhasil ditambahkan";
                if ($product['id'] == 0) {
                    // Reset form for new entries
                    $product = [
                        'id' => 0,
                        'namaProduct' => '',
                        'harga' => '',
                        'jumlah' => '',
                        'deskripsi' => '',
                        'image' => ''
                    ];
                }
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Get all products for display
$products = $conn->query("SELECT * FROM product ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-thumbnail {
            max-height: 150px;
            object-fit: cover;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?= $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h2>
            <a href="home_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['image']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="namaProduct" class="form-label required-field">Nama Produk</label>
                        <input type="text" class="form-control" id="namaProduct" name="namaProduct" 
                               value="<?= htmlspecialchars($product['namaProduct']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label required-field">Harga (Rp)</label>
                            <input type="number" class="form-control" id="harga" name="harga" 
                                   value="<?= htmlspecialchars($product['harga']) ?>" min="0" step="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label required-field">Jumlah Stok</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                   value="<?= htmlspecialchars($product['jumlah']) ?>" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label required-field">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                  rows="3" required><?= htmlspecialchars($product['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        <?php if ($isEdit && !empty($product['image'])): ?>
                            <div class="mt-2">
                                <img src="../img/products/<?= htmlspecialchars($product['image']) ?>" 
                                     class="img-thumbnail" alt="Gambar Produk">
                                <p class="text-muted mt-1">File saat ini: <?= htmlspecialchars($product['image']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? 'Update Produk' : 'Simpan Produk' ?>
                    </button>
                    
                    <?php if ($isEdit): ?>
                        <a href="managementProduct.php" class="btn btn-outline-secondary">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Daftar Produk</h3>
            </div>
            <div class="card-body">
                <?php if ($products->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td>
                                            <?php if (!empty($row['image'])): ?>
                                                <img src="../img/products/<?= htmlspecialchars($row['image']) ?>" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px;">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['namaProduct']) ?></td>
                                        <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada produk yang tersedia</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($_GET['success'])): ?>
    <script>alert("<?= htmlspecialchars($_GET['success']) ?>");</script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <script>alert("<?= htmlspecialchars($_GET['error']) ?>");</script>
    <?php endif; ?>

</body>
</html>