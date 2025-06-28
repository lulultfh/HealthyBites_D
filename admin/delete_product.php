<?php
session_start();
include '../config.php';

// Ensure only admin can access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Get image name
        $stmt = $conn->prepare("SELECT image FROM product WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image'];

            // Delete product
            $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                // Delete image
                if (!empty($image)) {
                    $uploadDir = "../img/products/";
                    if (file_exists($uploadDir . $image)) {
                        unlink($uploadDir . $image);
                    }
                }
                header("Location: home_admin.php?success=Produk berhasil dihapus");
                exit();
            } else {
                header("Location: home_admin.php?error=Gagal menghapus produk");
                exit();
            }
        } else {
            header("Location: home_admin.php?error=Produk tidak ditemukan");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Cek jika error disebabkan oleh foreign key
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            header("Location: home_admin.php?error=Produk tidak bisa dihapus karena sedang digunakan dalam data pesanan.");
        } else {
            header("Location: home_admin.php?error=Terjadi kesalahan: " . urlencode($e->getMessage()));
        }
        exit();
    }
} else {
    header("Location: home_admin.php");
    exit();
}
?>
