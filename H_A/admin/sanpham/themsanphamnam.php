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

// Lấy danh sách màu và kích cỡ
$mau_result = mysqli_query($conn, "SELECT MaMau FROM mau");
$size_result = mysqli_query($conn, "SELECT MaSize FROM kichco");

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['tensp'];
    $gia = $_POST['gia'];
    $gioitinh = $_POST['gioitinh'];
    $maloai = $_POST['maloai'];
    $mota = $_POST['mota'];

    // Thêm vào bảng sanpham (gộp chung)
    $sql_sp = "INSERT INTO sanphamnam (TenSanPham, Gia, GioiTinh, MaChiTiet, MoTa) 
               VALUES ('$ten', '$gia', '$gioitinh', '$maloai', '$mota')";
    if (mysqli_query($conn, $sql_sp)) {
        $masp = mysqli_insert_id($conn); // Lấy MaSP mới thêm

        // Xử lý màu và kích cỡ
        foreach ($_POST['mau'] as $mamau) {
            // Lưu ảnh theo màu
            if (isset($_FILES['anh_'.$mamau]) && $_FILES['anh_'.$mamau]['error'] == 0) {
                $file_name = basename($_FILES['anh_'.$mamau]['name']);
                $target_path = "C:/xampp/htdocs/H_A/webroot/images/sanpham/" . $file_name;
                move_uploaded_file($_FILES['anh_'.$mamau]['tmp_name'], $target_path);
            } else {
                $file_name = ''; // Trường hợp không có ảnh
            }

            // Lưu chi tiết sản phẩm (kích cỡ, màu, ảnh)
            if (!empty($_POST['size'])) {
                foreach ($_POST['size'] as $masize) {
                    $sql_ct = "INSERT INTO chitietsanphamnam (MaSP, HinhAnh, MaMau, MaSize)
                               VALUES ('$masp', '$file_name', '$mamau', '$masize')";
                    mysqli_query($conn, $sql_ct);
                }
            }
        }

        echo "<script>alert('Thêm sản phẩm thành công'); window.location='sanphamnam.php';</script>";
        exit;
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm</title>
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
    </style>
    <script>
        function toggleImageUpload(mauCheckbox) {
            const mamau = mauCheckbox.value;
            const container = document.getElementById("color-images-" + mamau);
            const inputId = "image_" + mamau;
            const existing = document.getElementById(inputId);

            if (mauCheckbox.checked) {
                let div = document.createElement("div");
                div.className = "color-image";
                div.id = inputId;
                div.innerHTML = `<label>Ảnh cho màu ${mamau}: <input type="file" name="anh_${mamau}" required></label>`;
                container.appendChild(div);
            } else if (existing) {
                container.removeChild(existing);
            }
        }

        function loadLoaiSanPham(gioitinh) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "getloai.php?gioitinh=" + gioitinh, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("maloai").innerHTML = xhr.responseText;
                } else {
                    alert("Không thể tải loại sản phẩm.");
                }
            };
            xhr.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            const gioiTinhSelect = document.getElementById("gioitinh");
            loadLoaiSanPham(gioiTinhSelect.value);
            gioiTinhSelect.addEventListener("change", function () {
                loadLoaiSanPham(this.value);
            });
        });
    </script>
</head>
<body>

<h2>Thêm sản phẩm</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Tên sản phẩm:</label>
    <input type="text" name="tensp" required>

    <label>Giá:</label>
    <input type="number" name="gia" required>

    <label>Giới tính:</label>
    <select name="gioitinh" id="gioitinh" required>
        <option value="nam">Nam</option>
        <option value="nu">Nữ</option>
    </select>

    <label>Loại sản phẩm:</label>
    <select name="maloai" id="maloai" required>
        <!-- Loại sản phẩm sẽ được load bằng AJAX -->
    </select>

    <label>Mô tả:</label>
    <textarea name="mota" required></textarea>

    <label>Chọn màu:</label>
    <div class="checkbox-group">
        <?php 
        mysqli_data_seek($mau_result, 0);
        while ($row = mysqli_fetch_assoc($mau_result)) { ?>
            <div>
                <input type="checkbox" name="mau[]" value="<?php echo $row['MaMau']; ?>" 
                    onclick="toggleImageUpload(this)"> <?php echo $row['MaMau']; ?>
                <div id="color-images-<?php echo $row['MaMau']; ?>"></div>
            </div>
        <?php } ?>
    </div>

    <label>Chọn kích cỡ:</label>
    <div class="checkbox-group">
        <?php 
        mysqli_data_seek($size_result, 0);
        while ($row = mysqli_fetch_assoc($size_result)) { ?>
            <div>
                <input type="checkbox" name="size[]" value="<?php echo $row['MaSize']; ?>">
                <?php echo $row['MaSize']; ?>
            </div>
        <?php } ?>
    </div>

    <button type="submit">Thêm sản phẩm</button>
</form>

</body>
</html>
