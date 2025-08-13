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

$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
$sql = "
    SELECT nv.MaNV, nv.TenNV, nv.Email, nv.SDT, nv.DiaChi, nv.MatKhau, q.Ten AS TenQuyen
    FROM nhanvien nv
    LEFT JOIN quyen q ON nv.Quyen = q.id
    WHERE nv.MaNV LIKE '%$search%' 
        OR nv.TenNV LIKE '%$search%' 
        OR nv.Email LIKE '%$search%'
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý nhân viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 20px;
            color: #333;
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }
        .search-bar {
            width: 100%;
            max-width: 500px;
            margin: 10px auto;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .search-bar input[type="text"] {
            padding: 6px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 6px 12px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #0069d9;
        }
        a.btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            font-size: 14px;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px auto;
        }
        a.btn:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        td.center, th.center {
            text-align: center;
        }
        a.btn-primary {
            background-color: #007bff;
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 3px;
            color: white;
            text-decoration: none;
        }
        a.btn-primary:hover {
            background-color: #0069d9;
        }
        a.btn-danger {
            background-color: #dc3545;
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 3px;
            color: white;
            text-decoration: none;
            margin-left: 5px;
        }
        a.btn-danger:hover {
            background-color: #c82333;
        }
        .btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div>
    <h2>Danh sách nhân viên</h2>

    <form class="search-bar" method="GET">
        <input type="text" name="search" placeholder="Tìm theo mã NV, tên hoặc email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Tìm kiếm</button>
    </form>

    <div class="btn-container">
        <a href="themnhanvien.php" class="btn">+ Thêm nhân viên</a>
    </div>

    <table>
        <thead>
            <tr>
                <th class="center">Mã NV</th>
                <th>Tên NV</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Mật khẩu</th>
                <th>Quyền</th>
                <th class="center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="center"><?= htmlspecialchars($row['MaNV']) ?></td>
                    <td><?= htmlspecialchars($row['TenNV']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['SDT']) ?></td>
                    <td><?= htmlspecialchars($row['DiaChi']) ?></td>
                    <td><?= htmlspecialchars($row['MatKhau']) ?></td>
                    <td><?= htmlspecialchars($row['TenQuyen']) ?></td>
                    <td class="center">
                        <a href="suanhanvien.php?id=<?= urlencode($row['MaNV']) ?>" class="btn-primary">Sửa</a>
                        <a href="xoanhanvien.php?id=<?= urlencode($row['MaNV']) ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');" class="btn-danger">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="center" style="font-style: italic; color: #999;">Không tìm thấy nhân viên nào.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
