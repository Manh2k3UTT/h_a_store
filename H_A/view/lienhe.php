<?php
include('include/header.php');

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "h_a");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = $conn->real_escape_string(trim($_POST['hoten'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $noidung = $conn->real_escape_string(trim($_POST['noidung'] ?? ''));

    if (!empty($hoten) && !empty($email) && !empty($noidung)) {
        $sql = "INSERT INTO lienhe (HoTen, Email, NoiDung) VALUES ('$hoten', '$email', '$noidung')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Cảm ơn bạn đã liên hệ!'); window.location.href='lienhe.php';</script>";
            exit;
        } else {
            echo "<script>alert('Lỗi khi gửi liên hệ.');</script>";
        }
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin.');</script>";
    }
}
?>

<style>
    .contact-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        padding: 40px;
        font-family: Arial, sans-serif;
    }

    .contact-info {
        flex: 1;
        min-width: 280px;
    }

    .contact-info h2, .contact-info h3 {
        color: #d32f2f;
        margin-bottom: 12px;
    }

    .contact-info p {
        margin: 8px 0;
        line-height: 1.5;
    }

    .contact-form {
        flex: 1;
        min-width: 280px;
    }

    .contact-form label {
        font-weight: bold;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 10px;
        margin: 8px 0 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
    }

    .contact-form button {
        padding: 10px 20px;
        background-color: #d32f2f;
        color: white;
        border: none;
        border-radius: 999px;
        font-weight: bold;
        cursor: pointer;
    }

    .contact-form button:hover {
        opacity: 0.9;
    }

    .map-section {
        padding: 40px;
    }

    .map-section iframe {
        width: 100%;
        height: 400px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .contact-wrapper {
            flex-direction: column;
        }
    }
</style>

<div class="contact-wrapper">
    <!-- Thông tin liên hệ -->
    <div class="contact-info">
        <h2>Liên hệ với H&A</h2>
        <p><strong>Địa chỉ:</strong> 123 Minh Khai, Hai Bà Trưng, Hà Nội</p>
        <p><strong>Điện thoại:</strong> 1900 988 903</p>
        <p><strong>Email:</strong> h_a_support@gmail.com</p>
        <p><strong>Giờ làm việc:</strong> 9:00 – 21:00 (Tất cả các ngày)</p>

        <h3>Kết nối với chúng tôi</h3>
        <p><a href="#" style="color: #d32f2f; text-decoration: none;">Facebook</a> | 
           <a href="#" style="color: #d32f2f; text-decoration: none;">Instagram</a> | 
           <a href="#" style="color: #d32f2f; text-decoration: none;">Zalo</a>
        </p>
    </div>

    <!-- Form liên hệ -->
    <div class="contact-form">
        <h2>Gửi thắc mắc</h2>
        <form method="post">
            <label>Họ tên:</label>
            <input type="text" name="hoten" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Nội dung:</label>
            <textarea name="noidung" rows="5" required></textarea>

            <button type="submit">Gửi</button>
        </form>
    </div>
</div>

<!-- Bản đồ -->
<div class="map-section">
    <h2 style="color: #d32f2f; font-family: Arial;">Bản đồ trụ sở chính</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7449.766279690142!2d105.864429!3d20.997321!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135adc73d5f3a75%3A0xba7e6c044a9a2f76!2sPantio%20Minh%20Khai!5e0!3m2!1svi!2s!4v1751955486341!5m2!1svi!2s" allowfullscreen="" loading="lazy"></iframe>
</div>

<?php include('include/footer.php'); ?>
