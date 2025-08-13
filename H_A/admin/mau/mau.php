<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
// Nếu chưa đăng nhập thì chuyển về login
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

$result = mysqli_query($conn, "SELECT * FROM mau");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách màu</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px 12px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        a.button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a.button:hover {
            background-color: #0056b3;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .add-button {
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <h2>Danh sách màu</h2>

    <div class="add-button">
        <a href="themmau.php" class="button">+ Thêm màu mới</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã màu</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stt = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $maMau = htmlspecialchars($row['MaMau']);
                echo "<tr>";
                echo "<td>$stt</td>";
                echo "<td>$maMau</td>";
                echo "<td class='action-buttons'>
 
                        <a class='button' href='xoamau.php?id=$maMau' onclick=\"return confirm('Bạn có chắc muốn xóa màu này không?')\">Xóa</a>
                      </td>";
                echo "</tr>";
                $stt++;
            }
            ?>
        </tbody>
    </table>

</body>
</html>
