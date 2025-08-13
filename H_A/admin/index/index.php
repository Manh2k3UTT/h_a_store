<?php
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
include '../../model/database.php';

// Tính doanh thu hôm nay
$sql_today = "SELECT SUM(d.TongTien) AS DoanhThu
              FROM donhang d
              JOIN giaohang g ON d.MaDonHang = g.MaDonHang
              WHERE g.TrangThai = 'Đã giao hàng'
              AND DATE(g.NgayCapNhat) = CURDATE()";
$result_today = $conn->query($sql_today);
$doanhthu_today = 0;
if ($result_today && $row = $result_today->fetch_assoc()) {
    $doanhthu_today = $row['DoanhThu'] ?? 0;
}

// Lấy danh sách đơn hàng đã giao hôm nay
$sql_donhang_homnay = "SELECT d.MaDonHang, d.TenNguoiNhan, d.TongTien, g.NgayCapNhat
                       FROM donhang d
                       JOIN giaohang g ON d.MaDonHang = g.MaDonHang
                       WHERE g.TrangThai = 'Đã giao hàng'
                       AND DATE(g.NgayCapNhat) = CURDATE()
                       ORDER BY g.NgayCapNhat DESC";
$result_donhang_homnay = $conn->query($sql_donhang_homnay);

// Lấy danh sách các năm có giao hàng
$sql_nam = "SELECT DISTINCT YEAR(NgayCapNhat) AS Nam
            FROM giaohang
            WHERE TrangThai = 'Đã giao hàng'
            ORDER BY Nam DESC";
$result_nam = $conn->query($sql_nam);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê doanh thu</title>
    <style>
        .year-btn, .month-btn {
            padding: 4px 8px;
            font-size: 14px;
            margin: 2px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }
        .year-btn:hover, .month-btn:hover {
            background-color: #0056b3;
        }
        .hidden { display: none; }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function toggleMonths(year) {
            document.querySelectorAll('.month-group').forEach(el => el.classList.add('hidden'));
            document.getElementById('months-' + year).classList.remove('hidden');
        }
    </script>
</head>
<body>

<h2 style="text-align: center;">Thống kê doanh thu</h2>

<!-- Doanh thu hôm nay -->
<div style="text-align: center; margin: 20px; font-size: 20px; font-weight: bold;">
    Doanh thu hôm nay: <?= number_format($doanhthu_today) ?> đ
</div>

<!-- Danh sách đơn hàng đã giao hôm nay -->
<?php if ($result_donhang_homnay->num_rows > 0): ?>
<table>
    <tr>
        <th>Mã đơn</th>
        <th>Người nhận</th>
        <th>Thời gian giao</th>
        <th>Tổng tiền</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result_donhang_homnay->fetch_assoc()): ?>
        <tr>
            <td><?= $row['MaDonHang'] ?></td>
            <td><?= htmlspecialchars($row['TenNguoiNhan']) ?></td>
            <td><?= $row['NgayCapNhat'] ?></td>
            <td><?= number_format($row['TongTien']) ?> đ</td>
            <td>
                <a href="chitietdonhang.php?madon=<?= $row['MaDonHang'] ?>" class="month-btn">Xem chi tiết</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align: center;">Không có đơn hàng nào được giao hôm nay.</p>
<?php endif; ?>

<!-- Danh sách năm -->
<h3 style="text-align: center;">Xem doanh thu theo năm</h3>
<div style="text-align: center;">
    <?php while ($row = $result_nam->fetch_assoc()): ?>
        <button class="year-btn" onclick="toggleMonths(<?= $row['Nam'] ?>)"><?= $row['Nam'] ?></button>
    <?php endwhile; ?>
</div>

<!-- Danh sách tháng -->
<?php
mysqli_data_seek($result_nam, 0);
while ($row = $result_nam->fetch_assoc()):
    $nam = $row['Nam'];
    $sql_thang = "SELECT MONTH(g.NgayCapNhat) AS Thang, SUM(d.TongTien) AS DoanhThu
                  FROM giaohang g
                  JOIN donhang d ON g.MaDonHang = d.MaDonHang
                  WHERE g.TrangThai = 'Đã giao hàng' AND YEAR(g.NgayCapNhat) = ?
                  GROUP BY Thang
                  ORDER BY Thang ASC";
    $stmt = $conn->prepare($sql_thang);
    $stmt->bind_param("i", $nam);
    $stmt->execute();
    $result_thang = $stmt->get_result();
?>
    <div class="month-group hidden" id="months-<?= $nam ?>">
        <table>
            <tr>
                <th>Tháng</th>
                <th>Doanh thu</th>
                <th>Hành động</th>
            </tr>
            <?php while ($thang = $result_thang->fetch_assoc()): ?>
                <tr>
                    <td>Tháng <?= $thang['Thang'] ?></td>
                    <td><?= number_format($thang['DoanhThu']) ?> đ</td>
                    <td>
                        <a href="chitiet_thang.php?nam=<?= $nam ?>&thang=<?= $thang['Thang'] ?>" class="month-btn">Xem chi tiết</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
<?php endwhile; ?>

</body>
</html>
