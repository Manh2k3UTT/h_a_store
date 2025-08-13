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
// Truy vấn dữ liệu với JOIN để lấy LoaiSanPham từ chitietloainu
$sql = "SELECT s.MaSP, s.TenSanPham, s.Gia, s.MoTa, c.TenChiTiet AS LoaiSanPham
        FROM sanphamnu s
        LEFT JOIN chitietloainu c ON s.MaChiTiet = c.MaChiTiet";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm nữ</title>
    <style>
        table { width: 90%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        .top-button { width: 90%; margin: 20px auto 0 auto; text-align: center; }
        button { padding: 6px 12px; cursor: pointer; }
    </style>
</head>
<body>

<h2 style="text-align: center;">Danh sách sản phẩm nữ</h2>

<!-- Nút Thêm ở trên -->
<div class="top-button">
    <a href="themsanphamnu.php"><button>Thêm sản phẩm</button></a>
</div>

<table>
    <thead>
        <tr>
            <th>MaSP</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Loại sản phẩm</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['MaSP']); ?></td>
            <td><?php echo htmlspecialchars($row['TenSanPham']); ?></td>
            <td><?php echo number_format($row['Gia']) . ' đ'; ?></td>
            <td><?php echo htmlspecialchars($row['LoaiSanPham']); ?></td>
            <td><?php echo htmlspecialchars($row['MoTa']); ?></td>
            <td>
                <a href="suasanphamnu.php?id=<?php echo urlencode($row['MaSP']); ?>"><button>Sửa</button></a>
                <a href="xoasanphamnu.php?id=<?php echo urlencode($row['MaSP']); ?>" onclick="return confirm('Bạn có chắc muốn xóa?');"><button>Xóa</button></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
