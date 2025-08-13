<?php
session_start();

if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'];
    $soluong = max(1, intval($_POST['soluong']));

    if (isset($_SESSION['giohang'][$key])) {
        $_SESSION['giohang'][$key]['soluong'] = $soluong;

        $gia = $_SESSION['giohang'][$key]['gia'];
        $thanhtien = $soluong * $gia;

        // Cập nhật vào bảng giohang_tam nếu người dùng đã đăng nhập
        if (isset($_SESSION['MaKH'])) {
            $makh = $_SESSION['MaKH'];
            list($masp, $mamau, $masize) = explode('_', $key);

            $conn = new mysqli("localhost", "root", "", "h_a");
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("UPDATE giohang_tam SET SoLuong = ? WHERE MaKH = ? AND MaSP = ? AND MaMau = ? AND MaSize = ?");
                $stmt->bind_param("iisss", $soluong, $makh, $masp, $mamau, $masize);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
        }

        // Tính lại tổng tiền
        $tong = 0;
        foreach ($_SESSION['giohang'] as $sp) {
            $tong += $sp['gia'] * $sp['soluong'];
        }

        echo json_encode([
            'success' => true,
            'thanhtien' => number_format($thanhtien, 0, ',', '.') . ' đ',
            'tong' => number_format($tong, 0, ',', '.') . ' đ'
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
