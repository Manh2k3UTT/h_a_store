<?php include('include/header.php'); ?>

<div style="padding: 40px; font-family: Arial, sans-serif;">
    <h1>Giới thiệu về H&A</h1>
    <p>H&A là thương hiệu thời trang mang đến phong cách hiện đại, trẻ trung và chất lượng cao dành cho cả nam và nữ. Dưới đây là hệ thống cửa hàng tại Hà Nội.</p>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start; margin-top: 30px;">
        <!-- Phần chọn quận (nhỏ hơn) -->
        <div style="flex: 0.4; min-width: 180px;">
            <label for="districtSelect" style="font-weight: bold;">Chọn quận:</label><br>
            <select id="districtSelect" style="padding: 10px; font-size: 16px; margin-top: 10px; width: 100%;">
                <option value="">-- Chọn quận --</option>
                <option value="haibatrung">Quận Hai Bà Trưng</option>
                <option value="dongda">Quận Đống Đa</option>
                <option value="caugiay">Quận Cầu Giấy</option>
                <option value="bactuliem">Quận Bắc Từ Liêm</option>
                <option value="thanhxuan">Quận Thanh Xuân</option>
                <option value="longbien">Quận Long Biên</option>
                <option value="hadong">Quận Hà Đông</option>
                <option value="hoangmai">Quận Hoàng Mai</option>
                <option value="namtuliem">Quận Nam Từ Liêm</option>
            </select>
        </div>

        <!-- Khung Google Map (to hơn) -->
        <div style="flex: 1.6; min-width: 400px;">
            <iframe id="mapFrame"
                src=""
                width="100%" 
                height="500" 
                style="border:1px solid #ccc; border-radius: 8px;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</div>

<script>
    const mapFrame = document.getElementById("mapFrame");
    const districtSelect = document.getElementById("districtSelect");

    const mapUrls = {
    caugiay: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7447.870082983077!2d105.803703!3d21.035285!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9889bcbb2b%3A0x21812859b53128ad!2zUGFudGlvIE5ndXnhu4VuIEtow6FuaCBUb8Ogbg!5e0!3m2!1sen!2sus!4v1751956015689!5m2!1sen!2sus",
    dongda: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7449.034091307625!2d105.835784!3d21.011988!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab86e01ccec5%3A0x476e43e6d3e89d91!2zUGFudGlvIFjDoyDEkMOgbg!5e0!3m2!1sen!2sus!4v1751955965162!5m2!1sen!2sus",
    haibatrung: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7449.766279690142!2d105.864429!3d20.997321!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135adc73d5f3a75%3A0xba7e6c044a9a2f76!2sPantio%20Minh%20Khai!5e0!3m2!1sen!2sus!4v1751955486341!5m2!1sen!2sus",
    bactuliem: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d465.4374661281175!2d105.780708!3d21.052694!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31345593b3245b6d%3A0xae56b6a7812d36d1!2zVmluY29tIFBsYXphIELhuq9jIFThu6sgTGnDqm0!5e0!3m2!1svi!2sus!4v1751956372689!5m2!1svi!2sus",
    thanhxuan: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7449.546928994208!2d105.815738!3d21.001716!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac9b0ee2a33d%3A0x95ccc2e05eb470c5!2sPantio%20Vincom%20Royal%20City!5e0!3m2!1svi!2sus!4v1751956448626!5m2!1svi!2sus",
    longbien: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7447.099062875949!2d105.916661!3d21.050703!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135a93665417795%3A0x6e84e8097a063743!2sPantio%20Vincom%20Plaza%20Long%20Bi%C3%AAn!5e0!3m2!1svi!2sus!4v1751956485218!5m2!1svi!2sus",
    hadong: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7450.132814410686!2d105.751122!3d20.989975!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31345313bd3d982b%3A0x6feb2e43fd52e39f!2zUGFudGlvIEFlb24gTWFsbCBIw6AgxJDDtG5n!5e0!3m2!1svi!2sus!4v1751956522656!5m2!1svi!2sus",
    hoangmai: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d931.4189731820044!2d105.825624!3d20.965527!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ad830d5b748f%3A0x1024479a7cf008a1!2zUGFudGlvIExpbmggxJDDoG0!5e0!3m2!1sen!2sus!4v1751956548641!5m2!1sen!2sus",
    namtuliem: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7448.875178880924!2d105.77767600000001!3d21.01517!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454aa0fed56c7%3A0x1ba275bf03d4c1a9!2zVHJ1bmcgdMOibSB0aMawxqFuZyBt4bqhaSBUaGUgR2FyZGVu!5e0!3m2!1svi!2sus!4v1751956664293!5m2!1svi!2sus",
    };

    districtSelect.addEventListener("change", function () {
        const value = districtSelect.value;
        mapFrame.src = mapUrls[value] || "";
    });
</script>

<?php include('include/footer.php'); ?>
