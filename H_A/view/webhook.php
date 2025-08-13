<?php
// webhook.php

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['fulfillmentText' => "Không kết nối được cơ sở dữ liệu."]);
    exit;
}

// Nhận JSON từ Dialogflow
$request = json_decode(file_get_contents("php://input"), true);
$intent = $request['queryResult']['intent']['displayName'] ?? '';
$params = $request['queryResult']['parameters'] ?? [];

// ===== 1. Ý định: Kiểm tra sản phẩm có bán không =====
if ($intent === 'KiemTraSanPham') {
    $productName = strtolower(trim($params['product_name'] ?? ''));

    if (empty($productName)) {
        echo json_encode([
            "fulfillmentText" => "Bạn muốn tìm sản phẩm nào ạ?"
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM sanphamnam WHERE LOWER(TenSanPham) LIKE ?");
    $search = "%" . $productName . "%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    $responseText = $count > 0
        ? "Dạ shop có bán '$productName' ạ!"
        : "Rất tiếc, hiện không tìm thấy sản phẩm '$productName'.";

    echo json_encode([
        "fulfillmentText" => $responseText,
        "outputContexts" => [
            [
                "name" => $request['session'] . "/contexts/sanpham-context",
                "lifespanCount" => 3,
                "parameters" => [
                    "product" => $productName
                ]
            ]
        ]
    ]);
    exit;
}

// ===== 2. Ý định: Kiểm tra sản phẩm có/còn màu =====
if ($intent === 'KiemTraMauSanPham') {
    $productName = strtolower(trim($params['product'] ?? ''));
    $colorName = strtolower(trim($params['color'] ?? ''));

    if (empty($productName) || empty($colorName)) {
        echo json_encode([
            "fulfillmentText" => "Bạn muốn kiểm tra sản phẩm và màu nào ạ?"
        ]);
        exit;
    }

    // Lấy MaSP từ tên sản phẩm
    $stmt = $conn->prepare("SELECT MaSP FROM sanphamnam WHERE LOWER(TenSanPham) LIKE ?");
    $searchProduct = "%" . $productName . "%";
    $stmt->bind_param("s", $searchProduct);
    $stmt->execute();
    $stmt->bind_result($masp);
    $stmt->fetch();
    $stmt->close();

    if (empty($masp)) {
        echo json_encode([
            "fulfillmentText" => "Không tìm thấy sản phẩm '$productName'."
        ]);
        exit;
    }

    // Lấy MaMau từ tên màu
    $stmt = $conn->prepare("SELECT MaMau FROM mau WHERE LOWER(MaMau) = ?");
    $stmt->bind_param("s", $colorName);
    $stmt->execute();
    $stmt->bind_result($mamau);
    $stmt->fetch();
    $stmt->close();

    if (empty($mamau)) {
        echo json_encode([
            "fulfillmentText" => "Không tìm thấy màu '$colorName' trong hệ thống."
        ]);
        exit;
    }

    // Kiểm tra số lượng tồn kho
    $stmt = $conn->prepare("SELECT SoLuong FROM chitietsanphamnam WHERE MaSP = ? AND MaMau = ?");
    $stmt->bind_param("ss", $masp, $mamau);
    $stmt->execute();
    $stmt->bind_result($soluong);
    $stmt->fetch();
    $stmt->close();

    $responseText = ($soluong > 0)
        ? "Dạ sản phẩm '$productName' hiện có màu '$colorName' và còn hàng ạ."
        : "Rất tiếc, sản phẩm '$productName' màu '$colorName' hiện đã hết hàng.";

    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}

if ($intent === 'SanPhamBanChay') {
    // Lấy sản phẩm bán chạy trong tháng hiện tại (dựa theo đơn hàng, không cần kiểm tra đã giao)
    $stmt = $conn->prepare("
        SELECT sp.TenSanPham, SUM(ct.SoLuong) AS TongSoLuong
        FROM donhang d
        JOIN chitietdonhang ct ON d.MaDonHang = ct.MaDonHang
        JOIN sanphamnam sp ON ct.MaSP = sp.MaSP
        WHERE MONTH(d.NgayDat) = MONTH(CURDATE())
          AND YEAR(d.NgayDat) = YEAR(CURDATE())
        GROUP BY ct.MaSP
        ORDER BY TongSoLuong DESC
        LIMIT 3
    ");

    $stmt->execute();
    $stmt->bind_result($tenSanPham, $tongSoLuong);

    $topSanPham = [];
    while ($stmt->fetch()) {
        $topSanPham[] = "$tenSanPham (đã bán $tongSoLuong sản phẩm)";
    }
    $stmt->close();

    if (empty($topSanPham)) {
        $responseText = "Hiện chưa có sản phẩm nào được bán trong tháng này.";
    } else {
        $responseText = "Top sản phẩm bán chạy trong tháng này là:\n- " . implode("\n- ", $topSanPham);
    }

    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}


// ===== 3. Ý định: Kiểm tra loại của sản phẩm dựa trên context =====
if ($intent === 'KiemTraLoaiSanPham') {
    // Lấy product từ context trước đó
    $contexts = $request['queryResult']['outputContexts'] ?? [];
    $productName = '';

    foreach ($contexts as $ctx) {
        if (strpos($ctx['name'], 'sanpham-context') !== false && isset($ctx['parameters']['product'])) {
            $productName = strtolower(trim($ctx['parameters']['product']));
            break;
        }
    }

    if (empty($productName)) {
        echo json_encode([
            "fulfillmentText" => "Bạn vui lòng nói rõ tên sản phẩm trước ạ."
        ]);
        exit;
    }

    // Lấy danh sách loại từ bảng chi tiết loại sản phẩm
    $stmt = $conn->prepare("
        SELECT DISTINCT MaSize 
        FROM chitietsanphamnam ct
        JOIN sanphamnam sp ON ct.MaSP = sp.MaSP
        WHERE LOWER(sp.TenSanPham) LIKE ?
    ");
    $search = '%' . $productName . '%';
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $dsLoai = [];
    while ($row = $result->fetch_assoc()) {
        $dsLoai[] = $row['MaSize'];
    }
    $stmt->close();

    if (empty($dsLoai)) {
        $responseText = "Rất tiếc, hiện chưa có thông tin về loại cho sản phẩm '$productName'.";
    } else {
        $responseText = "Sản phẩm '$productName' hiện có các loại: " . implode(', ', $dsLoai) . ".";
    }

    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}

if ($intent === 'KiemTraLoaiVaMauSanPham') {
    // Lấy product từ context
    $contexts = $request['queryResult']['outputContexts'] ?? [];
    $productName = '';

    foreach ($contexts as $ctx) {
        if (strpos($ctx['name'], 'sanpham-context') !== false && isset($ctx['parameters']['product'])) {
            $productName = $ctx['parameters']['product'];
            break;
        }
    }

    if (empty($productName)) {
        echo json_encode([
            "fulfillmentText" => "Bạn đang hỏi về sản phẩm nào ạ?"
        ]);
        exit;
    }

    // Tìm MaSP
    $stmt = $conn->prepare("SELECT MaSP FROM sanphamnam WHERE LOWER(TenSanPham) LIKE ?");
    $search = "%" . $productName . "%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $stmt->bind_result($masp);
    $stmt->fetch();
    $stmt->close();

    if (empty($masp)) {
        echo json_encode(["fulfillmentText" => "Không tìm thấy sản phẩm '$productName'."]);
        exit;
    }

    // Lấy danh sách màu theo sản phẩm
    $stmt = $conn->prepare("SELECT DISTINCT MaMau FROM chitietsanphamnam WHERE MaSP = ?");
    $stmt->bind_param("s", $masp);
    $stmt->execute();
    $result = $stmt->get_result();

    $ds_mausac = [];
    while ($row = $result->fetch_assoc()) {
        $ds_mausac[] = $row['MaMau'];
    }
    $stmt->close();

    if (empty($ds_mausac)) {
        $responseText = "Sản phẩm '$productName' hiện không có màu nào trong hệ thống.";
    } else {
        $responseText = "Sản phẩm '$productName' hiện có các màu: " . implode(', ', $ds_mausac) . ".";
    }

    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}


// ===== Nếu không khớp với intent nào =====
echo json_encode([
    "fulfillmentText" => "Xin lỗi, tôi chưa hiểu rõ yêu cầu của bạn."
]);
