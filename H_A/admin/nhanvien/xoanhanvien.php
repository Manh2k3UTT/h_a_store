<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "h_a";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

// Kiểm tra nếu có id
if (isset($_GET['id'])) {
    $maNV = $_GET['id'];

    // Xóa nhân viên theo MaNV
    $sql = "DELETE FROM nhanvien WHERE MaNV = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $maNV);

    if (mysqli_stmt_execute($stmt)) {
        // ✅ Chuyển hướng về trang nhanvien.php sau khi xóa thành công
        header("Location: nhanvien.php");
        exit();
    } else {
        echo "Lỗi khi xóa nhân viên: " . mysqli_error($conn);
    }
} else {
    echo "ID nhân viên không hợp lệ.";
}

?>
