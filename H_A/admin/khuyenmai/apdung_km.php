<?php
include("../../model/database.php");

$action = $_GET['action'] ?? '';
$makm = $_GET['makm'] ?? '';
$masp = $_GET['masp'] ?? '';

if (!$action || !$makm || !$masp) {
    die("Thiếu tham số.");
}

if ($action == 'apdung') {
    // Trước khi áp dụng, kiểm tra sản phẩm đã có khuyến mãi chưa
    $check = $conn->prepare("SELECT * FROM apdung_km WHERE MaSP = ?");
    $check->bind_param("s", $masp);
    $check->execute();
    $exist = $check->get_result()->fetch_assoc();

    if ($exist) {
        // Nếu đã có và không phải khuyến mãi hiện tại, không áp dụng
        header("Location: apdung.php?id=$makm");
        exit;
    }

    // Áp dụng khuyến mãi
    $stmt = $conn->prepare("INSERT INTO apdung_km (MaSP, MaKM) VALUES (?, ?)");
    $stmt->bind_param("ss", $masp, $makm);
    $stmt->execute();
    header("Location: apdung.php?id=$makm");
    exit;

} elseif ($action == 'bo') {
    // Bỏ áp dụng
    $stmt = $conn->prepare("DELETE FROM apdung_km WHERE MaSP = ? AND MaKM = ?");
    $stmt->bind_param("ss", $masp, $makm);
    $stmt->execute();
    header("Location: apdung.php?id=$makm");
    exit;
}
?>
