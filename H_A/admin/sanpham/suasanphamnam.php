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

// Lấy dữ liệu sản phẩm chính (bảng chung sanpham)
$sql = "SELECT * FROM sanphamnam WHERE MaSP = '$maSP'";
$result = mysqli_query($conn, $sql);
$sanpham = mysqli_fetch_assoc($result);
if (!$sanpham) {
    die("Sản phẩm không tồn tại.");
}

// Lấy danh sách màu và kích cỡ
$mau_result = mysqli_query($conn, "SELECT MaMau FROM mau");
$kichco_result = mysqli_query($conn, "SELECT MaSize FROM kichco");

// Lấy chi tiết sản phẩm đã có
$chitiet_result = mysqli_query($conn, "SELECT * FROM chitietsanphamnam WHERE MaSP = '$maSP'");
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

// Lấy loại sản phẩm theo giới tính hiện tại của sản phẩm để hiển thị
$gioitinh = $sanpham['GioiTinh'] ?? 'nam';
$loai_sql = "SELECT * FROM chitietloainam WHERE MaLoai IN (
                SELECT MaLoai FROM loainam WHERE GioiTinh = '$gioitinh'
             )";
$loai_result = mysqli_query($conn, $loai_sql);

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $_POST['TenSanPham'];
    $gia = $_POST['Gia'];
    $mota = $_POST['MoTa'];
    $gioitinh_post = $_POST['GioiTinh'];
    $loai = $_POST['LoaiSanPham'];

    // Cập nhật bảng sanpham
    $sql_update = "UPDATE sanphamnam SET TenSanPham='$ten', Gia='$gia', MoTa='$mota', GioiTinh='$gioitinh_post', MaChiTiet='$loai' WHERE MaSP='$maSP'";
    mysqli_query($conn, $sql_update);

    // Lấy dữ liệu được chọn
    $kichcos = $_POST['kichco'] ?? [];
    $maus = $_POST['mau'] ?? [];

    // Xoá các dòng chi tiết cũ
    mysqli_query($conn, "DELETE FROM chitietsanphamnam WHERE MaSP='$maSP'");

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
            mysqli_query($conn, "INSERT INTO chitietsanphamnam (MaSP, MaMau, MaSize, HinhAnh) VALUES ('$maSP', '$mau', '$kc', '$fileName')");
        }
    }

    echo "<script>alert('Cập nhật thành công'); window.location='sanphamnam.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
            color: #c40000;
        }
        form {
            max-width: 600px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button[type="submit"] {
            background-color: #c40000;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            width: 100%;
        }
        button[type="submit"]:hover {
            background-color: #a00000;
        }
        .checkbox-group {
            margin-top: 5px;
        }
        .checkbox-group div {
            margin-bottom: 8px;
        }
        .color-image {
            margin-left: 20px;
        }
        .color-image img {
            width: 400px;
            height: 300px;
        }
        .color-box {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
        }

        .color-box label {
            min-width: 80px;
        }

        .color-box img {
            width: 100px;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
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

        // AJAX load loại sản phẩm theo giới tính
        function loadLoaiSanPham(gioitinh) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "getloai.php?gioitinh=" + gioitinh, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("maloai").innerHTML = xhr.responseText;

                    // Nếu có loại sản phẩm cũ, cố gắng giữ lại lựa chọn
                    const oldLoai = "<?= $sanpham['MaChiTiet'] ?>";
                    const select = document.getElementById("maloai");
                    for(let i=0; i < select.options.length; i++) {
                        if(select.options[i].value === oldLoai) {
                            select.options[i].selected = true;
                            break;
                        }
                    }
                } else {
                    alert("Không thể tải loại sản phẩm.");
                }
            };
            xhr.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Load loại sản phẩm theo giới tính hiện tại
            const gioiTinhSelect = document.getElementById("gioitinh");
            loadLoaiSanPham(gioiTinhSelect.value);

            gioiTinhSelect.addEventListener("change", function () {
                loadLoaiSanPham(this.value);
            });

            // Hiển thị upload ảnh nếu màu đã check sẵn
            <?php foreach ($mau_checked as $mau): ?>
            toggleImageUpload("<?= $mau ?>");
            <?php endforeach; ?>
        });
    </script>
</head>
<body>
    <h2>Sửa sản phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên sản phẩm: 
            <input type="text" name="TenSanPham" value="<?= htmlspecialchars($sanpham['TenSanPham']) ?>" required>
        </label><br><br>

        <label>Giá: 
            <input type="number" name="Gia" value="<?= htmlspecialchars($sanpham['Gia']) ?>" required>
        </label><br><br>

        <label>Giới tính:
            <select name="GioiTinh" id="gioitinh" required>
                <option value="nam" <?= $sanpham['GioiTinh']=='nam' ? 'selected' : '' ?>>Nam</option>
                <option value="nu" <?= $sanpham['GioiTinh']=='nu' ? 'selected' : '' ?>>Nữ</option>
            </select>
        </label><br><br>

        <label>Loại sản phẩm:
            <select name="LoaiSanPham" id="maloai" required>
                <!-- Loại sản phẩm load AJAX -->
            </select>
        </label><br><br>

        <label>Mô tả:<br>
            <textarea name="MoTa" rows="4" cols="50" required><?= htmlspecialchars($sanpham['MoTa']) ?></textarea>
        </label><br><br>

        <fieldset>
            <legend>Chọn kích cỡ:</legend>
            <?php 
            // reset con trỏ
            mysqli_data_seek($kichco_result, 0);
            while ($row = mysqli_fetch_assoc($kichco_result)) {
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

                    <div id="upload_<?= $mau ?>" style="<?= $display ?>">
                        <input type="file" name="hinhanh_<?= $mau ?>" accept="image/*">
                        <?php if ($has_image): ?>
                            <img src="<?= $image_src ?>" alt="Ảnh màu <?= $mau ?>">
                        <?php endif; ?>
                    </div>
                </div>
            <?php } ?>
        </fieldset><br>

        <button type="submit">Cập nhật</button>
        <a href="sanphamnam.php"><button type="button">Hủy</button></a>
    </form>
</body>
</html>
