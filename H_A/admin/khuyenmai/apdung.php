<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}

include("../../model/database.php");



// Lấy mã khuyến mãi từ param URL
if (!isset($_GET['id'])) {
    echo "Không xác định mã khuyến mãi.";
    exit;
}

$makm = $_GET['id'];

// Lấy thông tin tên khuyến mãi
$stmt_km = $conn->prepare("SELECT TenKM FROM khuyenmai WHERE MaKM = ?");
$stmt_km->bind_param("s", $makm);
$stmt_km->execute();
$result_km = $stmt_km->get_result();
if ($result_km->num_rows == 0) {
    echo "Khuyến mãi không tồn tại.";
    exit;
}
$km = $result_km->fetch_assoc();
$tenkm = $km['TenKM'];
$stmt_km->close();

// Xử lý khi người dùng bấm áp dụng hoặc bỏ áp dụng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masp = $_POST['masp'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($masp) {
        if ($action === 'apdung') {
            // Xóa hết khuyến mãi cũ của sản phẩm này (mỗi sp chỉ áp dụng 1 km)
            $stmtDel = $conn->prepare("DELETE FROM sanpham_khuyenmai WHERE MaSP = ?");
            $stmtDel->bind_param("s", $masp);
            $stmtDel->execute();
            $stmtDel->close();

            // Thêm áp dụng khuyến mãi mới
            $stmtIns = $conn->prepare("INSERT INTO sanpham_khuyenmai (MaSP, MaKM) VALUES (?, ?)");
            $stmtIns->bind_param("ss", $masp, $makm);
            $stmtIns->execute();
            $stmtIns->close();
        } elseif ($action === 'boapdung') {
            // Bỏ áp dụng khuyến mãi (xóa bản ghi)
            $stmtDel = $conn->prepare("DELETE FROM sanpham_khuyenmai WHERE MaSP = ? AND MaKM = ?");
            $stmtDel->bind_param("ss", $masp, $makm);
            $stmtDel->execute();
            $stmtDel->close();
        }
    }

    // Chuyển hướng lại trang để tránh submit lại form
    header("Location: apdung.php?id=$makm");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include("../include/header.php");

// Lấy danh sách sản phẩm chưa áp dụng khuyến mãi nào
$sql_chua_apdung = "
    SELECT sp.MaSP, sp.TenSanPham
    FROM sanphamnam sp
    LEFT JOIN sanpham_khuyenmai skm ON sp.MaSP = skm.MaSP
    WHERE skm.MaSP IS NULL
";

// Lấy danh sách sản phẩm đã áp dụng khuyến mãi $makm
$sql_da_apdung = "
    SELECT sp.MaSP, sp.TenSanPham
    FROM sanphamnam sp
    INNER JOIN sanpham_khuyenmai skm ON sp.MaSP = skm.MaSP
    WHERE skm.MaKM = ?
";

$result_chua_apdung = $conn->query($sql_chua_apdung);

$stmt_da_apdung = $conn->prepare($sql_da_apdung);
$stmt_da_apdung->bind_param("s", $makm);
$stmt_da_apdung->execute();
$result_da_apdung = $stmt_da_apdung->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Áp dụng khuyến mãi - <?= htmlspecialchars($tenkm) ?></title>
    <style>
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        form {
            margin: 0;
        }
        button {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-apdung { background-color: #4CAF50; color: white; }
        .btn-boapdung { background-color: #f44336; color: white; }
        h1, h2 {
            width: 90%;
            margin: 20px auto 10px;
        }
        .btn-back {
            display: inline-block;
            margin: 0 20px auto 20px;
            padding: 8px 16px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Áp dụng khuyến mãi: <?= htmlspecialchars($tenkm) ?></h1>
<a href="khuyenmai.php" class="btn-back">← Quay lại danh sách khuyến mãi</a>

<h2>Sản phẩm chưa áp dụng khuyến mãi nào</h2>
<table>
    <thead>
        <tr>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result_chua_apdung->num_rows > 0): ?>
        <?php while ($row = $result_chua_apdung->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['MaSP']) ?></td>
                <td><?= htmlspecialchars($row['TenSanPham']) ?></td>
                <td>
                    <form method="post" action="apdung.php?id=<?= urlencode($makm) ?>">
                        <input type="hidden" name="masp" value="<?= htmlspecialchars($row['MaSP']) ?>">
                        <input type="hidden" name="action" value="apdung">
                        <button type="submit" class="btn-apdung">Áp dụng</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="3">Tất cả sản phẩm đã được áp dụng khuyến mãi</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2>Sản phẩm đã áp dụng khuyến mãi này</h2>
<table>
    <thead>
        <tr>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result_da_apdung->num_rows > 0): ?>
        <?php while ($row = $result_da_apdung->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['MaSP']) ?></td>
                <td><?= htmlspecialchars($row['TenSanPham']) ?></td>
                <td>
                    <form method="post" action="apdung.php?id=<?= urlencode($makm) ?>">
                        <input type="hidden" name="masp" value="<?= htmlspecialchars($row['MaSP']) ?>">
                        <input type="hidden" name="action" value="boapdung">
                        <button type="submit" class="btn-boapdung">Bỏ áp dụng</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="3">Chưa có sản phẩm nào áp dụng khuyến mãi này</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$stmt_da_apdung->close();
$conn->close();
?>
