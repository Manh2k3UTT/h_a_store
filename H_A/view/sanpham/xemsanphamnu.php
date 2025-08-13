<?php
session_start();
require_once('../../model/database.php');


$masp = $_GET['id'] ?? '';

if (!$masp) {
    echo "Không tìm thấy sản phẩm.";
    exit;
}

// Lấy thông tin sản phẩm
$sp_query = mysqli_query($conn, "SELECT * FROM sanphamnam WHERE MaSP = '$masp'");
$sanpham = mysqli_fetch_assoc($sp_query);
if (!$sanpham) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

$gia = $sanpham['Gia'];
$today = date('Y-m-d');

// Kiểm tra khuyến mãi có hiệu lực
$km_query = mysqli_query($conn, "
    SELECT km.*
    FROM sanpham_khuyenmai spkm
    JOIN khuyenmai km ON spkm.MaKM = km.MaKM
    WHERE spkm.MaSP = '$masp'
      AND km.NgayBatDau <= '$today'
      AND km.NgayKetThuc >= '$today'
    LIMIT 1
");

$km = null;
if ($km_query && mysqli_num_rows($km_query) > 0) {
    $km = mysqli_fetch_assoc($km_query);
}

$giaKM = null;
if ($km) {
    if (!is_null($km['KM_PT']) && $km['KM_PT'] > 0) {
        $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
    } elseif (!is_null($km['TienKM']) && $km['TienKM'] > 0) {
        $giaKM = $gia - $km['TienKM'];
    }
    if ($giaKM < 0) $giaKM = 0;
}

// Màu và ảnh
$mau_query = mysqli_query($conn, "SELECT DISTINCT MaMau, HinhAnh FROM chitietsanphamnam WHERE MaSP = '$masp'");
$ds_mau = [];
while ($row = mysqli_fetch_assoc($mau_query)) {
    $ds_mau[] = $row;
}

// Kích cỡ
$size_query = mysqli_query($conn, "SELECT DISTINCT MaSize FROM chitietsanphamnam WHERE MaSP = '$masp'");
$ds_size = [];
while ($row = mysqli_fetch_assoc($size_query)) {
    $ds_size[] = $row['MaSize'];
}

// Số lượng theo màu + size
$sl_query = mysqli_query($conn, "SELECT MaMau, MaSize, SoLuong FROM chitietsanphamnam WHERE MaSP = '$masp'");
$so_luong_map = [];
while ($row = mysqli_fetch_assoc($sl_query)) {
    $so_luong_map[$row['MaMau']][$row['MaSize']] = $row['SoLuong'];
}

// Danh mục bên trái
$loai_query = mysqli_query($conn, "SELECT * FROM loainam WHERE GioiTinh = 'nu'");
$ds_loai = [];
while ($row = mysqli_fetch_assoc($loai_query)) {
    $ma = $row['MaLoai'];
    $ct_query = mysqli_query($conn, "SELECT * FROM chitietloainam WHERE MaLoai = '$ma'");
    $row['chitiet'] = [];
    while ($ct = mysqli_fetch_assoc($ct_query)) {
        $row['chitiet'][] = $ct;
    }
    $ds_loai[] = $row;
}

// --- Xử lý gửi bình luận ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noidung_binhluan'])) {
    if (!isset($_SESSION['MaKH'])) {
        echo "<script>alert('Vui lòng đăng nhập để bình luận!'); window.location.href='../login.php';</script>";
        exit;
    }

    $noidung = trim(mysqli_real_escape_string($conn, $_POST['noidung_binhluan']));
    $makh = $_SESSION['MaKH'];
    $thoigian = date('Y-m-d H:i:s');

    if ($noidung !== '') {
        $insert_sql = "INSERT INTO binhluan (MaSP, MaKH, NoiDung, ThoiGian) VALUES ('$masp', '$makh', '$noidung', '$thoigian')";
        mysqli_query($conn, $insert_sql);
        // Reload lại trang để tránh gửi lại khi refresh
        header("Location: xemsanphamnu.php?id=$masp#binhluan");
        exit;
    } else {
        $error_binhluan = "Nội dung bình luận không được để trống.";
    }
}

// Lấy danh sách bình luận
$bl_query = mysqli_query($conn, "
    SELECT bl.*, kh.TenKH 
    FROM binhluan bl 
    JOIN khachhang kh ON bl.MaKH = kh.MaKH
    WHERE bl.MaSP = '$masp' 
    ORDER BY bl.ThoiGian DESC
");
$binhluan_list = [];
while ($row = mysqli_fetch_assoc($bl_query)) {
    $binhluan_list[] = $row;
}
include('../include/header.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($sanpham['TenSanPham']) ?></title>
    <link rel="stylesheet" href="../asset/style.css">
    <script>
        function showImage(src) {
            document.getElementById("preview").src = src;
        }

        function updateStockStatus() {
            const mau = document.querySelector('input[name="mau"]:checked')?.value;
            const size = document.querySelector('input[name="size"]:checked')?.value;
            const stockMap = <?= json_encode($so_luong_map) ?>;
            const statusDiv = document.getElementById("stock-status");
            const actionButtons = document.getElementById("action-buttons");
            const quantityInput = document.querySelector('input[name="soluong"]');

            if (mau && size) {
                const sl = stockMap[mau]?.[size] ?? 0;
                if (sl > 0) {
                    statusDiv.innerText = "Còn hàng: " + sl;
                    quantityInput.max = sl;
                    if (parseInt(quantityInput.value) > sl) {
                        quantityInput.value = sl;
                    }
                    actionButtons.style.display = 'block';
                } else {
                    statusDiv.innerText = "Hết hàng";
                    quantityInput.max = 1;
                    quantityInput.value = 1;
                    actionButtons.style.display = 'none';
                }
            } else {
                statusDiv.innerText = "Vui lòng chọn màu và kích cỡ";
                quantityInput.max = 1;
                quantityInput.value = 1;
                actionButtons.style.display = 'none';
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('input[name="mau"], input[name="size"]').forEach(input => {
                input.addEventListener("change", updateStockStatus);
            });
            updateStockStatus();

            const form = document.getElementById("giohang-form");
            form.addEventListener("submit", function (e) {
                <?php if (!isset($_SESSION['MaKH'])): ?>
                e.preventDefault();
                alert("Vui lòng đăng nhập để mua hàng.");
                window.location.href = "../login.php";
                <?php endif; ?>
            });

            // Nút Ẩn / Hiện bình luận
            const toggleBtn = document.getElementById('toggle-binhluan-btn');
            const blList = document.getElementById('binhluan-list');
            if (toggleBtn && blList) {
                toggleBtn.addEventListener('click', () => {
                    if (blList.style.display === 'none') {
                        blList.style.display = 'block';
                        toggleBtn.innerText = 'Ẩn bình luận';
                    } else {
                        blList.style.display = 'none';
                        toggleBtn.innerText = 'Hiện bình luận';
                    }
                });
            }

            document.querySelectorAll('input[name="mau"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) {
                        // Lấy img trong label tương ứng
                        const label = this.closest('label');
                        const img = label.querySelector('img');
                        if(img) {
                            const newSrc = img.getAttribute('data-image');
                            // Thay đổi ảnh đại diện
                            document.getElementById('product-image').src = newSrc;
                        }
                    }
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
        <?php foreach ($ds_loai as $index => $loai): ?>
            <div>
                <div class="category-title" onclick="toggleDetail('ct<?= $index ?>')"><strong><?= htmlspecialchars($loai['TenLoai']) ?></strong></div>
                <div class="category-detail" id="ct<?= $index ?>" style="display:none;">
                    <?php foreach ($loai['chitiet'] as $ct): ?>
                        <div><a href="../sanpham/thoitrangnu.php?machitiet=<?= htmlspecialchars($ct['MaChiTiet']) ?>"><?= htmlspecialchars($ct['TenChiTiet']) ?></a></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    


    <div class="main-content-detail">
        <div class="product-detail">
            <div class="image-preview">
                <img id="product-image" src="../../webroot/images/sanpham/<?= htmlspecialchars($ds_mau[0]['HinhAnh']) ?>" alt="Ảnh sản phẩm đại diện" style="width: 300px; height: auto;">
            </div>

            <div class="info">
                <h2><?= htmlspecialchars($sanpham['TenSanPham']) ?></h2>
                <div>
                    <?php if ($giaKM !== null): ?>
                        <span class="gia-goc"><?= number_format($gia) ?> đ</span>
                        <span class="gia-km"><?= number_format($giaKM) ?> đ</span>
                    <?php else: ?>
                        <span><?= number_format($gia) ?> đ</span>
                    <?php endif; ?>
                </div>
                <p><?= nl2br(htmlspecialchars($sanpham['MoTa'])) ?></p>

                <form method="post" action="../giohang/themgiohang.php" id="giohang-form">
                    <input type="hidden" name="masp" value="<?= htmlspecialchars($masp) ?>">
                    
                    <p><strong>Chọn màu:</strong></p>
                    <div class="color-options">
                        <?php foreach ($ds_mau as $index => $m): ?>
                            <label>
                                <input type="radio" name="mau" value="<?= htmlspecialchars($m['MaMau']) ?>" <?= $index === 0 ? 'checked' : '' ?>>
                                <img src="../../webroot/images/sanpham/<?= htmlspecialchars($m['HinhAnh']) ?>" alt="Màu <?= htmlspecialchars($m['MaMau']) ?>" data-image="../../webroot/images/sanpham/<?= htmlspecialchars($m['HinhAnh']) ?>" width="40" height="40" style="border: 1px solid #ccc; margin-right:5px; vertical-align: middle;">
                                <?= htmlspecialchars($m['MaMau']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <p><strong>Chọn kích cỡ:</strong></p>
                    <div class="size-options">
                        <?php foreach ($ds_size as $size): ?>
                            <label>
                                <input type="radio" name="size" value="<?= htmlspecialchars($size) ?>">
                                <?= htmlspecialchars($size) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <p id="stock-status" style="color: red; font-weight: bold;"></p>

                    <p><strong>Số lượng:</strong>
                        <input type="number" name="soluong" value="1" min="1" max="10" style="width: 50px;">
                    </p>

                    <div id="action-buttons" style="margin-top: 10px; display: none;">
                        <button type="submit" name="action" value="add" class="btn-add-cart">Thêm vào giỏ hàng</button>
                        <button type="submit" name="action" value="buy" class="btn-buy-now">Mua ngay</button>
                    </div>
                </form>
            </div>
        </div>

        <hr>

        <div class="comment-section">
            <h3>Bình luận sản phẩm</h3>
            <button id="toggle-binhluan-btn">Ẩn bình luận</button>
            <div id="binhluan-list">
                <?php foreach ($binhluan_list as $bl): ?>
                    <div class="comment">
                        <b><?= htmlspecialchars($bl['TenKH']) ?></b> - <i><?= $bl['ThoiGian'] ?></i>
                        <p><?= nl2br(htmlspecialchars($bl['NoiDung'])) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (count($binhluan_list) === 0): ?>
                    <p>Chưa có bình luận nào.</p>
                <?php endif; ?>
            </div>

            <form method="post" action="#binhluan" style="margin-top: 15px;">
                <textarea name="noidung_binhluan" rows="3" cols="50" placeholder="Viết bình luận..."></textarea><br>
                <?php if (!empty($error_binhluan)) echo "<p style='color:red;'>$error_binhluan</p>"; ?>
                <button type="submit">Gửi bình luận</button>
            </form>
        </div>

    </div>
</div>

<script>
    function toggleDetail(id) {
        const el = document.getElementById(id);
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }
</script>

</body>
</html>
<?php
include('../include/footer.php');
?>