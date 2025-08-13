<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['Quyen']) || !in_array($_SESSION['Quyen'], [1, 2])) {
    echo "Bạn không có quyền truy cập trang này.";
    exit;
}
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
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

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM loainam WHERE TenLoai LIKE :search";
$stmt = $conn->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$ds = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Loại Nam</title>
    <style>
        .controls {
            width: 90%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .controls form {
            display: flex;
            gap: 10px;
        }
        .controls input[type="text"] {
            padding: 6px;
            width: 200px;
        }
        .controls button, .controls a {
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
        }
        .btn-them {
            background: #28a745;
        }
        .btn-sua {
            background: blue;
            padding: 6px 10px;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-xoa {
            background: red;
            padding: 6px 10px;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Quản lý Loại Nam</h2>

<div class="controls">
    <form method="GET">
        <input type="text" name="search" placeholder="Tìm theo tên loại..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" style="background: #007bff;">Tìm kiếm</button>
    </form>
    <a href="themloai.php" class="btn-them">➕ Thêm loại</a>
</div>

<table>
    <thead>
        <tr>
            <th>Mã loại</th>
            <th>Tên loại</th>
            <th>Giới tính</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ds as $row): ?>
            <tr>
                <td><?= $row['MaLoai'] ?></td>
                <td><?= htmlspecialchars($row['TenLoai']) ?></td>
                <td><?= $row['GioiTinh'] ?></td>
                <td>
                    <a href="sualoai.php?id=<?= $row['MaLoai'] ?>"><button class="btn-sua">Sửa</button></a>
                    <a href="xoaloai.php?id=<?= $row['MaLoai'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?')"><button class="btn-xoa">Xóa</button></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
