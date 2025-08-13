<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

// Lấy từ khóa tìm kiếm nếu có
$search = $_GET['search'] ?? '';
$search = mysqli_real_escape_string($conn, $search);

// Truy vấn dữ liệu với JOIN để lấy LoaiSanPham từ chitietloainam, thêm cột GioiTinh và điều kiện tìm kiếm
$sql = "SELECT s.MaSP, s.TenSanPham, s.Gia, s.MoTa, s.GioiTinh, c.TenChiTiet AS LoaiSanPham
        FROM sanphamnam s
        LEFT JOIN chitietloainam c ON s.MaChiTiet = c.MaChiTiet
        WHERE s.TenSanPham LIKE '%$search%'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm</title>
    <style>
        table { width: 90%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        .top-button { width: 90%; margin: 20px auto 0 auto; text-align: center; }
        .btn {
            padding: 6px 12px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: #fff;
        }
        .btn-add { background-color: #28a745; }      /* Xanh lá */
        .btn-edit { background-color: #007bff; }     /* Xanh dương */
        .btn-delete { background-color: #dc3545; }   /* Đỏ */
        .btn-view { background-color: #6c757d; }     /* Xám */
        input[type="text"] {
            padding: 6px;
            width: 300px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Danh sách sản phẩm</h2>

<!-- Thanh tìm kiếm -->
<div class="top-button">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Tìm kiếm tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-view">Tìm</button>
    </form>
</div>

<!-- Nút Thêm ở trên -->
<div class="top-button">
    <a href="themsanphamnam.php"><button class="btn btn-add">Thêm sản phẩm</button></a>
</div>

<table>
    <thead>
        <tr>
            <th>Mã sản phẩm</th>
            <th>Tên sản phẩm</th>
            <th>Giới tính</th>
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
            <td><?php echo htmlspecialchars($row['GioiTinh']); ?></td>
            <td><?php echo number_format($row['Gia']) . ' đ'; ?></td>
            <td><?php echo htmlspecialchars($row['LoaiSanPham']); ?></td>
            <td><?php echo htmlspecialchars($row['MoTa']); ?></td>
            <td>
                <a href="suasanphamnam.php?id=<?php echo urlencode($row['MaSP']); ?>"><button class="btn btn-edit">Sửa</button></a>
                <a href="xoasanphamnam.php?id=<?php echo urlencode($row['MaSP']); ?>" onclick="return confirm('Bạn có chắc muốn xóa?');"><button class="btn btn-delete">Xóa</button></a>
                <a href="chitietsanphamnam.php?masp=<?php echo urlencode($row['MaSP']); ?>"><button class="btn btn-view">Xem chi tiết</button></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
