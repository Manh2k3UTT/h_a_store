<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$database = "h_a";

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
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
if (isset($_GET['id'])) {
    $masp = $_GET['id'];

    // 1. Lấy danh sách ảnh cần xóa từ bảng chitietsanphamnu
    $sql_images = "SELECT HinhAnh FROM chitietsanphamnu WHERE MaSP = ?";
    $stmt_images = $conn->prepare($sql_images);
    $stmt_images->bind_param("i", $masp);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();

    while ($row = $result_images->fetch_assoc()) {
        $file = "../../webroot/images/sanpham/" . $row['HinhAnh'];
        if (!empty($row['HinhAnh']) && file_exists($file)) {
            unlink($file); // Xóa ảnh nếu tồn tại
        }
    }
    $stmt_images->close();

    // 2. Xóa chi tiết sản phẩm
    $sql_delete_detail = "DELETE FROM chitietsanphamnu WHERE MaSP = ?";
    $stmt_delete_detail = $conn->prepare($sql_delete_detail);
    $stmt_delete_detail->bind_param("i", $masp);
    $stmt_delete_detail->execute();
    $stmt_delete_detail->close();

    // 3. Xóa sản phẩm chính
    $sql_delete_product = "DELETE FROM sanphamnu WHERE MaSP = ?";
    $stmt_delete_product = $conn->prepare($sql_delete_product);
    $stmt_delete_product->bind_param("i", $masp);
    $stmt_delete_product->execute();
    $stmt_delete_product->close();

    // 4. Chuyển hướng về trang danh sách
    header("Location: sanphamnu.php");
    exit();
} else {
    echo "Không tìm thấy mã sản phẩm để xóa.";
}

$conn->close();
?>
