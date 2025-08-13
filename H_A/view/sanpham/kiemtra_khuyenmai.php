<?php
// 1. Kết nối CSDL (thay đổi theo thông tin của bạn)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "H_A";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// 2. Mã sản phẩm cần kiểm tra
$masp = 16;

// 3. Truy vấn kiểm tra
$sql = "SELECT * FROM sanpham_khuyenmai WHERE MaSP = $masp LIMIT 1";
$result = mysqli_query($conn, $sql);

// 4. Kiểm tra kết quả
if (mysqli_num_rows($result) > 0) {
    echo "Sản phẩm $masp có áp dụng khuyến mãi.";
} else {
    echo "Sản phẩm $masp không có khuyến mãi.";
}

// 5. Đóng kết nối
mysqli_close($conn);
?>
