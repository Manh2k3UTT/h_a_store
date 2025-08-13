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
// Lấy danh sách loại sản phẩm
$loaisp = mysqli_query($conn, "SELECT * FROM chitietloainu");

// Lấy danh sách màu và kích cỡ
$mau_result = mysqli_query($conn, "SELECT MaMau FROM mau");
$size_result = mysqli_query($conn, "SELECT MaSize FROM kichco");

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['tensp'];
    $gia = $_POST['gia'];
    $maloai = $_POST['maloai'];
    $mota = $_POST['mota'];

    // Thêm vào bảng sanphamnu
    $sql_sp = "INSERT INTO sanphamnu (TenSanPham, Gia, MaChiTiet, MoTa) VALUES ('$ten', '$gia', '$maloai', '$mota')";
    if (mysqli_query($conn, $sql_sp)) {
        $masp = mysqli_insert_id($conn); // Lấy MaSP mới thêm

        // Xử lý màu và kích cỡ
        foreach ($_POST['mau'] as $mamau) {
            // Lưu ảnh
            if (isset($_FILES['anh_'.$mamau]) && $_FILES['anh_'.$mamau]['error'] == 0) {
                $file_name = basename($_FILES['anh_'.$mamau]['name']);
                $target_path = "C:/xampp/htdocs/H_A/webroot/images/sanpham/" . $file_name;
                move_uploaded_file($_FILES['anh_'.$mamau]['tmp_name'], $target_path);
            } else {
                $file_name = ''; // Trường hợp không có ảnh
            }

            // Lưu từng kích cỡ tương ứng với màu
            if (!empty($_POST['size'])) {
                foreach ($_POST['size'] as $masize) {
                    $sql_ct = "INSERT INTO chitietsanphamnu (MaSP, HinhAnh, MaMau, MaSize)
                               VALUES ('$masp', '$file_name', '$mamau', '$masize')";
                    mysqli_query($conn, $sql_ct);
                }
            }
        }

        echo "<script>alert('Thêm sản phẩm thành công'); window.location='sanphamnu.php';</script>";
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
        .color-image { margin-bottom: 10px; }
        .color-image img {
            width: 400px;
            height: 300px;
        }
    </style>
    <script>
        function toggleImageUpload(mauCheckbox) {
            const mamau = mauCheckbox.value;
            const container = document.getElementById("color-images-" + mamau);  // Container cho mỗi màu
            const inputId = "image_" + mamau;
            const existing = document.getElementById(inputId);

            if (mauCheckbox.checked) {
                // Tạo container chứa input file
                let div = document.createElement("div");
                div.className = "color-image";
                div.id = inputId;
                div.innerHTML = `<label>Ảnh cho màu ${mamau}: <input type="file" name="anh_${mamau}" required></label>`;
                container.appendChild(div);
            } else if (existing) {
                container.removeChild(existing);
            }
        }
    </script>
</head>
<body>

<h2>Thêm sản phẩm nữ</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Tên sản phẩm:</label><br>
    <input type="text" name="tensp" required><br><br>

    <label>Giá:</label><br>
    <input type="number" name="gia" required><br><br>

    <label>Loại sản phẩm:</label><br>
    <select name="maloai" required>
        <?php while ($row = mysqli_fetch_assoc($loaisp)) { ?>
            <option value="<?php echo $row['MaChiTiet']; ?>"><?php echo $row['TenChiTiet']; ?></option>
        <?php } ?>
    </select><br><br>

    <label>Mô tả:</label><br>
    <textarea name="mota" required></textarea><br><br>

    <label>Chọn màu:</label><br>
    <?php while ($row = mysqli_fetch_assoc($mau_result)) { ?>
        <div>
            <input type="checkbox" name="mau[]" value="<?php echo $row['MaMau']; ?>"
                   onclick="toggleImageUpload(this)"> <?php echo $row['MaMau']; ?>
            <div id="color-images-<?php echo $row['MaMau']; ?>"></div> <!-- Thêm container cho ảnh dưới mỗi màu -->
        </div><br>
    <?php } ?>
    <div id="color-images"></div><br>

    <label>Chọn kích cỡ:</label><br>
    <?php while ($row = mysqli_fetch_assoc($size_result)) { ?>
        <input type="checkbox" name="size[]" value="<?php echo $row['MaSize']; ?>">
        <?php echo $row['MaSize']; ?><br>
    <?php } ?><br>

    <button type="submit">Thêm sản phẩm</button>
</form>

</body>
</html>
