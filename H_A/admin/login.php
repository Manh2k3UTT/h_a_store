<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["matkhau"] ?? '');

    if ($email === '' || $password === '') {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $sql = "SELECT * FROM nhanvien WHERE Email = ? AND MatKhau = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['manv'] = $user['MaNV'];
            $_SESSION['tennv'] = $user['TenNV'] ?? $user['HoTen'] ?? 'Nhân viên';
            $_SESSION['Quyen'] = $user['Quyen'];

            header("Location: ../admin/index/index.php");
            exit;
        } else {
            $error = "Sai email hoặc mật khẩu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0; padding: 0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            color: #333;
        }
        .login-box {
            background: white;
            padding: 30px 40px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
            width: 350px;
        }
        h4 {
            text-align: center;
            margin-bottom: 25px;
            color: #d6336c; /* màu hồng đậm */
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #d6336c;
            outline: none;
        }
        button {
            width: 100%;
            background-color: #d6336c;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #b02856;
        }
        .error-msg {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #f5c2c7;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h4>Đăng nhập hệ thống</h4>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required />
            </div>

            <div class="form-group">
                <label for="matkhau">Mật khẩu:</label>
                <input type="password" id="matkhau" name="matkhau" required />
            </div>

            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
