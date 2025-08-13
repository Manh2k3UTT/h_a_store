<?php
session_start();

// Hủy toàn bộ session
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập hoặc trang chủ
header('Location: login.php'); // hoặc '../index.php' nếu bạn muốn về trang chủ
exit();
?>
