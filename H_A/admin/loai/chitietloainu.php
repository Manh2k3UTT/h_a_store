<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$sql = "SELECT * FROM chitietloainu";
$result = mysqli_query($conn, $sql);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chi tiết loại sản phẩm nữ</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>Danh sách chi tiết loại sản phẩm nữ</h2>

<table>
    <thead>
        <tr>
            <th>Mã Chi Tiết</th>
            <th>Tên Chi Tiết</th>
            <th>Mã Loại</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['MaChiTiet']); ?></td>
            <td><?php echo htmlspecialchars($row['TenChiTiet']); ?></td>
            <td><?php echo htmlspecialchars($row['MaLoai']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
