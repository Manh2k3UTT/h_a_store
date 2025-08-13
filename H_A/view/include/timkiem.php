<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối CSDL lỗi: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';
$mau = $_GET['mau'] ?? '';
$size = $_GET['size'] ?? '';
$gia = $_GET['gia'] ?? '';
global $conn;
$sql = "SELECT * FROM sanphamnam WHERE 1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND TenSanPham LIKE '%$search%'";
}
if (!empty($_GET['gioitinh'])) {
    $gioitinh = $_GET['gioitinh'];
    $sql .= " AND GioiTinh = '$gioitinh'";
}
if (!empty($mau)) {
    $sql .= " AND MaSP IN (SELECT MaSP FROM chitietsanphamnam WHERE MaMau = '$mau')";
}
if (!empty($size)) {
    $sql .= " AND MaSP IN (SELECT MaSP FROM chitietsanphamnam WHERE MaSize = '$size')";
}
if (!empty($gia)) {
    if ($gia == '1') $sql .= " AND Gia < 200000";
    elseif ($gia == '2') $sql .= " AND Gia BETWEEN 200000 AND 500000";
    elseif ($gia == '3') $sql .= " AND Gia > 500000";
}
$sanpham_query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .main-content {
            margin-bottom: 60px;
        }
        h2 {
            text-align: center;
            color: #d32f2f;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 60px;
        }
        .product {
            width: 220px;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }
        .product img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }
        .gia-goc {
            text-decoration: line-through;
            color: gray;
            margin-right: 6px;
        }
        .gia-km {
            color: red;
            font-weight: bold;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #f1f1f1;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>
<div class="container">
    <div class="main-content">
        <h2>Kết quả tìm kiếm</h2>
        <div class="product-list">
            <?php while ($row = mysqli_fetch_assoc($sanpham_query)): ?>
                <?php
                $masp = $row['MaSP'];
                $gia = $row['Gia'];

                $img = 'noimage.jpg';
                $img_query = mysqli_query($conn, "SELECT HinhAnh FROM chitietsanphamnam WHERE MaSP = '$masp' LIMIT 1");
                if ($img_query && mysqli_num_rows($img_query) > 0) {
                    $img_row = mysqli_fetch_assoc($img_query);
                    if (!empty($img_row['HinhAnh'])) {
                        $img = $img_row['HinhAnh'];
                    }
                }

                $km = null;
                $km_query = mysqli_query($conn, "
                    SELECT km.* FROM sanpham_khuyenmai spkm
                    JOIN khuyenmai km ON spkm.MaKM = km.MaKM
                    WHERE spkm.MaSP = '$masp' LIMIT 1
                ");
                if ($km_query && mysqli_num_rows($km_query) > 0) {
                    $km = mysqli_fetch_assoc($km_query);
                }

                $today = date('Y-m-d');
                $giaKM = null;
                if ($km && $km['NgayBatDau'] <= $today && $today <= $km['NgayKetThuc']) {
                    if (!empty($km['KM_PT'])) {
                        $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
                    } elseif (!empty($km['TienKM'])) {
                        $giaKM = $gia - $km['TienKM'];
                    }
                    if ($giaKM < 0) $giaKM = 0;
                }

                $link = "/H_A/view/sanpham/";
                $link .= ($row['GioiTinh'] ?? 'nam') == 'nu' ? "xemsanphamnu.php" : "xemsanphamnam.php";
                $link .= "?id=$masp";
                ?>
                <div class="product">
                    <a href="<?= $link ?>">
                        <img src="/H_A/webroot/images/sanpham/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['TenSanPham']) ?>">
                    </a>
                    <h4><?= htmlspecialchars($row['TenSanPham']) ?></h4>
                    <?php if ($giaKM !== null): ?>
                        <span class="gia-goc"><?= number_format($gia) ?>đ</span>
                        <span class="gia-km"><?= number_format($giaKM) ?>đ</span>
                    <?php else: ?>
                        <span><?= number_format($gia) ?>đ</span>
                    <?php endif; ?>
                    <br>
                    <a href="<?= $link ?>">Xem chi tiết</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
</body>
</html>
