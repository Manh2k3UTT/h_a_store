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

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';

// Lấy danh sách mã loại từ bảng loainam để hiển thị dropdown
$sqlLoai = "SELECT MaLoai, TenLoai FROM loainam";
$resultLoai = mysqli_query($conn, $sqlLoai);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenChiTiet = trim($_POST['TenChiTiet'] ?? '');
    $maLoai = trim($_POST['MaLoai'] ?? '');

    if ($tenChiTiet == '' || $maLoai == '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO chitietloainam (TenChiTiet, MaLoai) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $tenChiTiet, $maLoai);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: chitietloainam.php");
            exit;
        } else {
            $error = "Lỗi khi thêm chi tiết loại sản phẩm: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thêm chi tiết loại sản phẩm nam</title>
    <style>
        form {
            width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type=text], select {
            width: 100%;
            padding: 6px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 8px 16px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Thêm chi tiết loại sản phẩm nam</h2>

<?php if (!empty($error)) : ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="TenChiTiet">Tên Chi Tiết:</label>
    <input type="text" name="TenChiTiet" id="TenChiTiet" required>

    <label for="MaLoai">Mã Loại:</label>
    <select name="MaLoai" id="MaLoai" required>
        <option value="">-- Chọn Mã Loại --</option>
        <?php
        if ($resultLoai && mysqli_num_rows($resultLoai) > 0) {
            while ($rowLoai = mysqli_fetch_assoc($resultLoai)) {
                echo '<option value="' . htmlspecialchars($rowLoai['MaLoai']) . '">' . 
                     htmlspecialchars($rowLoai['MaLoai']) . ' - ' . htmlspecialchars($rowLoai['TenLoai']) . '</option>';
            }
        }
        ?>
    </select>

    <button type="submit">Thêm</button>
    <a href="chitietloainam.php" style="margin-left: 10px;">Hủy</a>
</form>

</body>
</html>
