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
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include("../include/header.php");
include("../../model/database.php");

// Lấy danh sách khuyến mãi
$sql = "SELECT * FROM khuyenmai ORDER BY NgayBatDau DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách khuyến mãi</title>
    <style>
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-buttons a {
            margin: 0 4px;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-apdung { background-color: #4CAF50; color: white; }
        .btn-sua    { background-color: #2196F3; color: white; }
        .btn-xoa    { background-color: #f44336; color: white; }

        .top-bar {
            width: 95%;
            margin: 20px auto 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h2 {
            margin: 0;
        }

        .btn-them {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

    </style>
</head>
<body>

<div class="top-bar">
    <h2>Danh sách khuyến mãi</h2>
    <a href="themkhuyenmai.php" class="btn-them">+ Thêm khuyến mãi</a>
</div>

<table>
    <thead>
        <tr>
            <th>Mã KM</th>
            <th>Tên KM</th>
            <th>Mô tả</th>
            <th>Giảm (%)</th>
            <th>Giảm tiền (VNĐ)</th>
            <th>Bắt đầu</th>
            <th>Kết thúc</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['MaKM']) ?></td>
            <td><?= htmlspecialchars($row['TenKM']) ?></td>
            <td><?= htmlspecialchars($row['MoTa']) ?></td>
            <td><?= $row['KM_PT'] ?>%</td>
            <td><?= number_format($row['TienKM']) ?> đ</td>
            <td><?= $row['NgayBatDau'] ?></td>
            <td><?= $row['NgayKetThuc'] ?></td>
            <td class="action-buttons">
                <a href="apdung.php?id=<?= $row['MaKM'] ?>" class="btn-apdung">Áp dụng</a>
                <a href="suakhuyenmai.php?makm=<?= $row['MaKM'] ?>" class="btn-sua">Sửa</a>
                <a href="xoakhuyenmai.php?MaKM=<?= $row['MaKM'] ?>" class="btn-xoa" onclick="return confirm('Xác nhận xóa khuyến mãi này?')">Xóa</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
