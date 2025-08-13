<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($conn)) {
    $conn = new mysqli("localhost", "root", "", "h_a");
    if ($conn->connect_error) {
        die("Kết nối CSDL lỗi: " . $conn->connect_error);
    }
}
?>

<style>
.header-wrapper {
    font-family: Arial, sans-serif;
    border-bottom: 1px solid #ccc;
}

/* PHẦN TRÊN */
.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    background-color: white;
    flex-wrap: wrap;
    gap: 20px;
}

/* Logo */
.logo {
    font-size: 42px;
    font-weight: bold;
    text-decoration: none;
    color: #d32f2f;
    white-space: nowrap;
}

/* Search + filter */
.search-filter {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    max-width: 100%;
}

.search-row,
.filter-row {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search-filter input[type="text"] {
    padding: 10px 20px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 999px;
    width: 400px;
}

.search-filter select {
    padding: 10px 14px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 999px;
    min-width: 140px;
}

.search-filter button {
    padding: 10px 20px;
    background-color: #d32f2f;
    color: white;
    border: none;
    border-radius: 999px;
    font-weight: bold;
    cursor: pointer;
}

.search-filter button:hover {
    opacity: 0.9;
}

/* Tài khoản */
.account {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    text-decoration: none;
    white-space: nowrap;
}

.account:hover {
    text-decoration: underline;
}

/* MENU */
.menu-bar {
    background-color: white;
    padding: 12px 40px;
    display: flex;
    gap: 40px;
    justify-content: center;
    flex-wrap: wrap;
}

.menu-bar a {
    color: #d32f2f;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
}

.menu-bar a:hover {
    text-decoration: underline;
}
</style>

<div class="header-wrapper">

    <!-- PHẦN TRÊN -->
    <div class="header-top">
        <!-- Logo -->
        <a href="/H_A/view/index.php" class="logo">H&amp;A</a>

        <!-- Search + Filter -->
        <form method="GET" class="search-filter" action="/H_A/view/include/timkiem.php">
            <div class="search-row">
                <input type="text" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">Tìm</button>
            </div>
            <div class="filter-row">
                <select name="gioitinh">
                    <option value="">Giới tính</option>
                    <option value="nam" <?= ($_GET['gioitinh'] ?? '') == 'nam' ? 'selected' : '' ?>>Nam</option>
                    <option value="nu" <?= ($_GET['gioitinh'] ?? '') == 'nu' ? 'selected' : '' ?>>Nữ</option>
                </select>

                <select name="mau">
                    <option value="">Màu sắc</option>
                    <?php 
                    $mau_query = mysqli_query($conn, "SELECT MaMau FROM mau");
                    while ($row = mysqli_fetch_assoc($mau_query)) {
                        $selected = ($_GET['mau'] ?? '') == $row['MaMau'] ? 'selected' : '';
                        echo "<option value='{$row['MaMau']}' $selected>{$row['MaMau']}</option>";
                    }
                    ?>
                </select>

                <select name="size">
                    <option value="">Kích cỡ</option>
                    <?php 
                    $size_query = mysqli_query($conn, "SELECT MaSize FROM kichco");
                    while ($row = mysqli_fetch_assoc($size_query)) {
                        $selected = ($_GET['size'] ?? '') == $row['MaSize'] ? 'selected' : '';
                        echo "<option value='{$row['MaSize']}' $selected>{$row['MaSize']}</option>";
                    }
                    ?>
                </select>

                <select name="gia">
                    <option value="">Khoảng giá</option>
                    <option value="1" <?= ($_GET['gia'] ?? '') == '1' ? 'selected' : '' ?>>Dưới 200.000đ</option>
                    <option value="2" <?= ($_GET['gia'] ?? '') == '2' ? 'selected' : '' ?>>200.000đ - 500.000đ</option>
                    <option value="3" <?= ($_GET['gia'] ?? '') == '3' ? 'selected' : '' ?>>Trên 500.000đ</option>
                </select>
            </div>
        </form>

        <!-- Tài khoản -->
        <a href="<?= isset($_SESSION['MaKH']) ? '/H_A/view/account.php' : '/H_A/view/login.php' ?>" class="account">
            <?= isset($_SESSION['TenKH']) ? htmlspecialchars($_SESSION['TenKH']) : "Đăng nhập / Đăng ký" ?>
        </a>
    </div>

    <!-- PHẦN MENU -->
    <div class="menu-bar">
        <a href="/H_A/view/index.php">Trang chủ</a>
        <a href="/H_A/view/sanpham/thoitrangnu.php">Quần áo nữ</a>
        <a href="/H_A/view/sanpham/thoitrangnam.php">Quần áo nam</a>
        <a href="/H_A/view/gioithieu.php">Giới thiệu</a>
        <a href="/H_A/view/lienhe.php">Liên hệ</a>
    </div>

</div>
