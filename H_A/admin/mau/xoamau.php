<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include '../include/header.php';

$maMau = $_GET['id'] ?? '';
if (!$maMau) {
    echo "<script>alert('Không tìm thấy mã màu.'); window.location='mau.php';</script>";
    exit;
}

// Danh sách các bảng có thể chứa MaMau
$tables = ['chitietsanphamnam', 'chitietsanphamnu', 'phieunhap', 'phieuxuat'];
$isUsed = false;
$usedInTables = [];

foreach ($tables as $table) {
    $checkCol = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE 'MaMau'");
    if (mysqli_num_rows($checkCol) > 0) {
        $query = "SELECT COUNT(*) AS total FROM `$table` WHERE MaMau = '$maMau'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['total'] > 0) {
            $isUsed = true;
            $usedInTables[] = $table;
        }
    }
}

if ($isUsed) {
    $tablesStr = implode(', ', $usedInTables);
    echo "<script>alert('Không thể xóa màu \"$maMau\" vì đang được sử dụng trong: $tablesStr.'); window.location='mau.php';</script>";
} else {
    mysqli_query($conn, "DELETE FROM mau WHERE MaMau = '$maMau'");
    echo "<script>alert('Xóa màu \"$maMau\" thành công.'); window.location='mau.php';</script>";
}
?>
