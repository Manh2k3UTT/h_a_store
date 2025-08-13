<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header("Location: ../login.php");
    exit;
}

$manv = $_SESSION['manv'];
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Xử lý nhập hàng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $soluongs = $_POST['soluong'] ?? [];
    $notes = $_POST['note'] ?? [];

    foreach ($soluongs as $key => $soluong_nhap) {
        if (is_numeric($soluong_nhap) && $soluong_nhap > 0) {
            list($masp, $masize, $mamau) = explode('_', $key);

            $sql_update = "UPDATE chitietsanphamnam SET SoLuong = SoLuong + ? WHERE MaSP = ? AND MaSize = ? AND MaMau = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, "isss", $soluong_nhap, $masp, $masize, $mamau);
            mysqli_stmt_execute($stmt);

            $sql_gia = "SELECT Gia FROM sanphamnam WHERE MaSP = ?";
            $stmt_gia = mysqli_prepare($conn, $sql_gia);
            mysqli_stmt_bind_param($stmt_gia, "s", $masp);
            mysqli_stmt_execute($stmt_gia);
            $result_gia = mysqli_stmt_get_result($stmt_gia);
            $gia = mysqli_fetch_assoc($result_gia)['Gia'] ?? 0;

            $tongtien = $gia * $soluong_nhap;
            $ngaynhap = date('Y-m-d');
            $note = $notes[$key] ?? "";

            $sql_insert = "INSERT INTO phieunhap (MaSP, MaNV, SoLuong, NgayNhap, Size, Mau, TongTien, Note)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "ssisssds", $masp, $manv, $soluong_nhap, $ngaynhap, $masize, $mamau, $tongtien, $note);
            mysqli_stmt_execute($stmt_insert);
        }
    }

    header("Location: kho.php");
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập hàng</title>
    <link rel="stylesheet" href="../template/css/sb-admin-2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            text-align: center;
        }
        h3 {
            margin-top: 30px;
            margin-bottom: 20px;
            color: #333;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        table {
            margin: auto;
            width: 95%;
            border-collapse: separate;
            border-spacing: 0 10px; /* Giãn dòng theo chiều dọc */
            border: 1px solid #ccc; /* Viền ngoài */
        }
        table th, table td {
            vertical-align: middle !important;
            text-align: center;
            border: 1px solid #ccc; /* Viền các ô */
            padding: 10px 12px; /* Giãn cách trong ô */
            background-color: #fff;
        }
        table thead th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        table img {
            border-radius: 6px;
            width: 60px;
            height: auto;
        }
        input.form-control {
            text-align: center;
            margin: auto;
            max-width: 80px;
        }
        input[type="text"].form-control {
            max-width: 120px;
        }
        .text-center {
            margin-top: 20px;
        }
        .btn {
            margin: 10px 15px;
        }
    </style>

</head>
<body>
<div class="container">
    <h3>Nhập hàng - Sản phẩm nam</h3>
    <form method="post">
        <table class="table table-bordered bg-white">
            <thead class="thead-light">
                <tr>
                    <th>Hình ảnh</th>
                    <th>Mã SP</th>
                    <th>Size</th>
                    <th>Màu</th>
                    <th>Số lượng hiện tại</th>
                    <th>Nhập thêm</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $masp = $_GET['masp'] ?? '';
                if ($masp !== '') {
                    $stmt = mysqli_prepare($conn, "SELECT * FROM chitietsanphamnam WHERE MaSP = ?");
                    mysqli_stmt_bind_param($stmt, "s", $masp);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                } else {
                    $result = mysqli_query($conn, "SELECT * FROM chitietsanphamnam");
                }

                while ($row = mysqli_fetch_assoc($result)):
                    $key = $row['MaSP'] . "_" . $row['MaSize'] . "_" . $row['MaMau'];
                ?>
                <tr>
                    <td><img src="../../webroot/images/sanpham/<?= htmlspecialchars($row['HinhAnh']) ?>" alt="Ảnh"></td>
                    <td><?= htmlspecialchars($row['MaSP']) ?></td>
                    <td><?= htmlspecialchars($row['MaSize']) ?></td>
                    <td><?= htmlspecialchars($row['MaMau']) ?></td>
                    <td><?= htmlspecialchars($row['SoLuong']) ?></td>
                    <td><input type="number" name="soluong[<?= $key ?>]" class="form-control" min="0"></td>
                    <td><input type="text" name="note[<?= $key ?>]" class="form-control"></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="text-center">
            <button type="submit" name="submit" class="btn btn-success">Nhập</button>
            <a href="kho.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
