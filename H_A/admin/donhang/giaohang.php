<?php
include("../../model/database.php");

if (isset($_GET['id'])) {
    $madon = intval($_GET['id']);

    // Kiểm tra trạng thái đơn hàng
    $check = $conn->prepare("SELECT TrangThai FROM donhang WHERE MaDonHang = ?");
    $check->bind_param("i", $madon);
    $check->execute();
    $result = $check->get_result()->fetch_assoc();

    if ($result && $result['TrangThai'] === 'Đã xác nhận') {
        // Thêm vào bảng giao hàng
        $insert = $conn->prepare("INSERT INTO giaohang (MaDonHang, TrangThai) VALUES (?, 'Đang giao hàng')");
        $insert->bind_param("i", $madon);
        $insert->execute();

        // Giữ trạng thái đơn hàng là 'Đã xác nhận'
        header("Location: donhang.php");
        exit();
    } else {
        echo "Chỉ có thể giao hàng cho đơn đã xác nhận.";
    }
} else {
    echo "Thiếu mã đơn hàng.";
}
?>
