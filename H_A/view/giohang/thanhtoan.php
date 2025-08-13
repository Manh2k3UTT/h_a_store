<?php
session_start();
if (!isset($_SESSION['giohang']) || count($_SESSION['giohang']) == 0) {
    echo "Giỏ hàng của bạn đang trống.";
    exit;
}
include('../include/header.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #fff;
    color: #222;
    padding: 20px;
    margin: 0 auto;
  }

  h2 {
    color: #d32f2f;
    margin-bottom: 20px;
  }

  label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
  }

  input[type="text"],
  textarea,
  select {
    
    width: 40%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  textarea:focus,
  select:focus {
    
    border-color: #d32f2f;
    outline: none;
  }

  textarea {
    resize: vertical;
    max-width: 600px;
    min-height: 80px;
  }

  button[type="submit"] {
    background-color: #d32f2f;
    color: #fff;
    border: none;
    padding: 12px 24px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button[type="submit"]:hover {
    background-color: #9b2222;
  }
</style>
</head>
<body>
<h2>Thông tin người nhận và phương thức thanh toán</h2>
<form method="POST" id="formThanhToan" action="xulythanhtoan.php">
    <label>Tên người nhận:</label><br>
    <input type="text" name="ten" required><br><br>

    <label>Số điện thoại:</label><br>
    <input type="text" name="sdt" required pattern="[0-9]{9,15}"><br><br>

    <label>Địa chỉ giao hàng:</label><br>
    <textarea name="diachi" required></textarea><br><br>

    <label>Phương thức thanh toán:</label>
    <select name="pttt" id="pttt" required>
        <option value="cod">Thanh toán khi nhận hàng (COD)</option>
        <option value="online">Thanh toán online</option>
    </select><br>

    <input type="hidden" name="redirect_to" value="">

    <button type="submit">Tiếp tục</button>
</form>

<script>
document.getElementById("formThanhToan").addEventListener("submit", function(e) {
    const pttt = document.getElementById("pttt").value;
    if (pttt === "online") {
        this.action = "thanhtoan_vnpay.php";
    } else {
        this.action = "xulythanhtoan.php";
    }
});
</script>

</body>
</html>
