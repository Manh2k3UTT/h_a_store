<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';
include '../../model/database.php';

// Nhận tham số
$nam = isset($_GET['nam']) ? intval($_GET['nam']) : date('Y');
$thang = isset($_GET['thang']) ? intval($_GET['thang']) : date('m');

// Lấy danh sách ngày trong tháng có đơn hàng giao thành công
$sql_ngay = "
    SELECT DATE(g.NgayCapNhat) AS Ngay, COUNT(d.MaDonHang) AS SoDon, SUM(d.TongTien) AS DoanhThuNgay
    FROM giaohang g
    JOIN donhang d ON g.MaDonHang = d.MaDonHang
    WHERE g.TrangThai = 'Đã giao hàng'
      AND YEAR(g.NgayCapNhat) = ? AND MONTH(g.NgayCapNhat) = ?
    GROUP BY Ngay
    ORDER BY Ngay ASC
";
$stmt_ngay = $conn->prepare($sql_ngay);
$stmt_ngay->bind_param("ii", $nam, $thang);
$stmt_ngay->execute();
$result_ngay = $stmt_ngay->get_result();
?>

<h3 style="text-align: center;">Chi tiết doanh thu tháng <?= $thang ?>/<?= $nam ?></h3>

<?php while ($ngay_data = $result_ngay->fetch_assoc()) : ?>
    <h4 style="margin-left: 10%; color: #007bff;">
        Ngày <?= $ngay_data['Ngay'] ?> -
        <?= $ngay_data['SoDon'] ?> đơn -
        <?= number_format($ngay_data['DoanhThuNgay']) ?> đ
    </h4>

    <?php
    // Lấy danh sách đơn hàng trong ngày này
    $ngay = $ngay_data['Ngay'];
    $sql_don = "
        SELECT d.MaDonHang, d.TenNguoiNhan, d.SDT, d.DiaChi, d.TongTien
        FROM donhang d
        JOIN giaohang g ON d.MaDonHang = g.MaDonHang
        WHERE DATE(g.NgayCapNhat) = ?
          AND g.TrangThai = 'Đã giao hàng'
    ";
    $stmt_don = $conn->prepare($sql_don);
    $stmt_don->bind_param("s", $ngay);
    $stmt_don->execute();
    $result_don = $stmt_don->get_result();
    ?>

    <table border="1" cellpadding="10" cellspacing="0" width="80%" style="margin: 10px auto 30px auto; text-align: center;">
        <tr style="background-color: #f2f2f2;">
            <th>Mã Đơn</th>
            <th>Người Nhận</th>
            <th>SĐT</th>
            <th>Địa Chỉ</th>
            <th>Tổng Tiền</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result_don->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['MaDonHang'] ?></td>
                <td><?= htmlspecialchars($row['TenNguoiNhan']) ?></td>
                <td><?= htmlspecialchars($row['SDT']) ?></td>
                <td><?= htmlspecialchars($row['DiaChi']) ?></td>
                <td><?= number_format($row['TongTien']) ?> đ</td>
                <td>
                    <a href="chitietdonhang.php?madon=<?= $row['MaDonHang'] ?>" style="padding: 4px 8px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">Chi tiết</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

<?php endwhile; ?>

<div style="text-align: center;">
    <a href="index.php" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">← Quay lại thống kê</a>
</div>
