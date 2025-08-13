<?php
include("../../model/database.php");

if (isset($_POST['magh']) && isset($_POST['trangthai'])) {
    $magh = intval($_POST['magh']);
    $trangthai = $_POST['trangthai'];

    // Lấy mã đơn hàng
    $stmt = $conn->prepare("SELECT MaDonHang FROM giaohang WHERE MaGiaoHang = ?");
    $stmt->bind_param("i", $magh);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $madon = $res['MaDonHang'];

    // Cập nhật trạng thái giao hàng
    $update = $conn->prepare("UPDATE giaohang SET TrangThai = ? WHERE MaGiaoHang = ?");
    $update->bind_param("si", $trangthai, $magh);
    $update->execute();

    // Cập nhật trạng thái đơn hàng
    $capnhat_donhang = $conn->prepare("UPDATE donhang SET TrangThai = ? WHERE MaDonHang = ?");
    $capnhat_donhang->bind_param("si", $trangthai, $madon);
    $capnhat_donhang->execute();

    header("Location: list_giaohang.php");
    exit();
}
?>
