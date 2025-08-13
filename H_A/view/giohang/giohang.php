<?php
session_start();
require_once('../../model/database.php');
include('../include/header.php');

$today = date('Y-m-d');

// Xử lý xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_key'])) {
        $delKey = $_POST['delete_key'];
        unset($_SESSION['giohang'][$delKey]);

        // Nếu đã đăng nhập thì xóa trong bảng giohang_tam
        if (isset($_SESSION['MaKH'])) {
            $makh = $_SESSION['MaKH'];
            // Tách key thành MaSP, MaMau, MaSize
            $parts = explode('_', $delKey);
            if (count($parts) === 3) {
                $masp = $parts[0];
                $mamau = $parts[1];
                $masize = $parts[2];
                $conn->query("DELETE FROM giohang_tam WHERE MaKH='$makh' AND MaSP='$masp' AND MaMau='$mamau' AND MaSize='$masize'");
            }
        }
    }
}

// Lấy giỏ hàng
$giohang = $_SESSION['giohang'] ?? [];

// Tạo mảng chứa giá khuyến mãi
$gia_km_arr = [];

foreach ($giohang as $key => $item) {
    $masp = $item['masp'];
    $gia = $item['gia'];
    $giaKM = $gia;

    $km_query = mysqli_query($conn, "
        SELECT km.*
        FROM sanpham_khuyenmai spkm
        JOIN khuyenmai km ON spkm.MaKM = km.MaKM
        WHERE spkm.MaSP = '$masp'
          AND km.NgayBatDau <= '$today'
          AND km.NgayKetThuc >= '$today'
        LIMIT 1
    ");

    if ($km_query && mysqli_num_rows($km_query) > 0) {
        $km = mysqli_fetch_assoc($km_query);
        if (!is_null($km['KM_PT']) && $km['KM_PT'] > 0) {
            $giaKM = $gia - ($gia * $km['KM_PT'] / 100);
        } elseif (!is_null($km['TienKM']) && $km['TienKM'] > 0) {
            $giaKM = $gia - $km['TienKM'];
        }
        if ($giaKM < 0) $giaKM = 0;
    }

    $gia_km_arr[$key] = $giaKM;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn</title>
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body>
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($giohang)): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
        <p><a href="../sanpham/thoitrangnam.php">Tiếp tục mua sắm</a></p>
    <?php else: ?>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Màu</th>
                    <th>Kích cỡ</th>
                    <th>Giá (đ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (đ)</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tong = 0;
                foreach ($giohang as $key => $item):
                    $thanhtien = $gia_km_arr[$key] * $item['soluong'];
                    $tong += $thanhtien;
                ?>
                <tr data-key="<?= $key ?>" data-gia="<?= $gia_km_arr[$key] ?>">
                    <td><img src="../../webroot/images/sanpham/<?= htmlspecialchars($item['hinh']) ?>" class="img-cart" alt="Ảnh sản phẩm"></td>
                    <td><?= htmlspecialchars($item['ten']) ?></td>
                    <td><?= htmlspecialchars($item['mau']) ?></td>
                    <td><?= htmlspecialchars($item['size']) ?></td>
                    <td><?= number_format($gia_km_arr[$key], 0, ',', '.') ?></td>
                    <td>
                        <input type="number" value="<?= $item['soluong'] ?>" min="1" class="soluong" style="width: 60px;" data-key="<?= $key ?>">
                    </td>
                    <td class="thanhtien"><?= number_format($thanhtien, 0, ',', '.') ?></td>
                    <td>
                        <form method="POST" action="">
                            <button type="submit" name="delete_key" value="<?= $key ?>" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="total">Tổng cộng:</td>
                    <td colspan="2" id="tongtien" class="total"><?= number_format($tong, 0, ',', '.') ?> đ</td>
                </tr>
            </tfoot>
        </table>
        <div style="margin-top: 15px;">
            <a href="../sanpham/thoitrangnam.php" class="button-link">Tiếp tục mua sắm</a>
            <a href="thanhtoan.php" class="button-link" style="margin-left: 15px;">Thanh toán</a>
        </div>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.soluong').forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const gia = parseInt(row.getAttribute('data-gia'));
                const key = this.getAttribute('data-key');
                let soluong = parseInt(this.value);

                if (isNaN(soluong) || soluong < 1) soluong = 1;

                const thanhtien = gia * soluong;
                row.querySelector('.thanhtien').textContent = thanhtien.toLocaleString('vi-VN');

                let tong = 0;
                document.querySelectorAll('#cart-table tbody tr').forEach(r => {
                    const g = parseInt(r.getAttribute('data-gia'));
                    const sl = parseInt(r.querySelector('.soluong').value) || 0;
                    tong += g * sl;
                });
                document.getElementById('tongtien').textContent = tong.toLocaleString('vi-VN') + ' đ';

                fetch('update_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `key=${encodeURIComponent(key)}&soluong=${encodeURIComponent(soluong)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        row.querySelector('.thanhtien').textContent = data.thanhtien;
                        document.getElementById('tongtien').textContent = data.tong;
                    }
                });
            });
        });
    </script>
</body>
</html>
