<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$sql = "SELECT * FROM chitietloainam";
$result = mysqli_query($conn, $sql);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chi tiết loại sản phẩm nam</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        .top-button {
            width: 80%;
            margin: 20px auto;
            text-align: center;
        }
        button {
            padding: 6px 12px;
            cursor: pointer;
        }
        .action-buttons a {
            margin: 0 5px;
            text-decoration: none;
        }
        .action-buttons button {
            padding: 4px 10px;
        }
    </style>
</head>
<body>

<h2>Danh sách chi tiết loại sản phẩm nam</h2>

<div class="top-button">
    <a href="themchitietloainam.php"><button>Thêm chi tiết loại sản phẩm</button></a>
</div>

<table>
    <thead>
        <tr>
            <th>Mã Chi Tiết</th>
            <th>Tên Chi Tiết</th>
            <th>Mã Loại</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['MaChiTiet']); ?></td>
            <td><?php echo htmlspecialchars($row['TenChiTiet']); ?></td>
            <td><?php echo htmlspecialchars($row['MaLoai']); ?></td>
            <td class="action-buttons">
                <a href="suachitietloainam.php?id=<?php echo urlencode($row['MaChiTiet']); ?>">
                    <button>Sửa</button>
                </a>
                <a href="xoachitietloainam.php?id=<?php echo urlencode($row['MaChiTiet']); ?>" onclick="return confirm('Bạn có chắc muốn xóa?');">
                    <button>Xóa</button>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
