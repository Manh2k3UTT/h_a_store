<?php
$conn = mysqli_connect("localhost", "root", "", "h_a");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$tennv = $_SESSION['tennv'] ?? 'Người dùng';
include '../include/header.php';
$maSP = $_GET['id'] ?? null;
if (!$maSP) {
    die("Không tìm thấy mã sản phẩm.");
}

// Lấy dữ liệu sản phẩm chính
$sql = "SELECT * FROM sanphamnu WHERE MaSP = '$maSP'";
$result = mysqli_query($conn, $sql);
$sanpham = mysqli_fetch_assoc($result);

// Lấy loại sản phẩm
$loai_sql = "SELECT * FROM chitietloainu";
$loai_result = mysqli_query($conn, $loai_sql);

// Lấy các kích cỡ
$kichco_result = mysqli_query($conn, "SELECT MaSize FROM kichco");

// Lấy các màu
$mau_result = mysqli_query($conn, "SELECT MaMau FROM mau");

// Lấy chi tiết sản phẩm đã có
$chitiet_result = mysqli_query($conn, "SELECT * FROM chitietsanphamnu WHERE MaSP = '$maSP'");
$ds_chitiet = [];
while ($row = mysqli_fetch_assoc($chitiet_result)) {
    $ds_chitiet[] = $row;
}

// Tạo mảng tra cứu chi tiết hiện có
$kichco_checked = [];
$mau_checked = [];
$mau_images = [];

foreach ($ds_chitiet as $ct) {
    if (!in_array($ct['MaSize'], $kichco_checked)) {
        $kichco_checked[] = $ct['MaSize'];
    }
    if (!in_array($ct['MaMau'], $mau_checked)) {
        $mau_checked[] = $ct['MaMau'];
        $mau_images[$ct['MaMau']] = $ct['HinhAnh'];
    }
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $_POST['TenSanPham'];
    $gia = $_POST['Gia'];
    $mota = $_POST['MoTa'];
    $loai = $_POST['LoaiSanPham'];

    // Cập nhật bảng sanphamnu
    mysqli_query($conn, "UPDATE sanphamnu SET TenSanPham='$ten', Gia='$gia', MoTa='$mota', MaChiTiet='$loai' WHERE MaSP='$maSP'");

    // Lấy dữ liệu được chọn
    $kichcos = $_POST['kichco'] ?? [];
    $maus = $_POST['mau'] ?? [];

    // Xoá các dòng không còn tồn tại
    mysqli_query($conn, "DELETE FROM chitietsanphamnu WHERE MaSP='$maSP'");

    // Thêm lại chi tiết mới
    foreach ($maus as $mau) {
        $tenFile = $_FILES["hinhanh_$mau"]['name'] ?? '';
        $tmpFile = $_FILES["hinhanh_$mau"]['tmp_name'] ?? '';
        $fileName = '';

        if ($tenFile && is_uploaded_file($tmpFile)) {
            $fileName = basename($tenFile);
            move_uploaded_file($tmpFile, "../../webroot/images/sanpham/" . $fileName);
        } elseif (isset($mau_images[$mau])) {
            $fileName = $mau_images[$mau]; // giữ ảnh cũ nếu không đổi
        }

        foreach ($kichcos as $kc) {
            mysqli_query($conn, "INSERT INTO chitietsanphamnu (MaSP, MaMau, MaSize, HinhAnh) VALUES ('$maSP', '$mau', '$kc', '$fileName')");
        }
    }

    echo "<script>alert('Cập nhật thành công'); window.location='sanphamnu.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <style>
        .color-box {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .color-box img {
            margin-left: 10px;
            max-height: 60px;
        }
    </style>
    <script>
        function toggleImageUpload(mau) {
            const checkbox = document.getElementById("mau_" + mau);
            const div = document.getElementById("upload_" + mau);
            if (checkbox.checked) {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>Sửa sản phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên sản phẩm: <input type="text" name="TenSanPham" value="<?= htmlspecialchars($sanpham['TenSanPham']) ?>"></label><br><br>
        <label>Giá: <input type="text" name="Gia" value="<?= htmlspecialchars($sanpham['Gia']) ?>"></label><br><br>
        <label>Loại sản phẩm:
            <select name="LoaiSanPham">
                <?php while ($row = mysqli_fetch_assoc($loai_result)) { ?>
                    <option value="<?= $row['MaChiTiet'] ?>" <?= $row['MaChiTiet'] == $sanpham['MaChiTiet'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['TenChiTiet']) ?>
                    </option>
                <?php } ?>
            </select>
        </label><br><br>
        <label>Mô tả:<br>
            <textarea name="MoTa" rows="4" cols="50"><?= htmlspecialchars($sanpham['MoTa']) ?></textarea>
        </label><br><br>

        <fieldset>
            <legend>Chọn kích cỡ:</legend>
            <?php while ($row = mysqli_fetch_assoc($kichco_result)) {
                $size = $row['MaSize']; ?>
                <label>
                    <input type="checkbox" name="kichco[]" value="<?= $size ?>" <?= in_array($size, $kichco_checked) ? 'checked' : '' ?>>
                    <?= $size ?>
                </label>
            <?php } ?>
        </fieldset><br>

        <fieldset>
            <legend>Chọn màu:</legend>
            <?php 
            mysqli_data_seek($mau_result, 0); 
            while ($row = mysqli_fetch_assoc($mau_result)) {
                $mau = $row['MaMau']; 
                $checked = in_array($mau, $mau_checked);
                $display = $checked ? '' : 'display:none;';
                $has_image = isset($mau_images[$mau]) && $mau_images[$mau] !== '';
                $image_src = $has_image ? "../../webroot/images/sanpham/" . $mau_images[$mau] : '';
            ?>
                <div class="color-box">
                    <label>
                        <input type="checkbox" id="mau_<?= $mau ?>" name="mau[]" value="<?= $mau ?>" onchange="toggleImageUpload('<?= $mau ?>')" <?= $checked ? 'checked' : '' ?>>
                        <?= $mau ?>
                    </label>

                    <div id="upload_<?= $mau ?>" style="margin-left:20px; <?= $display ?>">
                        Ảnh:
                        <?php if ($has_image): ?>
                            <img id="preview_<?= $mau ?>" src="<?= $image_src ?>" alt="ảnh hiện tại" style="max-width: 150px;"><br>
                            <span id="filename_<?= $mau ?>"><strong></strong> <?= $mau_images[$mau] ?></span>
                            <?php else: ?>
                            <img id="preview_<?= $mau ?>" style="display:none; max-width: 150px;"><br>
                            <span id="filename_<?= $mau ?>"></span>
                        <?php endif; ?>
                        <input type="file" name="hinhanh_<?= $mau ?>" onchange="previewImage(event, 'preview_<?= $mau ?>', 'filename_<?= $mau ?>')"><br>

                        
                    </div>
                </div>
            <?php } ?>
        </fieldset>
        
        <br>

        <button type="submit">Cập nhật sản phẩm</button>
    </form>
</body>
</html>
