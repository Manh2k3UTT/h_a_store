<?php
ob_start();
include("../include/header.php");
include("../../model/database.php");

if (!isset($_GET['makm'])) {
    header("Location: khuyenmai.php");
    exit;
}

$makm = intval($_GET['makm']);

// Lấy thông tin khuyến mãi
$stmt = $conn->prepare("SELECT * FROM khuyenmai WHERE MaKM = ?");
$stmt->bind_param("i", $makm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Không tìm thấy khuyến mãi.";
    exit;
}

$row = $result->fetch_assoc();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkm = trim($_POST['tenkm']);
    $mota = trim($_POST['mota']);
    $km_pt = isset($_POST['km_pt']) ? trim($_POST['km_pt']) : "0";
    $TienKM = isset($_POST['TienKM']) ? trim($_POST['TienKM']) : "0";
    $ngaybd = $_POST['ngaybd'];
    $ngaykt = $_POST['ngaykt'];

    // Kiểm tra chỉ nhập một trong hai
    if ($km_pt !== "0" && $TienKM !== "0") {
        $errors[] = "Chỉ được nhập giảm phần trăm hoặc giảm số tiền, không được nhập cả hai.";
    }

    if (empty($tenkm)) {
        $errors[] = "Tên khuyến mãi không được để trống.";
    }

    if (empty($ngaybd) || empty($ngaykt)) {
        $errors[] = "Ngày bắt đầu và kết thúc không được để trống.";
    }

    if (empty($errors)) {
        $sql_update = "UPDATE khuyenmai SET TenKM=?, MoTa=?, KM_PT=?, TienKM=?, NgayBatDau=?, NgayKetThuc=? WHERE MaKM=?";
        $stmt_update = $conn->prepare($sql_update);
        $km_pt_val = floatval($km_pt);
        $TienKM_val = floatval($TienKM);
        $stmt_update->bind_param("ssdsssi", $tenkm, $mota, $km_pt_val, $TienKM_val, $ngaybd, $ngaykt, $makm);

        if ($stmt_update->execute()) {
            header("Location: khuyenmai.php");
            exit;
        } else {
            $errors[] = "Cập nhật thất bại. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Khuyến Mãi</title>
    <style>
        form {
            width: 400px;
            margin: 30px auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 6px;
            margin-top: 4px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #0a74da;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>

    <script>
        function toggleFields() {
            const km_pt = document.getElementById("km_pt");
            const TienKM = document.getElementById("TienKM");

            function isValueMeaningful(val) {
                val = val.trim();
                return val !== "" && val !== "0" && val !== "0.0" && val !== "0.00";
            }

            function updateState() {
                const km_pt_val = km_pt.value;
                const TienKM_val = TienKM.value;

                if (isValueMeaningful(km_pt_val) && isValueMeaningful(TienKM_val)) {
                    alert("Chỉ được nhập 1 trong 2: phần trăm hoặc số tiền.");
                    TienKM.value = "";
                    km_pt.disabled = false;
                    TienKM.disabled = true;
                } else if (isValueMeaningful(km_pt_val)) {
                    km_pt.disabled = false;
                    TienKM.disabled = true;
                } else if (isValueMeaningful(TienKM_val)) {
                    km_pt.disabled = true;
                    TienKM.disabled = false;
                } else {
                    km_pt.disabled = false;
                    TienKM.disabled = false;
                }
            }

            km_pt.addEventListener("input", updateState);
            TienKM.addEventListener("input", updateState);
            updateState();
        }

        window.onload = toggleFields;

        function validateKM() {
            let pt = document.getElementById("km_pt").value.trim();
            let tien = document.getElementById("TienKM").value.trim();

            function isValueMeaningful(val) {
                return val !== "" && val !== "0" && val !== "0.0" && val !== "0.00";
            }

            if (isValueMeaningful(pt) && isValueMeaningful(tien)) {
                alert("Chỉ được nhập giảm phần trăm hoặc giảm số tiền, không được nhập cả hai.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2 style="text-align: center;">Sửa Khuyến Mãi</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" onsubmit="return validateKM();">
        <label for="tenkm">Tên khuyến mãi:</label>
        <input type="text" id="tenkm" name="tenkm" value="<?= htmlspecialchars($row['TenKM']) ?>" required>

        <label for="mota">Mô tả:</label>
        <textarea id="mota" name="mota" rows="3"><?= htmlspecialchars($row['MoTa']) ?></textarea>

        <label for="km_pt">Giảm phần trăm (%):</label>
        <input type="number" step="0.01" id="km_pt" name="km_pt" value="<?= htmlspecialchars($row['KM_PT']) ?>">

        <label for="TienKM">Giảm số tiền (VND):</label>
        <input type="number" step="1000" id="TienKM" name="TienKM" value="<?= htmlspecialchars($row['TienKM']) ?>">

        <label for="ngaybd">Ngày bắt đầu:</label>
        <input type="date" id="ngaybd" name="ngaybd" value="<?= $row['NgayBatDau'] ?>" required>

        <label for="ngaykt">Ngày kết thúc:</label>
        <input type="date" id="ngaykt" name="ngaykt" value="<?= $row['NgayKetThuc'] ?>" required>

        <button type="submit">Lưu thay đổi</button>
    </form>
</body>
</html>
<?php
ob_end_flush(); // kết thúc và gửi dữ liệu đi
?>