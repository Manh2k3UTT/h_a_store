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

$masp = $_GET['masp'] ?? '';

$sql = "SELECT ctn.*, sp.Gia, sp.TenSanPham FROM chitietsanphamnam ctn 
        JOIN sanphamnam sp ON ctn.MaSP = sp.MaSP
        WHERE ctn.MaSP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $masp);
$stmt->execute();
$result = $stmt->get_result();

// Xử lý khi nhấn nút Xuất
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['soluong'] as $key => $soluongxuat) {
        $soluongxuat = (int)$soluongxuat;
        if ($soluongxuat <= 0) continue;

        list($masp, $mamau, $masize) = explode('_', $key);

        $check_sql = "SELECT SoLuong FROM chitietsanphamnam WHERE MaSP=? AND MaMau=? AND MaSize=?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("sss", $masp, $mamau, $masize);
        $stmt->execute();
        $check_result = $stmt->get_result();
        $row = $check_result->fetch_assoc();
        $current = $row['SoLuong'];

        if ($current >= $soluongxuat) {
            $update_sql = "UPDATE chitietsanphamnam SET SoLuong = SoLuong - ? 
                           WHERE MaSP = ? AND MaMau = ? AND MaSize = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("isss", $soluongxuat, $masp, $mamau, $masize);
            $stmt->execute();

            $gia_sql = "SELECT Gia FROM sanphamnam WHERE MaSP=?";
            $stmt = $conn->prepare($gia_sql);
            $stmt->bind_param("s", $masp);
            $stmt->execute();
            $gia_result = $stmt->get_result();
            $gia = $gia_result->fetch_assoc()['Gia'];

            $tongtien = $gia * $soluongxuat;
            $ngayxuat = date("Y-m-d");
            $note = $_POST['note'][$key] ?? '';

            $insert_sql = "INSERT INTO phieuxuat (MaSP, MaNV, SoLuong, NgayXuat, Size, Mau, TongTien, Note)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssisssis", $masp, $manv, $soluongxuat, $ngayxuat, $masize, $mamau, $tongtien, $note);
            $stmt->execute();
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
    <title>Xuất sản phẩm</title>
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
<div class="container mt-5">
    <h3>Xuất hàng - Mã sản phẩm <?= htmlspecialchars($masp) ?></h3>
    <form method="post" class="w-100">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>Mã SP</th>
                    <th>Tên SP</th>
                    <th>Ảnh</th>
                    <th>Màu</th>
                    <th>Size</th>
                    <th>Số lượng hiện tại</th>
                    <th>Số lượng xuất</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $key = $row['MaSP'] . "_" . $row['MaMau'] . "_" . $row['MaSize'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaSP']) ?></td>
                    <td><?= htmlspecialchars($row['TenSanPham']) ?></td>
                    <td><img src="../../webroot/images/sanpham/<?= htmlspecialchars($row['HinhAnh']) ?>" alt=""></td>
                    <td><?= htmlspecialchars($row['MaMau']) ?></td>
                    <td><?= htmlspecialchars($row['MaSize']) ?></td>
                    <td><?= $row['SoLuong'] ?></td>
                    <td><input type="number" name="soluong[<?= $key ?>]" class="form-control" min="0" max="<?= $row['SoLuong'] ?>"></td>
                    <td><input type="text" name="note[<?= $key ?>]" class="form-control"></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="btn-center mt-4">
            <button type="submit" class="btn btn-danger">Xuất</button>
            <a href="kho.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
