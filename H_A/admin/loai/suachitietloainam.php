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

$maChiTiet = $_GET['id'] ?? '';
if ($maChiTiet == '') {
    header("Location: chitietloainam.php");
    exit;
}

// Lấy dữ liệu chi tiết loại sản phẩm cần sửa
$sqlGet = "SELECT * FROM chitietloainam WHERE MaChiTiet = ?";
$stmtGet = mysqli_prepare($conn, $sqlGet);
mysqli_stmt_bind_param($stmtGet, "s", $maChiTiet);
mysqli_stmt_execute($stmtGet);
$resultGet = mysqli_stmt_get_result($stmtGet);
if (mysqli_num_rows($resultGet) == 0) {
    // Nếu không tìm thấy mã chi tiết, quay về danh sách
    mysqli_stmt_close($stmtGet);
    header("Location: chitietloainam.php");
    exit;
}
$row = mysqli_fetch_assoc($resultGet);
mysqli_stmt_close($stmtGet);

// Lấy danh sách mã loại để hiển thị dropdown
$sqlLoai = "SELECT MaLoai, TenLoai FROM loainam";
$resultLoai = mysqli_query($conn, $sqlLoai);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenChiTiet = trim($_POST['TenChiTiet'] ?? '');
    $maLoai = trim($_POST['MaLoai'] ?? '');

    if ($tenChiTiet == '' || $maLoai == '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $sqlUpdate = "UPDATE chitietloainam SET TenChiTiet = ?, MaLoai = ? WHERE MaChiTiet = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "sss", $tenChiTiet, $maLoai, $maChiTiet);
        if (mysqli_stmt_execute($stmtUpdate)) {
            mysqli_stmt_close($stmtUpdate);
            header("Location: chitietloainam.php");
            exit;
        } else {
            $error = "Lỗi khi cập nhật chi tiết loại sản phẩm: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmtUpdate);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sửa chi tiết loại sản phẩm nam</title>
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

<h2>Sửa chi tiết loại sản phẩm nam</h2>

<?php if ($error != ''): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="TenChiTiet">Tên Chi Tiết:</label>
    <input type="text" id="TenChiTiet" name="TenChiTiet" required value="<?php echo htmlspecialchars($row['TenChiTiet']); ?>">

    <label for="MaLoai">Mã Loại:</label>
    <select id="MaLoai" name="MaLoai" required>
        <option value="">-- Chọn Mã Loại --</option>
        <?php
        if ($resultLoai && mysqli_num_rows($resultLoai) > 0) {
            while ($rowLoai = mysqli_fetch_assoc($resultLoai)) {
                $selected = ($rowLoai['MaLoai'] == $row['MaLoai']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($rowLoai['MaLoai']) . '" ' . $selected . '>' .
                     htmlspecialchars($rowLoai['MaLoai']) . ' - ' . htmlspecialchars($rowLoai['TenLoai']) .
                     '</option>';
            }
        }
        ?>
    </select>

    <button type="submit">Lưu thay đổi</button>
    <a href="chitietloainam.php" style="margin-left: 10px;">Hủy</a>
</form>

</body>
</html>
