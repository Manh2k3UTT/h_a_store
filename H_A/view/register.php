<?php
$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$tenkh = $email = $sdt = $diachi = $matkhau = $matkhau_nhaplai = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkh = trim($_POST["tenkh"]);
    $email = trim($_POST["email"]);
    $sdt = trim($_POST["sdt"]);
    $diachi = trim($_POST["diachi"]);
    $matkhau = $_POST["matkhau"];
    $matkhau_nhaplai = $_POST["matkhau_nhaplai"];

    // Kiểm tra từng trường
    if (empty($tenkh)) $errors['tenkh'] = "Vui lòng nhập họ tên.";
    if (empty($email)) $errors['email'] = "Vui lòng nhập email.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email không hợp lệ.";
    
    if (empty($sdt)) $errors['sdt'] = "Vui lòng nhập số điện thoại.";
    if (empty($diachi)) $errors['diachi'] = "Vui lòng nhập địa chỉ.";
    if (empty($matkhau)) $errors['matkhau'] = "Vui lòng nhập mật khẩu.";
    if (empty($matkhau_nhaplai)) $errors['matkhau_nhaplai'] = "Vui lòng nhập lại mật khẩu.";
    elseif ($matkhau !== $matkhau_nhaplai) $errors['matkhau_nhaplai'] = "Mật khẩu nhập lại không khớp.";

    // Nếu không có lỗi thì tiến hành thêm dữ liệu
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO khachhang (TenKH, Email, SDT, DiaChi, MatKhau) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $tenkh, $email, $sdt, $diachi, $matkhau);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors['submit'] = "Lỗi khi đăng ký: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f0f0;
        padding: 20px;
    }
    .container {
        max-width: 400px; /* Tăng chiều rộng một chút để dễ nhìn hơn */
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px #ccc;
        box-sizing: border-box;
    }
    form label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 5px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .error {
        color: red;
        font-size: 0.85em;
        margin-bottom: 5px;
    }
    input[type="submit"] {
        width: 100%;
        background: #dc3545; /* Màu đỏ nổi bật */
        color: white;
        border: none;
        padding: 10px;
        margin-top: 15px;
        border-radius: 5px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background: #c82333;
    }
    h2 {
        text-align: center;
        color: #333;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Đăng ký</h2>

    <?php if (!empty($errors['submit'])) echo "<div class='error'>{$errors['submit']}</div>"; ?>

    <form method="post">
        <label>Họ tên:</label>
        <input type="text" name="tenkh" value="<?= htmlspecialchars($tenkh) ?>">
        <?php if (!empty($errors['tenkh'])) echo "<div class='error'>{$errors['tenkh']}</div>"; ?>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
        <?php if (!empty($errors['email'])) echo "<div class='error'>{$errors['email']}</div>"; ?>

        <label>Số điện thoại:</label>
        <input type="text" name="sdt" value="<?= htmlspecialchars($sdt) ?>">
        <?php if (!empty($errors['sdt'])) echo "<div class='error'>{$errors['sdt']}</div>"; ?>

        <label>Địa chỉ:</label>
        <input type="text" name="diachi" value="<?= htmlspecialchars($diachi) ?>">
        <?php if (!empty($errors['diachi'])) echo "<div class='error'>{$errors['diachi']}</div>"; ?>

        <label>Mật khẩu:</label>
        <input type="password" name="matkhau" value="<?= htmlspecialchars($matkhau) ?>">
        <?php if (!empty($errors['matkhau'])) echo "<div class='error'>{$errors['matkhau']}</div>"; ?>

        <label>Nhập lại mật khẩu:</label>
        <input type="password" name="matkhau_nhaplai" value="<?= htmlspecialchars($matkhau_nhaplai) ?>">
        <?php if (!empty($errors['matkhau_nhaplai'])) echo "<div class='error'>{$errors['matkhau_nhaplai']}</div>"; ?>

        <input type="submit" value="Đăng ký">
    </form>
</div>
</body>
</html>
