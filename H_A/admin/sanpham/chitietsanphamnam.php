<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

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

// Lấy mã sản phẩm từ tham số GET
$masp = $_GET['masp'] ?? '';

if ($masp == '') {
    echo "Không có mã sản phẩm!";
    exit;
}

// Truy vấn chi tiết sản phẩm theo mã sản phẩm
$sql = "SELECT * FROM chitietsanphamnam WHERE MaSP = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $masp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Không tìm thấy chi tiết sản phẩm.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - <?php echo htmlspecialchars($masp); ?></title>
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
            width: 40px;
            height: 30px;
            object-fit: cover;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Chi tiết sản phẩm - Mã sản phẩm: <?php echo htmlspecialchars($masp); ?></h2>

<table>
    <thead>
        <tr>
            <th>Mã sản phẩm</th>
            <th>Hình ảnh</th>
            <th>Màu</th>
            <th>Kích cỡ</th>
            <th>Số lượng</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['MaSP']); ?></td>
                <td>
                    <img src="../../webroot/images/sanpham/<?php echo htmlspecialchars($row['HinhAnh']); ?>" alt="Ảnh">
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
