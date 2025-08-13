<?php
session_start();
require_once('../../model/database.php'); // Kết nối DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masp = $_POST['masp'] ?? null;
    $mau = $_POST['mau'] ?? null;
    $size = $_POST['size'] ?? null;
    $soluong = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 1;
    $action = $_POST['action'] ?? 'add'; // Giá trị là 'add' hoặc 'buy'

    if (!$masp || !$mau || !$size || $soluong <= 0) {
        header("Location: xemsanphamnam.php?id=$masp");
        exit;
    }

    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['giohang'])) {
        $_SESSION['giohang'] = [];
    }

    $key = $masp . '_' . $mau . '_' . $size;

    if (isset($_SESSION['giohang'][$key])) {
        $_SESSION['giohang'][$key]['soluong'] += $soluong;
    } else {
        $sp_query = mysqli_query($conn, "SELECT TenSanPham, Gia FROM sanphamnam WHERE MaSP = '$masp'");
        $sp = mysqli_fetch_assoc($sp_query);

        $hinh_query = mysqli_query($conn, "SELECT HinhAnh FROM chitietsanphamnam WHERE MaSP = '$masp' AND MaMau = '$mau' LIMIT 1");
        $hinh = mysqli_fetch_assoc($hinh_query);

        $_SESSION['giohang'][$key] = [
            'masp' => $masp,
            'ten' => $sp['TenSanPham'],
            'gia' => $sp['Gia'],
            'mau' => $mau,
            'size' => $size,
            'soluong' => $soluong,
            'hinh' => $hinh['HinhAnh'] ?? 'noimage.jpg'
        ];
    }

    // ✅ Lưu vào bảng giohang_tam nếu người dùng đã đăng nhập
    if (isset($_SESSION['MaKH'])) {
        $makh = $_SESSION['MaKH'];

        // Kiểm tra nếu đã tồn tại thì cộng số lượng
        $sql = "INSERT INTO giohang_tam (MaKH, MaSP, MaMau, MaSize, SoLuong)
                VALUES ('$makh', '$masp', '$mau', '$size', $soluong)
                ON DUPLICATE KEY UPDATE SoLuong = SoLuong + $soluong";
        mysqli_query($conn, $sql);
    }

    // Phân biệt hành động
    if ($action === 'buy') {
        header("Location: thanhtoan.php");
        exit;
    } else {
        header("Location: giohang.php");
        exit;
    }
} else {
    header("Location: ../sanpham/thoitrangnam.php");
    exit;
}
