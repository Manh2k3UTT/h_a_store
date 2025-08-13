<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) die("Kết nối thất bại");

$gioitinh = $_GET['gioitinh'] ?? '';

$sql = "SELECT * FROM chitietloainam WHERE MaLoai IN (
            SELECT MaLoai FROM loainam WHERE GioiTinh = '$gioitinh'
        )";

$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo "<option value='{$row['MaChiTiet']}'>{$row['TenChiTiet']}</option>";
}
?>
