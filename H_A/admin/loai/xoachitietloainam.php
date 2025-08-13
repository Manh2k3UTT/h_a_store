<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$maChiTiet = $_GET['id'] ?? '';

if ($maChiTiet == '') {
    // Nếu không có mã chi tiết thì quay lại trang danh sách
    header("Location: chitietloainam.php");
    exit;
}

// Chuẩn bị câu lệnh xóa
$sqlDelete = "DELETE FROM chitietloainam WHERE MaChiTiet = ?";
$stmtDelete = mysqli_prepare($conn, $sqlDelete);
mysqli_stmt_bind_param($stmtDelete, "s", $maChiTiet);

if (mysqli_stmt_execute($stmtDelete)) {
    // Xóa thành công, quay lại trang danh sách
    mysqli_stmt_close($stmtDelete);
    header("Location: chitietloainam.php");
    exit;
} else {
    // Xóa thất bại, có thể thêm xử lý lỗi ở đây
    mysqli_stmt_close($stmtDelete);
    echo "Lỗi khi xóa chi tiết loại sản phẩm: " . mysqli_error($conn);
}
?>
