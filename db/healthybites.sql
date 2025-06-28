-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jun 2025 pada 20.19
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthybites`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `password`, `username`, `nama_admin`, `photo`, `address`) VALUES
(2, 'crumble@gmail.com', '$2y$10$JRUi1Yk9H1sYV786wVqU1uxrnPsTbBcEhMAPkylnbIiixoYh8PbuO', 'apel', 'kepobgt', 'profile_685279cbe7e7d7.59733386.png', 'jl kemangi'),
(3, 'sk@gmail.com', '$2y$10$ktYXbzKsoOg38iWcNinFUegYAYsJFUe.9mwqKPK16thrr9w76gDt.', 'shoo', 'hmm', 'profile_68526aa9b25fe3.95978191.png', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(29, 17, 7, 1, '2025-06-18 08:12:25'),
(30, 17, 14, 1, '2025-06-18 08:15:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL,
  `shipping_method` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `first_name`, `last_name`, `address`, `city`, `postcode`, `phone`, `email`, `notes`, `payment_method`, `shipping_method`, `total_amount`, `created_at`) VALUES
(1, 1, 'ptk', 'Jemes', 'ttyjuuuuubdde', 'eeeeee', '12345', '08965784334', 'perpusptma@gmail.com', '', 'transfer', 'free', 8000.00, '2025-06-11 18:48:42'),
(3, 11, 'ptk', 'gaming', 'ttyjuuuuubdde', 'eeeeee', '12345', '08965784334', 'studentumy24@umy.ac.id', '', 'transfer', 'free', 32555.00, '2025-06-11 19:07:24'),
(4, 1, 'Beni', 'Belangko', 'Jl kembang', 'township', '12345', '0981625821', 'blgko@gmail.com', '', 'cod', 'free', 21000.00, '2025-06-11 21:47:30'),
(6, 1, 'Lana', 'Sudirman', 'Jl bebek terbang', 'teri', '48912', '08791568123', 'sapi@gmail.com', '', 'transfer', 'free', 16500.00, '2025-06-18 02:26:26'),
(7, 1, 'Selly', 'Mahmud', 'Jl Daun Merah', 'Beringin', '34891', '07518293890', 'apaya@gmail.com', 'reqqq yangg besarrr dan segarrrr mantav', 'cod', 'flat', 31200.00, '2025-06-18 03:15:53'),
(8, 15, 'susanti', 'susi', 'Beninghill', 'hayoo', '12345', '08910283123', 'ssss@gmail.com', '', 'transfer', 'free', 15400.00, '2025-06-18 04:29:59'),
(9, 17, 'val', 't', 'mana aja', 'iya', '35951', '-018289201', 'vall@gamil.com', '', 'cod', 'flat', 27600.00, '2025-06-18 07:51:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(9, 6, 15, 1, 9500.00),
(10, 6, 14, 1, 7000.00),
(11, 7, 6, 1, 3200.00),
(12, 7, 13, 1, 13000.00),
(13, 8, 7, 1, 2400.00),
(14, 8, 13, 1, 13000.00),
(15, 9, 14, 1, 7000.00),
(16, 9, 7, 1, 2400.00),
(17, 9, 6, 1, 3200.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','completed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_status`
--

INSERT INTO `order_status` (`id`, `order_id`, `status`, `created_at`) VALUES
(1, 1, 'pending', '2025-06-11 18:48:42'),
(3, 3, 'pending', '2025-06-11 19:07:24'),
(4, 3, 'delivered', '2025-06-11 19:40:58'),
(5, 3, 'completed', '2025-06-11 19:42:08'),
(6, 1, 'completed', '2025-06-11 21:20:48'),
(7, 4, 'pending', '2025-06-11 21:47:30'),
(8, 4, 'completed', '2025-06-11 21:49:22'),
(9, 4, 'completed', '2025-06-17 10:08:41'),
(13, 6, 'pending', '2025-06-18 02:26:26'),
(14, 6, 'delivered', '2025-06-18 02:44:35'),
(15, 7, 'pending', '2025-06-18 03:15:53'),
(16, 6, 'completed', '2025-06-18 03:20:34'),
(17, 8, 'pending', '2025-06-18 04:29:59'),
(18, 7, 'delivered', '2025-06-18 04:30:42'),
(19, 9, 'pending', '2025-06-18 07:51:30'),
(20, 9, 'delivered', '2025-06-18 08:11:14'),
(21, 8, 'shipped', '2025-06-18 08:32:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `namaProduct` varchar(50) NOT NULL,
  `Product` varchar(100) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product`
--

INSERT INTO `product` (`id`, `namaProduct`, `Product`, `harga`, `jumlah`, `deskripsi`, `image`) VALUES
(6, 'kelapa', '', 3200.00, 3, 'Muda dan baru', '6852389d00e81.jpg'),
(7, 'Kesemek', '', 2400.00, 2, 'Enak', '6852385a3fea8.jpg'),
(12, 'Mangga', '', 13000.00, 3, 'segarrrrnya mantavvv', '6852382bd9969.jpg'),
(13, 'Semangka', '', 13000.00, 3, 'segarrrr', '6852381f180a4.jpg'),
(14, 'Manggis', '', 7000.00, 20, 'kuatttt', '685234b88536e.jpg'),
(15, 'pisang', '', 9500.00, 5, 'manis dan besarr', '685231fe96eb4.jpg'),
(17, 'Keju', '', 35000000.00, 10, 'Hasil fermentasi 50 tahun', '685277894dea7.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, `created_at`) VALUES
(6, 1, 14, 6, 4, 'enak tapi agak kurang kualitasnya', '2025-06-18 02:45:04'),
(7, 1, 15, 6, 5, 'mantap maniss dan besar sesuai ekspektasi', '2025-06-18 03:20:34'),
(8, 17, 6, 9, 5, 'segarrrr banget', '2025-06-18 08:11:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `photo`, `phone`, `address`) VALUES
(1, 'Cecilia', 'soul@gmail.com', '$2y$10$zT0j7Hz2yOEayJOT7Ckxnefpobh0.vV/ZJhYMBIJeruYs6HgjL5EC', '2025-06-06 19:38:25', '6849ecbc034c4.png', '08914562367', 'anomali jaya'),
(5, 'aadllla', 'apaja@gmail.com', '$2y$10$Pv0B34KUCLv4gNOcOiW1q.UbApT9k5hYMIQslU/8DOn9rziRZkgEa', '2025-06-06 19:45:23', '', '', ''),
(6, 'beyonn', 'ftdian0@gmail.com', '$2y$10$KTwDXLXamn8DongzQNPC6.HkpmknocOmESgz458MBfooc3w5GHtti', '2025-06-07 03:49:09', '', '', ''),
(7, 'opp', 'fgfg@gmail.com', '$2y$10$.gLMyf51v5Vhbss0dSqy9udXQ7BGEKG1sGgooRRHD0bU/nHfc4lNG', '2025-06-07 04:34:03', '', '', ''),
(11, 'kkk', 'sdf@gmail.com', '$2y$10$4gAV4n07OaJASodO5Fj2Vu1HqNF0a7E97sxoy2x9f1lWfrIxiFcvK', '2025-06-07 04:45:07', '', '', ''),
(12, 'biyy', 'adgty@gmail.com', '$2y$10$Xd9fKprvUWu9pkO.QgHVAuicdmr266phvYWXM2toYOwaOykFgn4sK', '2025-06-08 17:43:52', '', '', ''),
(15, 'devi', 'devija@gmail.com', '$2y$10$ndF1g7gReQ6zhNpwDCx45uy3G8n1MTcPC3s5c04Lex99PzjGd7Zg2', '2025-06-18 04:18:21', '68523e6a0d745.png', '', ''),
(17, 'val', 'vall@gmail.com', '$2y$10$xF5lDcKPa3YkacN97qHa6eAEIHqQdCi2RSd5kES40B.kkA/JBiXu2', '2025-06-18 07:31:41', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `nama_admin` (`username`);

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Ketidakleluasaan untuk tabel `order_status`
--
ALTER TABLE `order_status`
  ADD CONSTRAINT `order_status_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Ketidakleluasaan untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `testimonials_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
