<?php
session_start();
session_destroy();
header("Location: login.php"); // Atau index.php kalau ingin diarahkan ke halaman utama
exit();
