<?php
session_start();
$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$email = $matkhau = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $matkhau = $_POST["matkhau"];

    if (empty($email)) $errors['email'] = "Vui lòng nhập email.";
    if (empty($matkhau)) $errors['matkhau'] = "Vui lòng nhập mật khẩu.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT MaKH, TenKH FROM khachhang WHERE Email = ? AND MatKhau = ?");
        $stmt->bind_param("ss", $email, $matkhau);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION["MaKH"] = $row["MaKH"];
            $_SESSION["TenKH"] = $row["TenKH"];

            // 🔁 Lấy lại giỏ hàng tạm nếu có
            $_SESSION['giohang'] = []; // Xóa giỏ cũ nếu có
            $makh = $_SESSION['MaKH'];
            $gio_query = $conn->query("SELECT * FROM giohang_tam WHERE MaKH = '$makh'");

            while ($item = $gio_query->fetch_assoc()) {
                $key = $item['MaSP'] . '_' . $item['MaMau'] . '_' . $item['MaSize'];

                // Có thể lấy thêm thông tin sản phẩm nếu muốn
                $tensp = '';
                $gia = 0;
                $hinh = 'noimage.jpg';

                $sp = $conn->query("SELECT TenSanPham, Gia FROM sanphamnam WHERE MaSP = '{$item['MaSP']}'");
                if ($sp && $sp->num_rows > 0) {
                    $data = $sp->fetch_assoc();
                    $tensp = $data['TenSanPham'];
                    $gia = $data['Gia'];
                }

                $ha = $conn->query("SELECT HinhAnh FROM chitietsanphamnam WHERE MaSP = '{$item['MaSP']}' AND MaMau = '{$item['MaMau']}' LIMIT 1");
                if ($ha && $ha->num_rows > 0) {
                    $data = $ha->fetch_assoc();
                    $hinh = $data['HinhAnh'];
                }

                $_SESSION['giohang'][$key] = [
                    'masp' => $item['MaSP'],
                    'ten' => $tensp,
                    'gia' => $gia,
                    'mau' => $item['MaMau'],
                    'size' => $item['MaSize'],
                    'soluong' => $item['SoLuong'],
                    'hinh' => $hinh
                ];
            }

            header("Location: index.php");
            exit();
        }

        } else {
            $errors['login'] = "Email hoặc mật khẩu không đúng.";
        }

        $stmt->close();
    }


$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 40px 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.95em;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Đăng nhập</h2>

    <?php if (!empty($errors['login'])) echo "<div class='error'>{$errors['login']}</div>"; ?>

    <form method="post">
        <label>Email:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($email) ?>">
        <?php if (!empty($errors['email'])) echo "<div class='error'>{$errors['email']}</div>"; ?>

        <label>Mật khẩu:</label>
        <input type="password" name="matkhau" value="<?= htmlspecialchars($matkhau) ?>">
        <?php if (!empty($errors['matkhau'])) echo "<div class='error'>{$errors['matkhau']}</div>"; ?>

        <input type="submit" value="Đăng nhập">
    </form>

    <p class="register-link">
        Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a>
    </p>
</div>
</body>
</html>
