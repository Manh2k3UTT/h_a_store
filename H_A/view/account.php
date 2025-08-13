<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['MaKH'])) {
    header('Location: login.php');
    exit;
}

include 'include/header.php';

$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$MaKH = $_SESSION['MaKH'];
$errors = [];
$success = '';

// Cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $TenKH = trim($_POST['TenKH']);
    $Email = trim($_POST['Email']);
    $SDT = trim($_POST['SDT']);
    $DiaChi = trim($_POST['DiaChi']);
    $MatKhau = trim($_POST['MatKhau']);

    if ($TenKH === '') $errors['TenKH'] = 'Tên khách hàng không được để trống.';
    if ($Email === '' || !filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors['Email'] = 'Email không hợp lệ.';
    if ($SDT === '') $errors['SDT'] = 'Số điện thoại không được để trống.';
    if ($DiaChi === '') $errors['DiaChi'] = 'Địa chỉ không được để trống.';
    if ($MatKhau === '') $errors['MatKhau'] = 'Mật khẩu không được để trống.';

    if (empty($errors)) {
        $sql = "UPDATE khachhang SET TenKH = ?, Email = ?, SDT = ?, DiaChi = ?, MatKhau = ? WHERE MaKH = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $TenKH, $Email, $SDT, $DiaChi, $MatKhau, $MaKH);
        if ($stmt->execute()) {
            $success = 'Cập nhật thông tin thành công.';
            $_SESSION['TenKH'] = $TenKH;
        } else {
            $errors['db'] = 'Có lỗi khi cập nhật dữ liệu. Vui lòng thử lại.';
        }
        $stmt->close();
    }
}

// Lấy lại thông tin
$sql = "SELECT * FROM khachhang WHERE MaKH = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $MaKH);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Lấy lịch sử đơn hàng
$sql_history = "SELECT * FROM donhang WHERE MaKH = ? ORDER BY NgayDat DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $MaKH);
$stmt_history->execute();
$history_result = $stmt_history->get_result();

// Giỏ hàng
if (!isset($_SESSION['giohang'])) $_SESSION['giohang'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_key'])) {
        $key = $_POST['delete_key'];
        unset($_SESSION['giohang'][$key]);

        // Xóa trong bảng giohang_tam nếu đã đăng nhập
        if (isset($_SESSION['MaKH'])) {
            $makh = $_SESSION['MaKH'];
            $parts = explode('_', $key); // key có dạng: MaSP_MaMau_MaSize
            if (count($parts) === 3) {
                $masp = $parts[0];
                $mamau = $parts[1];
                $masize = $parts[2];
                $conn->query("DELETE FROM giohang_tam WHERE MaKH='$makh' AND MaSP='$masp' AND MaMau='$mamau' AND MaSize='$masize'");
            }
        }
    }
    } elseif (isset($_POST['update_cart'])) {
        foreach ($_POST['soluong'] as $key => $soluong) {
            $soluong = max(1, intval($soluong));
            $_SESSION['giohang'][$key]['soluong'] = $soluong;
        }
    }


$giohang = $_SESSION['giohang'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang tài khoản</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h2 { text-align: center; }
        .tabs { text-align: center; margin-bottom: 20px; }
        .tabs button {
            padding: 10px 15px;
            font-weight: bold;
            cursor: pointer;
            border: 1px solid #007bff;
            background-color: white;
            color: #007bff;
            border-radius: 5px;
            transition: 0.3s;
            margin: 0 5px;
            font-size: 16px;
        }
        .tabs button.active,
        .tabs button:hover {
            background-color: #007bff;
            color: white;
        }
        .form-wrapper { display: flex; flex-direction: column; align-items: center; }
        form { text-align: center; width: 100%; max-width: 450px; }
        label { display: block; margin-top: 15px; font-weight: bold; text-align: left; }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
        }
        .error { color: red; font-size: 0.9em; text-align: left; }
        .success { color: green; margin-top: 10px; font-weight: bold; text-align: center; }

        .cart-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f44336;
            color: #fff;
            font-size: 16px;
        }

        td img {
            max-width: 70px;
            height: auto;
            border-radius: 6px;
        }

        tfoot td {
            font-weight: bold;
            background-color: #fafafa;
            font-size: 1.1em;
            border-top: 2px solid #f44336;
        }

        td:nth-child(2), th:nth-child(2) { min-width: 200px; text-align: left; }
        td:nth-child(1), th:nth-child(1) { min-width: 100px; }
        td:nth-child(3), td:nth-child(4),
        th:nth-child(3), th:nth-child(4) { min-width: 80px; }

        .input-soluong {
            width: 70px;
            padding: 6px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button[name="update_cart"] {
            background-color: #28a745;
            color: #fff;
        }

        button[name="delete_key"] {
            background-color: #dc3545;
            color: white;
        }

        .button-link {
            display: inline-block;
            padding: 10px 18px;
            background-color: #d32f2f;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 6px;
        }

        .button-link:hover {
            background-color: #9b2222;
        }
    </style>
    <script>
        function showTab(tabName) {
            const tabs = ['info-tab', 'orders-tab', 'history-tab'];
            tabs.forEach(tab => {
                document.getElementById(tab).style.display = 'none';
                const btn = document.getElementById('btn-' + tab.split('-')[0]);
                if (btn) btn.classList.remove('active');
            });

            document.getElementById(tabName).style.display = 'block';
            const activeBtn = document.getElementById('btn-' + tabName.split('-')[0]);
            if (activeBtn) activeBtn.classList.add('active');
        }

        window.onload = function () {
            showTab('info-tab');
        };
    </script>
</head>
<body>
<h2>Trang tài khoản</h2>

<div class="tabs">
    <button id="btn-info" onclick="showTab('info-tab')">Thông tin</button>
    <button id="btn-orders" onclick="showTab('orders-tab')">Giỏ hàng</button>
    <button id="btn-history" onclick="showTab('history-tab')">Lịch sử đơn</button>
    <button onclick="document.getElementById('logout-form').submit();" style="background-color: #dc3545; color: white;">Đăng xuất</button>
</div>

<form id="logout-form" method="post" action="logout.php" style="display: none;"></form>

<!-- Tab Thông tin -->
<div id="info-tab">
    <div class="form-wrapper">
        <form method="post">
            <label for="TenKH">Tên khách hàng</label>
            <input type="text" id="TenKH" name="TenKH" value="<?= htmlspecialchars($user['TenKH']) ?>">
            <?php if (!empty($errors['TenKH'])): ?><div class="error"><?= $errors['TenKH'] ?></div><?php endif; ?>

            <label for="Email">Email</label>
            <input type="email" id="Email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>">
            <?php if (!empty($errors['Email'])): ?><div class="error"><?= $errors['Email'] ?></div><?php endif; ?>

            <label for="SDT">Số điện thoại</label>
            <input type="text" id="SDT" name="SDT" value="<?= htmlspecialchars($user['SDT']) ?>">
            <?php if (!empty($errors['SDT'])): ?><div class="error"><?= $errors['SDT'] ?></div><?php endif; ?>

            <label for="DiaChi">Địa chỉ</label>
            <input type="text" id="DiaChi" name="DiaChi" value="<?= htmlspecialchars($user['DiaChi']) ?>">
            <?php if (!empty($errors['DiaChi'])): ?><div class="error"><?= $errors['DiaChi'] ?></div><?php endif; ?>

            <label for="MatKhau">Mật khẩu</label>
            <input type="password" id="MatKhau" name="MatKhau" value="<?= htmlspecialchars($user['MatKhau']) ?>">
            <?php if (!empty($errors['MatKhau'])): ?><div class="error"><?= $errors['MatKhau'] ?></div><?php endif; ?>

            <?php if (!empty($errors['db'])): ?><div class="error"><?= $errors['db'] ?></div><?php endif; ?>
            <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

            <button type="submit" name="update_info" style="margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white;">Cập nhật thông tin</button>
        </form>
    </div>
</div>

<!-- Tab Giỏ hàng -->
<div id="orders-tab" style="display: none;">
    <h2>Giỏ hàng của bạn</h2>
    <?php if (empty($giohang)): ?>
        <p>Giỏ hàng trống.</p>
    <?php else: ?>
    <div class="cart-wrapper">
        <form method="post" id="cart-form">
            <table>
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tong = 0;
                    foreach ($giohang as $key => $sp):
                        $thanhtien = $sp['gia'] * $sp['soluong'];
                        $tong += $thanhtien;
                    ?>
                    <tr data-key="<?= $key ?>">
                        <td><img src="../webroot/images/sanpham/<?= htmlspecialchars($sp['hinh']) ?>" width="80"></td>
                        <td><?= htmlspecialchars($sp['ten']) ?></td>
                        <td><?= htmlspecialchars($sp['mau']) ?></td>
                        <td><?= htmlspecialchars($sp['size']) ?></td>
                        <td><?= number_format($sp['gia'], 0, ',', '.') ?> đ</td>
                        <td>
                            <input type="number" name="soluong[<?= $key ?>]" value="<?= $sp['soluong'] ?>" min="1" class="input-soluong" data-gia="<?= $sp['gia'] ?>" data-key="<?= $key ?>">
                        </td>
                        <td class="thanhtien"><?= number_format($thanhtien, 0, ',', '.') ?> đ</td>
                        <td><button type="submit" name="delete_key" value="<?= $key ?>" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="6" style="text-align: right;"><strong>Tổng cộng:</strong></td><td colspan="2" id="tongcong"><?= number_format($tong, 0, ',', '.') ?> đ</td></tr>
                </tfoot>
            </table>
            <div style="margin-top: 10px;">
                <button type="submit" name="update_cart">Cập nhật giỏ hàng</button>
                <a href="giohang/thanhtoan.php" class="button-link" style="margin-left: 15px;">Thanh toán</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<!-- Tab Lịch sử đơn hàng -->
<div id="history-tab" style="display: none;">
    <h2>Lịch sử đơn hàng</h2>
    <?php if ($history_result->num_rows === 0): ?>
        <p>Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <div class="cart-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dh = $history_result->fetch_assoc()): 
                        $madon = $dh['MaDonHang'];
                        $sql_ct = "SELECT ct.*, s.TenSanPham, m.MaMau, sz.MaSize 
                                   FROM chitietdonhang ct 
                                   JOIN sanphamnam s ON ct.MaSP = s.MaSP 
                                   JOIN mau m ON ct.MaMau = m.MaMau 
                                   JOIN kichco sz ON ct.MaSize = sz.MaSize 
                                   WHERE ct.MaDonHang = $madon";
                        $ct_result = $conn->query($sql_ct);
                    ?>
                        <tr>
                            <td><?= $madon ?></td>
                            <td><?= $dh['NgayDat'] ?></td>
                            <td><?= number_format($dh['TongTien'], 0, ',', '.') ?> đ</td>
                            <td><?= $dh['TrangThai'] ?></td>
                            <td>
                                <button onclick="toggleDetail('ct-<?= $madon ?>', this)" class="button-link">Xem chi tiết</button>
                            </td>
                        </tr>
                        <tr id="ct-<?= $madon ?>" style="display: none; background-color: #fcfcfc;">
    <td colspan="5" style="padding: 10px 20px;">
        <table style="width: 100%; font-size: 14px; color: #555;">
            <thead style="font-size: 13px; font-weight: normal; color: #666;">
                <tr style="border-bottom: 1px solid #ddd;">
                    <th align="left">Sản phẩm</th>
                    <th>Màu</th>
                    <th>Size</th>
                    <th>SL</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ct = $ct_result->fetch_assoc()):
                    $thanhtien = $ct['SoLuong'] * $ct['DonGia'];
                ?>
                <tr style="border-bottom: 1px dashed #e0e0e0;">
                    <td><?= htmlspecialchars($ct['TenSanPham']) ?></td>
                    <td><?= htmlspecialchars($ct['MaMau']) ?></td>
                    <td><?= htmlspecialchars($ct['MaSize']) ?></td>
                    <td><?= $ct['SoLuong'] ?></td>
                    <td><?= number_format($ct['DonGia'], 0, ',', '.') ?> đ</td>
                    <td><?= number_format($thanhtien, 0, ',', '.') ?> đ</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </td>
</tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.input-soluong').forEach(input => {
    input.addEventListener('input', function () {
        let soluong = parseInt(this.value);
        if (isNaN(soluong) || soluong < 1) soluong = 1;

        const key = this.getAttribute('data-key');
        const inputElem = this;

        fetch('giohang/update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'key=' + encodeURIComponent(key) + '&soluong=' + soluong
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = inputElem.closest('tr');
                row.querySelector('.thanhtien').innerText = data.thanhtien;
                document.getElementById('tongcong').innerText = data.tong;
            }
        });
    });
});
</script>
<script>
function toggleDetail(id, btn) {
    const row = document.getElementById(id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
        btn.textContent = 'Ẩn chi tiết';
    } else {
        row.style.display = 'none';
        btn.textContent = 'Xem chi tiết';
    }
}
</script>
</body>
</html>
