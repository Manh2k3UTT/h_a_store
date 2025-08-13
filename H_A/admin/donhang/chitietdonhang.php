<?php
session_start();
include("../../model/database.php");

if (!isset($_GET['madon'])) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}

$madon = intval($_GET['madon']);

// Lấy thông tin đơn hàng
$sql_donhang = "SELECT * FROM donhang WHERE MaDonHang = ?";
$stmt_donhang = $conn->prepare($sql_donhang);
$stmt_donhang->bind_param("i", $madon);
$stmt_donhang->execute();
$result_donhang = $stmt_donhang->get_result();
$donhang = $result_donhang->fetch_assoc();

if (!$donhang) {
    echo "Đơn hàng không tồn tại.";
    exit;
}

// Lấy chi tiết đơn hàng
$sql_ct = "
    SELECT ct.*, sp.TenSanPham
    FROM chitietdonhang ct
    JOIN sanphamnam sp ON ct.MaSP = sp.MaSP
    WHERE ct.MaDonHang = ?
";

$stmt_ct = $conn->prepare($sql_ct);
$stmt_ct->bind_param("i", $madon);
$stmt_ct->execute();
$result_ct = $stmt_ct->get_result();

$tennv = $_SESSION['tennv'] ?? 'Người dùng';

include("../include/header.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $madon ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h2, p {
            margin-bottom: 5px;
        }
        .btn-back {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 14px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="donhang.php" class="btn-back">← Quay lại</a>

    <h2>Chi tiết đơn hàng #<?= $madon ?></h2>

    <p><strong>Người nhận:</strong> <?= htmlspecialchars($donhang['TenNguoiNhan']) ?></p>
    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donhang['DiaChi']) ?></p>
    <p><strong>SĐT:</strong> <?= htmlspecialchars($donhang['SDT']) ?></p>
    <p><strong>Ngày đặt:</strong> <?= $donhang['NgayDat'] ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= $donhang['PhuongThucThanhToan'] ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($donhang['TongTien']) ?> đ</p>

    <h3>Sản phẩm trong đơn hàng:</h3>
    <table>
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_ct->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['TenSanPham']) ?></td>
                <td><?= htmlspecialchars($row['MaMau']) ?></td>
                <td><?= htmlspecialchars($row['MaSize']) ?></td>
                <td><?= $row['SoLuong'] ?></td>
                <td><?= number_format($row['DonGia']) ?> đ</td>
                <td><?= number_format($row['SoLuong'] * $row['DonGia']) ?> đ</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
