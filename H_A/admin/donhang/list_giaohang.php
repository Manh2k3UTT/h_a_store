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
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2, 5])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include("../include/header.php");
include("../../model/database.php");

// Cập nhật trạng thái giao hàng và ngày cập nhật
if (isset($_GET['giaohang']) && isset($_GET['trangthai'])) {
    $madon = intval($_GET['giaohang']);
    $trangthai = $_GET['trangthai'];

    if (in_array($trangthai, ['Đã giao hàng', 'Hủy'])) {
        // Cập nhật bảng giao hàng
        $sql1 = "UPDATE giaohang SET TrangThai = ?, NgayCapNhat = NOW() WHERE MaDonHang = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("si", $trangthai, $madon);
        $stmt1->execute();

        // Cập nhật bảng đơn hàng
        $sql2 = "UPDATE donhang SET TrangThai = ? WHERE MaDonHang = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("si", $trangthai, $madon);
        $stmt2->execute();
    }
}

// Lấy danh sách giao hàng
$sql = "SELECT gh.MaDonHang, gh.TrangThai, gh.NgayCapNhat, dh.TenNguoiNhan, dh.SDT, dh.DiaChi, dh.NgayDat, dh.TongTien
        FROM giaohang gh
        JOIN donhang dh ON gh.MaDonHang = dh.MaDonHang
        ORDER BY dh.NgayDat DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý giao hàng</title>
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
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-giao {
            background-color: green;
            color: white;
        }

        .btn-huy {
            background-color: red;
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

<h2 style="text-align: center;">Danh sách giao hàng</h2>

<table>
    <tr>
        <th>Mã đơn</th>
        <th>Người nhận</th>
        <th>SĐT</th>
        <th>Địa chỉ</th>
        <th>Ngày đặt</th>
        <th>Ngày giao</th>
        <th>Tổng tiền</th>
        <th>Trạng thái giao</th>
        <th>Hành động</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['MaDonHang'] ?></td>
        <td><?= htmlspecialchars($row['TenNguoiNhan']) ?></td>
        <td><?= htmlspecialchars($row['SDT']) ?></td>
        <td><?= htmlspecialchars($row['DiaChi']) ?></td>
        <td><?= $row['NgayDat'] ?></td>
        <td><?= $row['NgayCapNhat'] ?? '---' ?></td>
        <td><?= number_format($row['TongTien']) ?> đ</td>
        <td><?= $row['TrangThai'] ?></td>
        <td>
            <?php if ($row['TrangThai'] == 'Đang giao hàng'): ?>
                <a href="?giaohang=<?= $row['MaDonHang'] ?>&trangthai=Đã giao hàng" class="btn btn-giao" onclick="return confirm('Xác nhận đã giao hàng?')">Đã giao hàng</a>
                <a href="?giaohang=<?= $row['MaDonHang'] ?>&trangthai=Hủy" class="btn btn-huy" onclick="return confirm('Xác nhận hủy giao hàng?')">Hủy</a>
            <?php else: ?>
                <button class="btn disabled" disabled>Đã xử lý</button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
