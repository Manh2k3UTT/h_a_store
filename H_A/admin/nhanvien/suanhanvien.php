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

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

if (!isset($_GET['id'])) {
    die("Thiếu mã nhân viên.");
}
$maNV = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $_POST['TenNV'];
    $email = $_POST['Email'];
    $sdt = $_POST['SDT'];
    $diachi = $_POST['DiaChi'];
    $quyen = $_POST['Quyen'];

    // Xử lý mật khẩu nếu có nhập
    $matkhau_moi = $_POST['MatKhau'] ?? '';
    if (!empty($matkhau_moi)) {
        $sql_update = "UPDATE nhanvien SET TenNV=?, Email=?, SDT=?, DiaChi=?, Quyen=?, MatKhau=? WHERE MaNV=?";
        $stmt = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt, "sssssss", $ten, $email, $sdt, $diachi, $quyen, $matkhau_moi, $maNV);
    } else {
        $sql_update = "UPDATE nhanvien SET TenNV=?, Email=?, SDT=?, DiaChi=?, Quyen=? WHERE MaNV=?";
        $stmt = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt, "ssssss", $ten, $email, $sdt, $diachi, $quyen, $maNV);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: nhanvien.php");
        exit;
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}

$sql_nv = "SELECT * FROM nhanvien WHERE MaNV = '$maNV'";
$result_nv = mysqli_query($conn, $sql_nv);
if (mysqli_num_rows($result_nv) !== 1) {
    die("Không tìm thấy nhân viên.");
}
$nv = mysqli_fetch_assoc($result_nv);

$sql_quyen = "SELECT * FROM quyen";
$result_quyen = mysqli_query($conn, $sql_quyen);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhân viên</title>
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
<div class="container">
    <div class="form-wrapper">
        <h2>Sửa thông tin nhân viên</h2>
        <form method="POST">
            <div class="form-group">
                <label>Tên nhân viên</label>
                <input type="text" name="TenNV" class="form-control" value="<?= htmlspecialchars($nv['TenNV']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($nv['Email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="SDT" class="form-control" value="<?= htmlspecialchars($nv['SDT']) ?>" required>
            </div>
            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="DiaChi" class="form-control" value="<?= htmlspecialchars($nv['DiaChi']) ?>" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu (chỉ nhập nếu muốn đổi)</label>
                <input type="text" name="MatKhau" class="form-control" value="<?= htmlspecialchars($nv['MatKhau']) ?>">
            </div>
            <div class="form-group">
                <label>Quyền</label>
                <select name="Quyen" class="form-control" required>
                    <?php while ($row = mysqli_fetch_assoc($result_quyen)): ?>
                        <option value="<?= $row['id'] ?>" <?= $row['id'] == $nv['Quyen'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['Ten']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="nhanvien.php" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
