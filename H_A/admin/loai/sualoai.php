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

// Lấy mã loại từ URL
if (!isset($_GET['id'])) {
    header('Location: loainam.php');
    exit;
}
$maloai = $_GET['id'];

// Lấy dữ liệu cũ
$sql = "SELECT * FROM loainam WHERE MaLoai = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$maloai]);
$loai = $stmt->fetch();

if (!$loai) {
    echo "<p>Không tìm thấy loại!</p>";
    exit;
}

// Xử lý cập nhật
if (isset($_POST['capnhat'])) {
    $ten = $_POST['TenLoai'];
    $gioitinh = $_POST['GioiTinh'];

    $sql = "UPDATE loainam SET TenLoai = ?, GioiTinh = ? WHERE MaLoai = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$ten, $gioitinh, $maloai]);

    header('Location: loainam.php');
    exit;
}
?>

<h2 style="text-align: center;">Cập nhật Loại Nam</h2>

<form method="post" style="width: 400px; margin: auto; text-align: center;">
    <label>Tên loại:</label><br>
    <input type="text" name="TenLoai" value="<?= htmlspecialchars($loai['TenLoai']) ?>" required><br><br>

    <label>Giới tính:</label><br>
    <select name="GioiTinh" required>
        <option value="Nam" <?= $loai['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
        <option value="Nữ" <?= $loai['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
    </select><br><br>

    <button type="submit" name="capnhat">Cập nhật</button>
    <a href="loainam.php" style="margin-left: 10px;">Quay lại</a>
</form>
