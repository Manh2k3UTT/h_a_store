<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2, 3])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$filter = $_GET['loai'] ?? 'tatca';
$sanpham = [];

if ($filter === 'tatca' || $filter === 'nam') {
    $query = "SELECT MaSP, TenSanPham, Gia, MaChiTiet, MoTa, 'Nam' AS GioiTinh FROM sanphamnam";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $sanpham[] = $row;
    }
}

if ($filter === 'tatca' || $filter === 'nu') {
    $query = "SELECT MaSP, TenSanPham, Gia, MaChiTiet, MoTa, 'Nữ' AS GioiTinh FROM sanphamnu";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $sanpham[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Kho Sản Phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 20px auto;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            text-align: right;
        }
        select {
            padding: 6px 10px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        td.center, th.center {
            text-align: center;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a.btn {
            display: inline-block;
            padding: 5px 10px;
            font-size: 13px;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
            color: white;
        }
        a.btn-nhap {
            background-color: #28a745;
        }
        a.btn-nhap:hover {
            background-color: #218838;
        }
        a.btn-xuat {
            background-color: #dc3545;
        }
        a.btn-xuat:hover {
            background-color: #c82333;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>

<h2>Kho Sản Phẩm</h2>

<!-- <form method="get">
    <label for="loai">Lọc theo: </label>
    <select id="loai" name="loai" onchange="this.form.submit()">
        <option value="tatca" <?= $filter == 'tatca' ? 'selected' : '' ?>>Tất cả</option>
        <option value="nam" <?= $filter == 'nam' ? 'selected' : '' ?>>Sản phẩm nam</option>
        <option value="nu" <?= $filter == 'nu' ? 'selected' : '' ?>>Sản phẩm nữ</option>
    </select>
</form> -->

<table>
    <thead>
        <tr>
            <th class="center">Mã SP</th>
            <th>Tên Sản Phẩm</th>
            <th class="center">Giá</th>
            <th>Mô Tả</th>
            <th class="center">Giới Tính</th>
            <th class="center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($sanpham) > 0): ?>
            <?php foreach ($sanpham as $sp): ?>
                <tr>
                    <td class="center"><?= htmlspecialchars($sp['MaSP']) ?></td>
                    <td><?= htmlspecialchars($sp['TenSanPham']) ?></td>
                    <td class="center"><?= number_format($sp['Gia'], 0, ',', '.') ?> đ</td>
                    
                    <td><?= htmlspecialchars($sp['MoTa']) ?></td>
                    <td class="center"><?= htmlspecialchars($sp['GioiTinh']) ?></td>
                    <td class="center">
                        <a class="btn btn-nhap" href="nhap.php?masp=<?= urlencode($sp['MaSP']) ?>&gioitinh=<?= strtolower($sp['GioiTinh']) ?>">Nhập</a>
                        <a class="btn btn-xuat" href="xuat.php?masp=<?= urlencode($sp['MaSP']) ?>&gioitinh=<?= strtolower($sp['GioiTinh']) ?>">Xuất</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="no-data">Không có sản phẩm phù hợp.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
