<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}

$host = 'localhost';
$dbname = 'h_a';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}

// Lấy mã loại
$maLoai = $_GET['id'] ?? null;

if ($maLoai) {
    $stmt = $conn->prepare("DELETE FROM loainam WHERE MaLoai = :maloai");
    $stmt->execute(['maloai' => $maLoai]);

    header("Location: loainam.php");
    exit;
} else {
    echo "Thiếu mã loại.";
}
