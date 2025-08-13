<?php
if (!isset($_GET['madon'])) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}

$madon = intval($_GET['madon']);

$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy chi tiết đơn hàng
$sql = "
    SELECT ct.*, sp.TenSanPham
    FROM chitietdonhang ct
    JOIN sanphamnam sp ON ct.MaSP = sp.MaSP
    WHERE ct.MaDonHang = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $madon);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Không có sản phẩm nào trong đơn hàng.";
    exit;
}

// Hiển thị dưới dạng bảng
echo '<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; margin-top: 10px;">
        <tr style="background-color: #f2f2f2;">
            <th>Tên sản phẩm</th>
            <th>Màu</th>
            <th>Size</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>';

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row['TenSanPham']) . "</td>
        <td>" . htmlspecialchars($row['MaMau']) . "</td>
        <td>" . htmlspecialchars($row['MaSize']) . "</td>
        <td>" . $row['SoLuong'] . "</td>
        <td>" . number_format($row['DonGia'], 0, ',', '.') . " đ</td>
        <td>" . number_format($row['DonGia'] * $row['SoLuong'], 0, ',', '.') . " đ</td>
    </tr>";
}
echo '</table>';
