<?php
session_start();
include('../../model/database.php'); // hoặc include đường dẫn database của bạn

if (isset($_GET['MaKM'])) {
    $makm = $_GET['MaKM'];

    // Xóa tất cả liên kết sản phẩm - khuyến mãi
    $sql1 = "DELETE FROM sanpham_khuyenmai WHERE MaKM = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $makm);
    $stmt1->execute();
    $stmt1->close();

    // Xoá khuyến mãi trong bảng khuyenmai
    $sql2 = "DELETE FROM khuyenmai WHERE MaKM = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $makm);
    $stmt2->execute();
    $stmt2->close();

    header("Location: khuyenmai.php?status=deleted");
    exit;
} else {
    echo "Thiếu thông tin MaKM.";
}
?>
