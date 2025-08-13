<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2, 4])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include("../include/header.php");
include("../../model/database.php");

// Xử lý xác nhận đơn hàng
if (isset($_GET['xacnhan'])) {
    $madon = intval($_GET['xacnhan']);

    // Cập nhật trạng thái đơn hàng
    $sql_update = "UPDATE donhang SET TrangThai = 'Đã xác nhận' WHERE MaDonHang = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("i", $madon);
    $stmt->execute();

    // Thêm vào bảng giao hàng
    $sql_insert = "INSERT INTO giaohang (MaDonHang, TrangThai) VALUES (?, 'Đang giao hàng')";
    $stmt2 = $conn->prepare($sql_insert);
    $stmt2->bind_param("i", $madon);
    $stmt2->execute();
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM donhang ORDER BY NgayDat DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <style>
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }

        .btn-xacnhan {
            background-color: green;
            color: white;
        }

        .btn-chitiet {
            background-color: #2196F3;
            color: white;
        }

        .disabled {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Danh sách đơn hàng</h2>

<table>
    <tr>
        <th>Mã đơn</th>
        <th>Người nhận</th>
        <th>SĐT</th>
        <th>Địa chỉ</th>
        <th>Ngày đặt</th>
        <th>Phương thức</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['MaDonHang'] ?></td>
        <td><?= htmlspecialchars($row['TenNguoiNhan']) ?></td>
        <td><?= htmlspecialchars($row['SDT']) ?></td>
        <td><?= htmlspecialchars($row['DiaChi']) ?></td>
        <td><?= $row['NgayDat'] ?></td>
        <td><?= $row['PhuongThucThanhToan'] ?></td>
        <td><?= number_format($row['TongTien']) ?> đ</td>
        <td><?= $row['TrangThai'] ?></td>
        <td>
            <?php if ($row['TrangThai'] == 'Chờ xác nhận'): ?>
                <a href="?xacnhan=<?= $row['MaDonHang'] ?>" class="btn btn-xacnhan" onclick="return confirm('Xác nhận đơn hàng này?')">Xác nhận</a>
            <?php else: ?>
                <button class="btn disabled" disabled>Đã xử lý</button>
            <?php endif; ?>
            <a href="chitietdonhang.php?madon=<?= $row['MaDonHang'] ?>" class="btn btn-chitiet">Xem chi tiết</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
