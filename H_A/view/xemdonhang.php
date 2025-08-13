<?php
session_start();
if (!isset($_SESSION['MaKH']) || !isset($_GET['madon'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$madon = $_GET['madon'];
$makh = $_SESSION['MaKH'];

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM donhang WHERE MaDH = ? AND MaKH = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $madon, $makh);
$stmt->execute();
$result = $stmt->get_result();
$donhang = $result->fetch_assoc();

if (!$donhang) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}

// Lấy chi tiết đơn hàng
$sql_ct = "SELECT ct.*, sp.TenSP FROM chitietdonhang ct
            JOIN sanpham sp ON ct.MaSP = sp.MaSP
            WHERE ct.MaDH = ?";
$stmt_ct = $conn->prepare($sql_ct);
$stmt_ct->bind_param("i", $madon);
$stmt_ct->execute();
$ct_result = $stmt_ct->get_result();
?>

<h2>Chi tiết đơn hàng #<?php echo $madon; ?></h2>
<p>Ngày đặt: <?php echo $donhang['NgayDat']; ?></p>
<p>Tổng tiền: <?php echo number_format($donhang['TongTien'], 0, ',', '.'); ?>₫</p>
<p>Trạng thái: <?php echo $donhang['TrangThai']; ?></p>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Sản phẩm</th>
        <th>Màu</th>
        <th>Size</th>
        <th>Số lượng</th>
        <th>Giá</th>
    </tr>
    <?php while ($item = $ct_result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $item['TenSP']; ?></td>
        <td><?php echo $item['MaMau']; ?></td>
        <td><?php echo $item['MaSize']; ?></td>
        <td><?php echo $item['SoLuong']; ?></td>
        <td><?php echo number_format($item['Gia'], 0, ',', '.'); ?>₫</td>
    </tr>
    <?php endwhile; ?>
</table>

<p><a href="account.php">← Quay lại trang tài khoản</a></p>
