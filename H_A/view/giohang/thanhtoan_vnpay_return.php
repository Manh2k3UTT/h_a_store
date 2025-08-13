<?php
session_start();
include("../../model/database.php");

// Lấy thông tin từ VNPAY trả về
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
$vnp_Amount = $_GET['vnp_Amount'] ?? 0;

if ($vnp_ResponseCode === '00') {
    // ✅ Thanh toán thành công

    if (!isset($_SESSION['giohang']) || empty($_SESSION['giohang'])) {
        echo "Không tìm thấy giỏ hàng.";
        exit;
    }

    if (!isset($_SESSION['MaKH'])) {
        echo "Vui lòng đăng nhập trước khi đặt hàng.";
        exit;
    }

    $makh = $_SESSION['MaKH'];
    $ten = $_SESSION['ten'];
    $sdt = $_SESSION['sdt'];
    $diachi = $_SESSION['diachi'];
    $pttt = "VNPay";
    $ngaydat = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $tongtien = 0;
    $gia_km_arr = [];

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

    // Thêm đơn hàng
    $sql_donhang = "INSERT INTO donhang (MaKH, TenNguoiNhan, SDT, DiaChi, NgayDat, TongTien, TrangThai, PhuongThucThanhToan)
                    VALUES (?, ?, ?, ?, ?, ?, 'Chờ xác nhận', ?)";
    $stmt = $conn->prepare($sql_donhang);
    $stmt->bind_param("issssds", $makh, $ten, $sdt, $diachi, $ngaydat, $tongtien, $pttt);
    $stmt->execute();
    $madonhang = $conn->insert_id;

    // Thêm chi tiết đơn hàng + trừ kho
    $sql_ct = "INSERT INTO chitietdonhang (MaDonHang, MaSP, MaMau, MaSize, SoLuong, DonGia)
               VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_ct = $conn->prepare($sql_ct);

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

    // Xoá giỏ hàng tạm
    $conn->query("DELETE FROM giohang_tam WHERE MaKH = '$makh'");

    // Xoá session
    unset($_SESSION['giohang']);
    unset($_SESSION['ten']);
    unset($_SESSION['sdt']);
    unset($_SESSION['diachi']);
    ?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Thanh toán thành công</title>
        <script>
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 3000);
        </script>
    </head>
    <body>
        <h2 style="color: green; text-align: center; margin-top: 100px;">
            ✅ Thanh toán qua VNPay thành công! Đơn hàng đã được ghi nhận.<br>
            <small>Đang chuyển về trang chủ...</small>
        </h2>
    </body>
    </html>

    <?php
} else {
    echo "<h2 style='color:red; text-align:center; margin-top:100px;'>❌ Thanh toán thất bại hoặc bị huỷ.</h2>";
}
?>
