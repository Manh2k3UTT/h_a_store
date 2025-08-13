<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "h_a";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
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

// Lấy danh sách quyền để hiển thị trong form
$quyen_query = "SELECT * FROM quyen";
$quyen_result = mysqli_query($conn, $quyen_query);

// Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tennv = $_POST['TenNV'];
    $email = $_POST['Email'];
    $sdt = $_POST['SDT'];
    $diachi = $_POST['DiaChi'];
    $quyen = $_POST['Quyen'];
    $matkhau = $_POST['MatKhau'];
    $matkhau = $_POST['MatKhau'];

    $sql = "INSERT INTO nhanvien (TenNV, Email, SDT, DiaChi, Quyen, MatKhau)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $tennv, $email, $sdt, $diachi, $quyen, $matkhau);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: nhanvien.php");
        exit();
    } else {
        echo "Lỗi thêm nhân viên: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
    <link rel="stylesheet" href="template/css/sb-admin-2.min.css">
    <style>
        body {
            background: #f4f6f9;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-wrapper {
            max-width: 600px;
            margin: 60px auto;
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-group label {
            font-weight: 500;
            color: #34495e;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
        }
        .btn-primary {
            background-color: #e74c3c;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #c0392b;
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 10px 20px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="form-wrapper">
        <h2>Thêm nhân viên</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="TenNV">Tên nhân viên</label>
                <input type="text" name="TenNV" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="Email">Email</label>
                <input type="email" name="Email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="SDT">Số điện thoại</label>
                <input type="text" name="SDT" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="DiaChi">Địa chỉ</label>
                <input type="text" name="DiaChi" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="MatKhau">Mật khẩu</label>
                <input type="password" name="MatKhau" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="Quyen">Quyền</label>
                <select name="Quyen" class="form-control" required>
                    <option value="">-- Chọn quyền --</option>
                    <?php while ($row = mysqli_fetch_assoc($quyen_result)): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['Ten']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Thêm</button>
            <a href="nhanvien.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
</body>
</html>
