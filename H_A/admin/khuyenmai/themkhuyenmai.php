<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';

include("../../model/database.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkm = $_POST["tenkm"] ?? '';
    $mota = $_POST["mota"] ?? '';
    $km_pt = (int)($_POST["km_pt"] ?? 0);
    $tienkm = (int)($_POST["tienkm"] ?? 0);
    $ngaybatdau = $_POST["ngaybatdau"] ?? '';
    $ngayketthuc = $_POST["ngayketthuc"] ?? '';

    // Validate cơ bản
    if (empty($tenkm)) $errors[] = "Tên khuyến mãi không được để trống.";
    if (empty($ngaybatdau)) $errors[] = "Vui lòng chọn ngày bắt đầu.";
    if (empty($ngayketthuc)) $errors[] = "Vui lòng chọn ngày kết thúc.";

    // Kiểm tra chỉ chọn một loại khuyến mãi
    if (($km_pt > 0 && $tienkm > 0) || ($km_pt == 0 && $tienkm == 0)) {
        $errors[] = "Chỉ được nhập hoặc khuyến mãi phần trăm hoặc số tiền, không được nhập cả hai hoặc bỏ trống cả hai.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO khuyenmai (TenKM, MoTa, KM_PT, TienKM, NgayBatDau, NgayKetThuc) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $tenkm, $mota, $km_pt, $tienkm, $ngaybatdau, $ngayketthuc);

        if ($stmt->execute()) {
            header("Location: khuyenmai.php");
            exit();
        } else {
            $errors[] = "Lỗi thêm dữ liệu: " . $stmt->error;
        }
    }
}
include("../include/header.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khuyến mãi</title>
    <style>
        form {
            width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        .btn-submit {
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .note {
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Thêm khuyến mãi</h2>

<form method="post" action="">
    <label for="tenkm">Tên khuyến mãi:</label>
    <input type="text" name="tenkm" id="tenkm" required>

    <label for="mota">Mô tả:</label>
    <textarea name="mota" id="mota" rows="3"></textarea>

    <label for="km_pt">% giảm (%):</label>
    <input type="number" name="km_pt" id="km_pt" min="0" max="100">
    
    <label for="tienkm">Tiền giảm (vnđ):</label>
    <input type="number" name="tienkm" id="tienkm" min="0">
    <div class="note">* Chỉ nhập <strong>một</strong> trong hai: % giảm hoặc tiền giảm</div>

    <label for="ngaybatdau">Ngày bắt đầu:</label>
    <input type="date" name="ngaybatdau" id="ngaybatdau" required>

    <label for="ngayketthuc">Ngày kết thúc:</label>
    <input type="date" name="ngayketthuc" id="ngayketthuc" required>

    <button type="submit" class="btn-submit">Thêm khuyến mãi</button>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</form>

</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ptInput = document.getElementById("km_pt");
    const tienInput = document.getElementById("tienkm");

    ptInput.addEventListener("input", function () {
        if (ptInput.value.trim() !== "") {
            tienInput.disabled = true;
        } else {
            tienInput.disabled = false;
        }
    });

    tienInput.addEventListener("input", function () {
        if (tienInput.value.trim() !== "") {
            ptInput.disabled = true;
        } else {
            ptInput.disabled = false;
        }
    });
});
</script>
