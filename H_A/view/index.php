<?php
require_once('../model/database.php');
include('include/header.php');

$today = date('Y-m-d');

function layAnhSanPham($conn, $masp) {
    $query = mysqli_query($conn, "SELECT HinhAnh FROM chitietsanphamnam WHERE MaSP = '$masp' LIMIT 1");
    $row = mysqli_fetch_assoc($query);
    return $row['HinhAnh'] ?? 'noimage.jpg';
}

function layKhuyenMai($conn, $masp) {
    $query = mysqli_query($conn, "
        SELECT km.* FROM sanpham_khuyenmai spkm
        JOIN khuyenmai km ON spkm.MaKM = km.MaKM
        WHERE spkm.MaSP = '$masp' LIMIT 1");
    return mysqli_fetch_assoc($query);
}

function layGiaKhuyenMai($gia, $km, $today) {
    if (!$km) return null;
    if ($km['NgayBatDau'] <= $today && $today <= $km['NgayKetThuc']) {
        if (!is_null($km['KM_PT']) && $km['KM_PT'] > 0) {
            $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
        } elseif (!is_null($km['TienKM']) && $km['TienKM'] > 0) {
            $giaKM = $gia - $km['TienKM'];
        }
        return max($giaKM, 0);
    }
    return null;
}

function hienThiSanPham($result, $conn, $today) {
    while ($row = mysqli_fetch_assoc($result)) {
        $masp = $row['MaSP'];
        $gia = $row['Gia'];
        $img = layAnhSanPham($conn, $masp);
        $km = layKhuyenMai($conn, $masp);
        $giaKM = layGiaKhuyenMai($gia, $km, $today);
        echo '<div class="product">';
        echo "<a href='sanpham/xemsanphamnam.php?id=$masp'><img src='../webroot/images/sanpham/" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($row['TenSanPham']) . "'></a>";
        echo "<h4>" . htmlspecialchars($row['TenSanPham']) . "</h4>";
        if ($giaKM !== null) {
            echo "<span class='gia-goc'>" . number_format($gia) . "đ</span> ";
            echo "<span class='gia-km'>" . number_format($giaKM) . "đ</span>";
        } else {
            echo "<span>" . number_format($gia) . "đ</span>";
        }
        echo "<br><a href='xemsanphamnam.php?id=$masp'>Xem chi tiết</a>";
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <style>
    /* ===== Slider ảnh ===== */
    .slider-container {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 6;
        overflow: hidden;
        margin-bottom: 40px;
    }

    .slider {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .slide {
        display: none;
        width: 100%;
        height: 100%;
        position: absolute;
        animation: fade 1s ease-in-out;
    }

    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slide.active {
        display: block;
    }

    @keyframes fade {
        from {opacity: 0.3}
        to {opacity: 1}
    }

    .dots {
        position: absolute;
        bottom: 15px;
        width: 100%;
        text-align: center;
    }

    .dot {
        display: inline-block;
        width: 14px;
        height: 14px;
        margin: 0 5px;
        background: #ccc;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s;
    }

    .dot.active,
    .dot:hover {
        background: #d60000;
    }
    

    /* ===== Layout ===== */
    .container {
        display: flex;
        justify-content: center;
    }

    .main-content {
        width: 100%;
        text-align: center;
        padding: 0;
        margin: 0;
    }

    h2 {
        text-align: center;
        margin: 50px 0 30px;
        color: #d60000;
        font-size: 42px;
        font-weight: bold;
    }

    /* ===== Product slider ===== */
    .product-slider {
        position: relative;
        margin-bottom: 50px;
        padding-bottom: 100px;
    }

    .product-list-horizontal {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        gap: 20px;
        padding: 10px 0;
    }

    .product-list-horizontal::-webkit-scrollbar {
        display: none;
    }

    .prev-btn, .next-btn {
        position: absolute;
        top: 45%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.8);
        color: #d60000;
        border: none;
        font-size: 30px;
        padding: 8px 12px;
        cursor: pointer;
        z-index: 10;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        transition: background 0.3s;
    }

    .prev-btn:hover, .next-btn:hover {
        background-color: #fff;
    }

    .prev-btn { left: 0; }
    .next-btn { right: 0; }

    /* ===== Product card ===== */
    .product {
        flex: 0 0 calc(25% - 24px);
        width: calc(25% - 24px);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .product:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .product img {
        width: 100%;
        height: 420px;
        object-fit: cover;
    }

    .product h4 {
        font-size: 17px;
        color: #222;
        margin: 12px;
        height: 48px;
        overflow: hidden;
    }

    .product span {
        font-size: 16px;
        margin: 0 12px 12px;
        display: inline-block;
    }

    .gia-goc {
        text-decoration: line-through;
        color: #999;
        margin-right: 10px;
    }

    .gia-km {
        color: #e60000;
        font-weight: 600;
    }

    .product a {
        display: inline-block;
        margin-bottom: 15px;
        background: #d60000;
        color: #fff;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .product a:hover {
        background: #a80000;
    }


    /* css cho danh mục */
    .featured-mosaic {
        display: flex;
        gap: 20px;
        justify-content: space-between;
    }

    .mosaic-left, .mosaic-right {
        display: flex;
        flex-direction: column;
        gap: 20px;
        flex: 1;
    }

    .top-left {
        display: flex;
        gap: 20px;
        height: 50%; /* nửa chiều cao */
    }

    .top-left a {
        flex: 1;
        display: block;
        height: 100%;
        border-radius: 12px;
        overflow: hidden;
    }

    .bottom-left {
        height: 50%;
    }

    .bottom-left a {
        display: block;
        height: 100%;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
    }

    .mosaic-center {
        flex: 0 0 360px;
        display: flex;
        justify-content: center;
        align-items: stretch;
    }

    .mosaic-center a {
        display: block;
        height: 100%;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
    }

    .mosaic-right {
        flex: 0 0 200px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .mosaic-right a {
        display: block;
        flex: 1; /* chia đôi chiều cao */
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
    }

    .featured-mosaic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .featured-mosaic a:hover img {
        transform: scale(1.05);
    }

    .mosaic-item {
    position: relative;
    display: block;
    height: 100%;
    width: 100%;
    overflow: hidden;
    border-radius: 12px;
}

.mosaic-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.mosaic-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 10px 0;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.mosaic-item:hover .mosaic-caption {
    opacity: 1;
}

    </style>
</head>
<body>
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="h_a"
  agent-id="29f07ae6-576a-4be6-8137-ca7eaf88dd59"
  language-code="vi"
></df-messenger>
<!-- Slider ảnh -->
<div class="slider-container">
    <div class="slider">
        <div class="slide active"><img src="../webroot/images/anhbia/ảnh 1.jpg" alt="Ảnh 1"></div>
        <div class="slide"><img src="../webroot/images/anhbia/ảnh 2.jpg" alt="Ảnh 2"></div>
        <div class="slide"><img src="../webroot/images/anhbia/ảnh 3.jpg" alt="Ảnh 3"></div>
        <div class="slide"><img src="../webroot/images/anhbia/ảnh 4.jpg" alt="Ảnh 4"></div>
        <div class="slide"><img src="../webroot/images/anhbia/ảnh 5.jpg" alt="Ảnh 5"></div>
    </div>
    <div class="dots">
        <span class="dot active" onclick="showSlide(0)"></span>
        <span class="dot" onclick="showSlide(1)"></span>
        <span class="dot" onclick="showSlide(2)"></span>
        <span class="dot" onclick="showSlide(3)"></span>
        <span class="dot" onclick="showSlide(4)"></span>
    </div>
</div>

<!-- Hiển thị sản phẩm -->
<div class="container">
    <div class="main-content">

        <h2>Quần áo nữ</h2>
        <div class="product-slider">
            <button class="prev-btn" onclick="scrollSlider('nu', -1)">❮</button>
            <div class="product-list-horizontal" id="slider-nu">
                <?php
                $spnu = mysqli_query($conn, "SELECT * FROM sanphamnam WHERE GioiTinh = 'nu'");
                hienThiSanPham($spnu, $conn, $today);
                ?>
            </div>
            <button class="next-btn" onclick="scrollSlider('nu', 1)">❯</button>
        </div>

        <h2>Quần áo nam</h2>
        <div class="product-slider">
            <button class="prev-btn" onclick="scrollSlider('nam', -1)">❮</button>
            <div class="product-list-horizontal" id="slider-nam">
                <?php
                $spnam = mysqli_query($conn, "SELECT * FROM sanphamnam WHERE GioiTinh = 'nam'");
                hienThiSanPham($spnam, $conn, $today);
                ?>
            </div>
            <button class="next-btn" onclick="scrollSlider('nam', 1)">❯</button>
        </div>

    </div>
</div>

<!-- Danh mục nổi bật -->
<h2>Danh mục nổi bật</h2>
<div class="featured-mosaic" style="max-width: 1200px; margin: 40px auto;">
    <div class="mosaic-left">
        <div class="top-left">
            <a href="sanpham/thoitrangnu.php?machitiet=18" class="mosaic-item">
                <img src="../webroot/images/danhmuc/ảnh đầm công sở.png" alt="Đầm công sở">
                <div class="mosaic-caption">Đầm công sở</div>
            </a>
            <a href="sanpham/thoitrangnu.php?machitiet=28" class="mosaic-item">
                <img src="../webroot/images/danhmuc/ảnh chân váy.png" alt="Chân váy">
                <div class="mosaic-caption">Chân váy</div>
            </a>
        </div>
        <div class="bottom-left">
            <a href="sanpham/thoitrangnu.php?machitiet=13" class="mosaic-item">
                <img src="../webroot/images/danhmuc/ảnh áo vest.png" alt="Áo vest">
                <div class="mosaic-caption">Áo vest</div>
            </a>
        </div>
    </div>
    <div class="mosaic-center">
        <a href="sanpham/thoitrangnu.php?machitiet=12" class="mosaic-item">
            <img src="../webroot/images/danhmuc/ảnh áo sơ mi nữ.png" alt="Áo sơ mi nữ">
            <div class="mosaic-caption">Áo sơ mi nữ</div>
        </a>
    </div>
    <div class="mosaic-right">
        <a href="sanpham/thoitrangnu.php?machitiet=19" class="mosaic-item">
            <img src="../webroot/images/danhmuc/ảnh đầm dạo phố.png" alt="Đầm dạo phố">
            <div class="mosaic-caption">Đầm dạo phố</div>
        </a>
        <a href="sanpham/thoitrangnu.php?machitiet=20" class="mosaic-item">
            <img src="../webroot/images/danhmuc/đầm dạ hội.png" alt="Đầm dạ hội">
            <div class="mosaic-caption">Đầm dạ hội</div>
        </a>
    </div>
</div>
<!-- JS slider ảnh -->
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    slides.forEach((s, i) => {
        s.classList.toggle('active', i === index);
        dots[i].classList.toggle('active', i === index);
    });
    currentSlide = index;
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

setInterval(nextSlide, 5000);
</script>

<!-- JS trượt sản phẩm -->
<script>
function scrollSlider(gioiTinh, direction) {
    const container = document.getElementById(`slider-${gioiTinh}`);
    const scrollAmount = 300;
    container.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}
</script>

</body>
</html>

<?php include('include/footer.php'); ?>
