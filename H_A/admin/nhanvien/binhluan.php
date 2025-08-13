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

// Xử lý xóa bình luận
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $mabl = intval($_POST['delete_id']);
    $sql_delete = "DELETE FROM binhluan WHERE MaBL = $mabl";
    if (mysqli_query($conn, $sql_delete)) {
        echo "<script>alert('Xóa bình luận thành công'); window.location.href = 'binhluan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Không thể xóa bình luận');</script>";
    }
}

// Lấy danh sách bình luận
$sql = "
    SELECT bl.MaBL, bl.NoiDung, bl.ThoiGian, 
           kh.TenKH, sp.TenSanPham
    FROM binhluan bl
    LEFT JOIN khachhang kh ON bl.MaKH = kh.MaKH
    LEFT JOIN sanphamnam sp ON bl.MaSP = sp.MaSP
    ORDER BY bl.ThoiGian DESC
";
$result = mysqli_query($conn, $sql);
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách bình luận</title>
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

<h2>Danh sách bình luận</h2>

<table>
    <thead>
        <tr>
            <th>Mã BL</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaBL']) ?></td>
                    <td><?= htmlspecialchars($row['TenKH'] ?? 'Khách ẩn danh') ?></td>
                    <td><?= htmlspecialchars($row['TenSanPham'] ?? '[Đã xóa]') ?></td>
                    <td><?= htmlspecialchars($row['NoiDung']) ?></td>
                    <td><?= htmlspecialchars($row['ThoiGian']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này không?');">
                            <input type="hidden" name="delete_id" value="<?= $row['MaBL'] ?>">
                            <button type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Không có bình luận nào.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
