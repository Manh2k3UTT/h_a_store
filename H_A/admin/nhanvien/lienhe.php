<?php

$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý xóa liên hệ nếu có yêu cầu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $malh = (int)$_GET['delete'];
    $conn->query("DELETE FROM lienhe WHERE MaLH = $malh");
    echo "<script>alert('Đã xóa liên hệ.'); window.location.href='lienhe.php';</script>";
    exit;
}

// Lấy danh sách liên hệ
$result = $conn->query("SELECT * FROM lienhe ORDER BY NgayGui DESC");
$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

?>

<style>
    .contact-admin-wrapper {
        padding: 40px;
        font-family: Arial, sans-serif;
    }

    .contact-admin-wrapper h2 {
        color: #d32f2f;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ccc;
        text-align: left;
        vertical-align: top;
    }

    th {
        background-color: #f2f2f2;
    }

    .delete-btn {
        background-color: #d32f2f;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
    }

    .delete-btn:hover {
        background-color: #a62828;
    }
</style>

<div class="contact-admin-wrapper">
    <h2>Quản lý liên hệ khách hàng</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Mã LH</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Nội dung</th>
                    <th>Ngày gửi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['MaLH'] ?></td>
                        <td><?= htmlspecialchars($row['HoTen']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['NoiDung'])) ?></td>
                        <td><?= $row['NgayGui'] ?></td>
                        <td>
                            <a href="lienhe.php?delete=<?= $row['MaLH'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                <button class="delete-btn">Xóa</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Chưa có liên hệ nào.</p>
    <?php endif; ?>
</div>

