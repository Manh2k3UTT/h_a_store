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
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}

// Xử lý xóa khách hàng
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_btn']) && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Xóa bình luận trước
    $delete_comment_sql = "DELETE FROM binhluan WHERE MaKH = $delete_id";
    mysqli_query($conn, $delete_comment_sql);

    // Xóa khách hàng sau
    $delete_sql = "DELETE FROM khachhang WHERE MaKH = $delete_id";
    if (mysqli_query($conn, $delete_sql)) {
        echo "<script>alert('Xóa khách hàng và bình luận thành công'); window.location.href = 'khachhang.php';</script>";
        exit;
    } else {
        echo "<script>alert('Không thể xóa khách hàng');</script>";
    }
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

$sql = "SELECT * FROM khachhang";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách khách hàng</title>
    <style>
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f8f8f8;
        }
        form {
            margin: 0;
        }
        button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<h2>Danh sách tài khoản khách hàng</h2>

<table>
    <thead>
        <tr>
            <th>Mã KH</th>
            <th>Tên KH</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Địa chỉ</th>
            <th>Mật khẩu</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['MaKH']) ?></td>
            <td><?= htmlspecialchars($row['TenKH']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['SDT']) ?></td>
            <td><?= htmlspecialchars($row['DiaChi']) ?></td>
            <td><?= htmlspecialchars($row['MatKhau']) ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này không?');">
                    <input type="hidden" name="delete_id" value="<?= $row['MaKH'] ?>">
                    <button type="submit" name="delete_btn">Xóa</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
