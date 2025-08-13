<?php
include("../../model/database.php");

if (isset($_GET['id'])) {
    $madon = intval($_GET['id']);

    // Kiểm tra đơn hàng có tồn tại và ở trạng thái chờ xác nhận không
    $stmt = $conn->prepare("SELECT TrangThai FROM donhang WHERE MaDonHang = ?");
    $stmt->bind_param("i", $madon);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['TrangThai'] === 'Chờ xác nhận') {
        // Cập nhật trạng thái đơn hàng
        $update = $conn->prepare("UPDATE donhang SET TrangThai = 'Đã xác nhận' WHERE MaDonHang = ?");
        $update->bind_param("i", $madon);
        $update->execute();

        header("Location: donhang.php");
        exit();
    } else {
        echo "Đơn hàng không tồn tại hoặc không thể xác nhận.";
    }
} else {
    echo "Thiếu mã đơn hàng.";
}
?>
