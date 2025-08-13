<?php
require_once('../../model/database.php');
include('../include/header.php');

$search = $_GET['search'] ?? '';
$maLoai = $_GET['maloai'] ?? '';
$maChiTiet = $_GET['machitiet'] ?? '';
$mau = $_GET['mau'] ?? '';
$size = $_GET['size'] ?? '';
$gia = $_GET['gia'] ?? '';

// Lấy danh sách loại sản phẩm (bảng loainam) như file nam
$loai_query = mysqli_query($conn, "SELECT * FROM loainam WHERE GioiTinh = 'nu'");
$ds_loai = [];
while ($row = mysqli_fetch_assoc($loai_query)) {
    $ma = $row['MaLoai'];
    $chitiet_query = mysqli_query($conn, "SELECT * FROM chitietloainam WHERE MaLoai = '$ma'");
    $row['chitiet'] = [];
    while ($ct = mysqli_fetch_assoc($chitiet_query)) {
        $row['chitiet'][] = $ct;
    }
    $ds_loai[] = $row;
}

// Truy vấn sản phẩm nữ từ bảng sanphamnam với GioiTinh = 'nữ'
$sql = "SELECT * FROM sanphamnam WHERE GioiTinh = 'nữ'";

if (!empty($search)) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND TenSanPham LIKE '%$search_esc%'";
}

if (!empty($maChiTiet)) {
    $sql .= " AND MaChiTiet = '$maChiTiet'";
} elseif (!empty($maLoai)) {
    $ct_query = mysqli_query($conn, "SELECT MaChiTiet FROM chitietloainam WHERE MaLoai = '$maLoai'");
    $arr_ct = [];
    while ($row = mysqli_fetch_assoc($ct_query)) {
        $arr_ct[] = "'" . $row['MaChiTiet'] . "'";
    }
    if (!empty($arr_ct)) {
        $list = implode(",", $arr_ct);
        $sql .= " AND MaChiTiet IN ($list)";
    }
}

if (!empty($mau)) {
    $sql .= " AND MaSP IN (SELECT MaSP FROM chitietsanphamnam WHERE MaMau = '$mau')";
}

if (!empty($size)) {
    $sql .= " AND MaSP IN (SELECT MaSP FROM chitietsanphamnam WHERE MaSize = '$size')";
}

if (!empty($gia)) {
    if ($gia == '1') {
        $sql .= " AND Gia < 200000";
    } elseif ($gia == '2') {
        $sql .= " AND Gia BETWEEN 200000 AND 500000";
    } elseif ($gia == '3') {
        $sql .= " AND Gia > 500000";
    }
}

$sanpham_query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thời Trang Nữ</title>
    <link rel="stylesheet" href="../asset/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const titles = document.querySelectorAll('.category-title');
            titles.forEach(function(title) {
                title.addEventListener('click', function() {
                    const detail = this.nextElementSibling;
                    detail.classList.toggle('active');
                });
            });
        });
    </script>
</head>
<body>

<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="h_a"
  agent-id="29f07ae6-576a-4be6-8137-ca7eaf88dd59"
  language-code="vi"
></df-messenger>

<div class="container">
    <div class="sidebar">
        <h3>Danh mục</h3>
        <?php foreach ($ds_loai as $loai): ?>
            <div>
                <div class="category-title"><strong><?= htmlspecialchars($loai['TenLoai']) ?></strong></div>
                <div class="category-detail">
                    <div><a href="thoitrangnu.php?maloai=<?= $loai['MaLoai'] ?>">Tất cả <?= htmlspecialchars($loai['TenLoai']) ?></a></div>
                    <?php foreach ($loai['chitiet'] as $ct): ?>
                        <div><a href="thoitrangnu.php?machitiet=<?= $ct['MaChiTiet'] ?>"><?= htmlspecialchars($ct['TenChiTiet']) ?></a></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="main-content">
       <form method="GET" class="filter-form">
    <div class="search-bar">
        <input type="text" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Lọc</button>
    </div>
    <div class="filter-options">
        <select name="mau">
            <option value="">--Màu sắc--</option>
            <?php 
            $mau_query = mysqli_query($conn, "SELECT MaMau FROM mau");
            while ($row = mysqli_fetch_assoc($mau_query)) {
                $selected = ($mau == $row['MaMau']) ? 'selected' : '';
                echo "<option value='{$row['MaMau']}' $selected>{$row['MaMau']}</option>";
            }
            ?>
        </select>
        <select name="size">
            <option value="">--Kích cỡ--</option>
            <?php 
            $size_query = mysqli_query($conn, "SELECT MaSize FROM kichco");
            while ($row = mysqli_fetch_assoc($size_query)) {
                $selected = ($size == $row['MaSize']) ? 'selected' : '';
                echo "<option value='{$row['MaSize']}' $selected>{$row['MaSize']}</option>";
            }
            ?>
        </select>
        <select name="gia">
            <option value="">--Khoảng giá--</option>
            <option value="1" <?= ($gia == '1') ? 'selected' : '' ?>>Dưới 200.000đ</option>
            <option value="2" <?= ($gia == '2') ? 'selected' : '' ?>>200.000đ - 500.000đ</option>
            <option value="3" <?= ($gia == '3') ? 'selected' : '' ?>>Trên 500.000đ</option>
        </select>
    </div>
</form>


        <div class="product-list">
            <?php while ($row = mysqli_fetch_assoc($sanpham_query)): ?>
                <?php
                $masp = $row['MaSP'];
                $gia = $row['Gia'];

                // Lấy ảnh sản phẩm từ chitietsanphamnam
                $img_query = mysqli_query($conn, "SELECT HinhAnh FROM chitietsanphamnam WHERE MaSP = '$masp' LIMIT 1");
                $img_row = mysqli_fetch_assoc($img_query);
                $img = $img_row['HinhAnh'] ?? 'noimage.jpg';

                // Lấy khuyến mãi áp dụng
                $km_query = mysqli_query($conn, "
                    SELECT km.*
                    FROM sanpham_khuyenmai spkm
                    JOIN khuyenmai km ON spkm.MaKM = km.MaKM
                    WHERE spkm.MaSP = '$masp'
                    LIMIT 1
                ");
                $km = null;
                if ($km_query && mysqli_num_rows($km_query) > 0) {
                    $km = mysqli_fetch_assoc($km_query);
                }

                $today = date('Y-m-d');
                $giaKM = null;
                if ($km) {
                    if ($km['NgayBatDau'] <= $today && $today <= $km['NgayKetThuc']) {
                        if (!is_null($km['KM_PT']) && $km['KM_PT'] > 0) {
                            $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
                        } elseif (!is_null($km['TienKM']) && $km['TienKM'] > 0) {
                            $giaKM = $gia - $km['TienKM'];
                        }
                        if ($giaKM < 0) $giaKM = 0;
                    }
                }
                ?>

                <div class="product">
                    <a href="xemsanphamnu.php?id=<?= $masp ?>">
                        <img src="../../webroot/images/sanpham/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['TenSanPham']) ?>">
                    </a>
                    <h4><?= htmlspecialchars($row['TenSanPham']) ?></h4>
                    <?php if ($giaKM !== null): ?>
                        <span class="gia-goc"><?= number_format($gia) ?>đ</span>
                        <span class="gia-km"><?= number_format($giaKM) ?>đ</span>
                    <?php else: ?>
                        <span><?= number_format($gia) ?>đ</span>
                    <?php endif; ?>
                    <br>
                    <a href="xemsanphamnu.php?id=<?= $masp ?>">Xem chi tiết</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>
<?php
include('../include/footer.php');
?>