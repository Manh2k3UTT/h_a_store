<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include '../include/header.php';

$maMau = $_GET['id'] ?? '';
if (!$maMau) {
    echo "<script>alert('Không tìm thấy mã màu.'); window.location='mau.php';</script>";
    exit;
}

// Kiểm tra nếu màu đang được sử dụng trong các bảng khác
$tables = ['chitietsanphamnam', 'chitietsanphamnu', 'phieunhap', 'phieuxuat'];
$isUsed = false;

foreach ($tables as $table) {
    $checkCol = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE 'MaMau'");
    if (mysqli_num_rows($checkCol) > 0) {
        $query = "SELECT COUNT(*) AS total FROM `$table` WHERE MaMau = '$maMau'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['total'] > 0) {
            $isUsed = true;
            break;
        }
    }
}

if ($isUsed) {
    echo "<script>alert('Không thể sửa màu \"$maMau\" vì đang được sử dụng.'); window.location='mau.php';</script>";
    exit;
}

// Nếu không bị sử dụng thì xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mauMoi = $_POST['MaMauMoi'] ?? '';
    if ($mauMoi) {
        // Kiểm tra trùng mã mới
        $check = mysqli_query($conn, "SELECT * FROM mau WHERE MaMau = '$mauMoi'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Mã màu mới đã tồn tại.');</script>";
        } else {
            mysqli_query($conn, "UPDATE mau SET MaMau = '$mauMoi' WHERE MaMau = '$maMau'");
            echo "<script>alert('Cập nhật mã màu thành công.'); window.location='mau.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sửa màu</title>
</head>
<body>
    <h2>Sửa màu</h2>
    <form method="POST">
        <label>Mã màu mới: 
            <input type="text" name="MaMauMoi" value="<?= htmlspecialchars($maMau) ?>" required>
        </label>
        <br><br>
        <button type="submit">Cập nhật</button>
        <a href="mau.php">Quay lại</a>
    </form>
</body>
</html>
