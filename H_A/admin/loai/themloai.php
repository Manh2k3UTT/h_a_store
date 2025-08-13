<?php
include '../include/header.php';

// Kết nối CSDL
$host = 'localhost';
$dbname = 'h_a';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}

// Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenloai = $_POST['TenLoai'];
    $gioitinh = $_POST['GioiTinh'];

    // Đảm bảo chỉ nhận "Nam" hoặc "Nữ"
    if ($gioitinh === "Nam" || $gioitinh === "Nữ") {
        $stmt = $conn->prepare("INSERT INTO loainam (TenLoai, GioiTinh) VALUES (?, ?)");
        $stmt->execute([$tenloai, $gioitinh]);
        header("Location: loainam.php");
        exit;
    } else {
        $error = "Giới tính không hợp lệ!";
    }
}
?>

<h2 style="text-align:center;">Thêm loại sản phẩm nam</h2>

<form method="post" style="width: 400px; margin: auto;">
    <label for="TenLoai">Tên loại:</label><br>
    <input type="text" name="TenLoai" id="TenLoai" required style="width: 100%; padding: 8px;"><br><br>

    <label for="GioiTinh">Giới tính:</label><br>
    <select name="GioiTinh" id="GioiTinh" required style="width: 100%; padding: 8px;">
        <option value="Nam">Nam</option>
        <option value="Nữ">Nữ</option>
    </select><br><br>

    <button type="submit" style="padding: 10px 20px;">Thêm</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
