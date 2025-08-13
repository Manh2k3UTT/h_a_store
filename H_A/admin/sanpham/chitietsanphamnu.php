<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$sql = "SELECT * FROM chitietsanphamnu";
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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm nữ</title>
    <style>
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        img {
            width: 40px; /* Chỉnh kích thước ảnh */
            height: 30px; /* Chỉnh kích thước ảnh */
            object-fit: cover; /* Đảm bảo ảnh không bị méo, giữ tỉ lệ */
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Chi tiết sản phẩm nữ</h2>

<table>
    <thead>
        <tr>
            <th>MaSP</th>
            <th>Hình ảnh</th>
            <th>Màu</th>
            <th>Kích cỡ</th>
            <th>Số lượng</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['MaSP']; ?></td>
                <td>
                    <img src="C:/xampp/htdocs/H_A/webroot/images/sanpham/<?php echo htmlspecialchars($row['HinhAnh']); ?>" alt="Ảnh">
                </td>
                <td><?php echo htmlspecialchars($row['MaMau']); ?></td>
                <td><?php echo htmlspecialchars($row['MaSize']); ?></td>
                <td><?php echo $row['SoLuong']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
