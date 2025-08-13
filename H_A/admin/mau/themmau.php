<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

include '../include/header.php';

$thongbao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maMau = trim($_POST['MaMau'] ?? '');

    if ($maMau === '') {
        $thongbao = "Vui lòng nhập mã màu.";
    } else {
        // Kiểm tra trùng mã màu
        $check = mysqli_query($conn, "SELECT * FROM mau WHERE MaMau = '$maMau'");
        if (mysqli_num_rows($check) > 0) {
            $thongbao = "Mã màu này đã tồn tại.";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO mau (MaMau) VALUES ('$maMau')");
            if ($insert) {
                echo "<script>alert('Thêm màu thành công'); window.location='mau.php';</script>";
                exit;
            } else {
                $thongbao = "Lỗi khi thêm màu.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm màu</title>
    <style>
        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"] {
            padding: 6px;
            width: 250px;
        }

        button {
            padding: 6px 14px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .message {
            margin-top: 10px;
            color: red;
        }
    </style>
</head>
<body>

<h2>Thêm màu mới</h2>

<form method="post">
    <label>Mã màu:
        <input type="text" name="MaMau" placeholder="VD: Đen, Trắng, Xanh..." required>
    </label>
    <button type="submit">Thêm màu</button>
</form>

<?php if ($thongbao): ?>
    <div class="message"><?= htmlspecialchars($thongbao) ?></div>
<?php endif; ?>

</body>
</html>
