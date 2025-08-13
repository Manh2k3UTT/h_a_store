
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    nav {
      background-color: #333;
      display: flex;
      justify-content: space-between;
      padding: 10px 20px;
      align-items: center;
    }

    .logo a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .menu {
      display: flex;
      gap: 20px;
    }

    .dropdown {
      position: relative;
    }

    /* Ẩn checkbox */
    .dropdown input[type="checkbox"] {
      display: none;
    }

    .dropdown label {
      color: white;
      cursor: pointer;
      font-size: 16px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: #444;
      min-width: 160px;
      z-index: 1;
    }

    .dropdown-content a {
      color: white;
      padding: 12px 16px;
      display: block;
      text-decoration: none;
    }

    .dropdown-content a:hover {
      background-color: #666;
    }

    /* Khi checkbox được chọn thì hiển thị dropdown */
    .dropdown input[type="checkbox"]:checked ~ .dropdown-content {
      display: block;
    }

    .user-dropdown {
      position: relative;
    }

    .user-dropdown input[type="checkbox"] {
      display: none;
    }

    .user-dropdown label {
      color: white;
      cursor: pointer;
    }

    .user-dropdown .dropdown-content {
      right: 0;
      left: auto;
    }
    
    .user-toggle:checked ~ .dropdown-content {
      display: block;
    }

    .dropdown-content a:hover {
      background-color: #666;
    }
  
  </style>
</head>
<body>

<nav>
  <div class="logo">
    <a href="index.php">Trang quản trị</a>
  </div>

  <div class="menu">
    <div>
      <a href="../index/index.php" style="color: white; text-decoration: none;">Doanh thu</a>
    </div>

  
    <!-- Dropdown: Loại sản phẩm -->
    <div class="dropdown">
      <input type="checkbox" id="loai-toggle" />
      <label for="loai-toggle">Đơn hàng</label>
      <div class="dropdown-content">
        <a href="../donhang/donhang.php">Đơn hàng</a>
        <a href="../donhang/list_giaohang.php">Giao hàng</a>
      </div>
    </div>

    <!-- Dropdown: Sản phẩm -->
    <div class="dropdown">
      <input type="checkbox" id="sp-toggle" />
      <label for="sp-toggle">Sản phẩm</label>
      <div class="dropdown-content">
        <a href="../sanpham/sanphamnam.php">Sản phẩm</a>
        <a href="../loai/loainam.php">Loại</a>
        <a href="../loai/chitietloainam.php">Chi tiết loại</a>
        <a href="../mau/mau.php">Màu</a>
      </div>
    </div>

    <!-- Khuyến mãi -->
    <div>
      <a href="../khuyenmai/khuyenmai.php" style="color: white; text-decoration: none;">Khuyến mại</a>
    </div>

    <!-- Kho -->
    <div>
      <a href="../kho/kho.php" style="color: white; text-decoration: none;">Kho</a>
    </div>
    
    <!-- Nhân viên -->
     
    <div>
      <a href="../nhanvien/nhanvien.php" style="color: white; text-decoration: none;">Nhân viên</a>
    </div>

    <div class="dropdown">
      <input type="checkbox" id="kh-toggle" />
      <label for="kh-toggle">Khách hàng</label>
      <div class="dropdown-content">
        <a href="../nhanvien/khachhang.php">Tài khoản</a>
        <a href="../nhanvien/binhluan.php">Bình luận</a>
        <a href="../nhanvien/lienhe.php">Liên hệ</a>
      </div>
    </div>


  </div>
  <!-- Tài khoản bên phải -->
  <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($tennv) ?></span>
            <i class="fas fa-user-circle fa-lg"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow">
            <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Đăng xuất</a>
        </div>
    </li>

  

</nav>

</body>
</html>
