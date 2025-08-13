<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy tổng tiền từ giỏ hàng (hoặc POST)
if (isset($_SESSION['giohang']) && !empty($_SESSION['giohang'])) {
    $tongtien = 0;
    $today = date('Y-m-d');

    include('../../model/database.php');

    // Lưu thông tin người nhận từ form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_SESSION['ten'] = $_POST['ten'] ?? '';
        $_SESSION['sdt'] = $_POST['sdt'] ?? '';
        $_SESSION['diachi'] = $_POST['diachi'] ?? '';
    }
    foreach ($_SESSION['giohang'] as $item) {
        $masp = $item['masp'];
        $gia = $item['gia'];
        $soluong = $item['soluong'];

        $giaKM = $gia;
        $km_query = mysqli_query($conn, "
            SELECT km.* FROM sanpham_khuyenmai spkm
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

        $tongtien += $giaKM * $soluong;
    }
} else {
    die("Không có giỏ hàng để thanh toán.");
}

// ======================== CẤU HÌNH VNPAY ========================
$vnp_TmnCode = "I961T1FZ"; // Website ID in VNPAY System
$vnp_HashSecret = "KYGKBHSOF40QK94BM8CQZD6PMP82SUJR"; // Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// 👉 Đổi lại URL bên dưới theo địa chỉ ngrok của bạn
$vnp_Returnurl = "http://localhost/H_A/view/giohang/thanhtoan_vnpay_return.php";

// ======================== TẠO DỮ LIỆU ============================
$vnp_TxnRef = time() . ""; // Mã đơn hàng duy nhất
$vnp_OrderInfo = "Thanh toán đơn hàng H&A";
$vnp_OrderType = "billpayment";
$vnp_Amount = $tongtien * 100;
$vnp_Locale = "vn";
$vnp_BankCode = "NCB";
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => $startTime,
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $expire
);

if ($vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

// ======================== TẠO CHỮ KÝ ========================
ksort($inputData);
$query = "";
$hashdata = "";
$i = 0;
foreach ($inputData as $key => $value) {
    $query .= urlencode($key) . "=" . urlencode($value) . "&";
    $hashdata .= ($i ? '&' : '') . urlencode($key) . "=" . urlencode($value);
    $i++;
}

$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$vnp_Url .= "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

// ======================== REDIRECT ========================
header('Location: ' . $vnp_Url);
exit;
