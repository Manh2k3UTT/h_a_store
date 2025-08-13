<?php
session_start();
include("../../model/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');
    $pttt = $_POST['pttt'] ?? 'Thanh toán khi nhận hàng';

    if ($ten === '' || $sdt === '' || $diachi === '') {
        echo "Vui lòng nhập đầy đủ thông tin người nhận";
        exit();
    }

    if (!isset($_SESSION['giohang']) || empty($_SESSION['giohang'])) {
        echo "Giỏ hàng đang trống";
        exit();
    }

    if (!isset($_SESSION['MaKH'])) {
        echo "Vui lòng đăng nhập trước khi đặt hàng.";
        exit();
    }

    $makh = $_SESSION['MaKH'];
    $ngaydat = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $tongtien = 0;
    $gia_km_arr = [];

    // Tính tổng tiền có áp dụng khuyến mãi
    foreach ($_SESSION['giohang'] as $key => $item) {
        $gia = $item['gia'];
        $masp = $item['masp'];

        $km_query = mysqli_query($conn, "
            SELECT km.*
            FROM sanpham_khuyenmai spkm
            JOIN khuyenmai km ON spkm.MaKM = km.MaKM
            WHERE spkm.MaSP = '$masp'
              AND km.NgayBatDau <= '$today'
              AND km.NgayKetThuc >= '$today'
            LIMIT 1
        ");

        $giaKM = $gia;
        if ($km_query && mysqli_num_rows($km_query) > 0) {
            $km = mysqli_fetch_assoc($km_query);
            if (!is_null($km['KM_PT']) && $km['KM_PT'] > 0) {
                $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
            } elseif (!is_null($km['TienKM']) && $km['TienKM'] > 0) {
                $giaKM = $gia - $km['TienKM'];
            }
            if ($giaKM < 0) $giaKM = 0;
        }

        $gia_km_arr[$key] = $giaKM;
        $tongtien += $giaKM * $item['soluong'];
    }

    // Thêm đơn hàng (có MaKH)
    $sql_donhang = "INSERT INTO donhang (MaKH, TenNguoiNhan, SDT, DiaChi, NgayDat, TongTien, TrangThai, PhuongThucThanhToan)
                    VALUES (?, ?, ?, ?, ?, ?, 'Chờ xác nhận', ?)";
    $stmt = $conn->prepare($sql_donhang);
    $stmt->bind_param("issssds", $makh, $ten, $sdt, $diachi, $ngaydat, $tongtien, $pttt);
    $stmt->execute();

    $madonhang = $conn->insert_id;

    // Chuẩn bị thêm chi tiết đơn hàng
    $sql_ct = "INSERT INTO chitietdonhang (MaDonHang, MaSP, MaMau, MaSize, SoLuong, DonGia)
               VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_ct = $conn->prepare($sql_ct);

    // Chuẩn bị cập nhật số lượng tồn
    $sql_update_sl = "UPDATE chitietsanphamnam 
                      SET SoLuong = SoLuong - ? 
                      WHERE MaSP = ? AND MaMau = ? AND MaSize = ?";
    $stmt_update = $conn->prepare($sql_update_sl);

    foreach ($_SESSION['giohang'] as $key => $item) {
        $masp = $item['masp'];
        $mau = $item['mau'];
        $size = $item['size'];
        $soluong = $item['soluong'];
        $dongia = $gia_km_arr[$key];

        $stmt_ct->bind_param("iisssd", $madonhang, $masp, $mau, $size, $soluong, $dongia);
        $stmt_ct->execute();

        $stmt_update->bind_param("iiss", $soluong, $masp, $mau, $size);
        $stmt_update->execute();
    }

    // ❗ Xóa giỏ hàng tạm trong database (nếu có)
    $conn->query("DELETE FROM giohang_tam WHERE MaKH = '$makh'");

    // Xóa giỏ hàng session
    unset($_SESSION['giohang']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <script>
        setTimeout(function () {
            window.location.href = '../index.php';
        }, 3000);
    </script>
</head>
<body>
    <h2 style="color: green; text-align: center; margin-top: 100px;">
        Đặt hàng thành công! Bạn sẽ nhận hàng và thanh toán khi giao.<br>
        <small>Đang chuyển về trang chủ...</small>
    </h2>
</body>
</html>
