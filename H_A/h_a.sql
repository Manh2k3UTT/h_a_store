-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 16, 2025 lúc 08:50 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `h_a`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluan`
--

CREATE TABLE `binhluan` (
  `MaBL` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `MaKH` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `ThoiGian` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `binhluan`
--

INSERT INTO `binhluan` (`MaBL`, `MaSP`, `MaKH`, `NoiDung`, `ThoiGian`) VALUES
(27, 20, 10, 'áO ĐẸP', '2025-07-09 04:28:54'),
(28, 20, 10, 'áo như l\r\n', '2025-07-09 04:36:39'),
(29, 20, 10, 'áo như l\r\n', '2025-07-09 04:38:26'),
(30, 20, 10, 'yêu cầu thêm màu sắc\r\n', '2025-07-09 04:38:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `MaCT` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `MaMau` varchar(50) NOT NULL,
  `MaSize` varchar(50) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`MaCT`, `MaDonHang`, `MaSP`, `MaMau`, `MaSize`, `SoLuong`, `DonGia`) VALUES
(14, 16, 17, 'Trắng', 'S', 1, 50000),
(15, 17, 19, 'Đỏ', 'M', 1, 250000),
(16, 18, 16, 'Trắng', 'L', 1, 50000),
(17, 18, 17, 'Trắng', 'S', 1, 50000),
(18, 19, 16, 'Trắng', 'L', 1, 45000),
(19, 19, 17, 'Trắng', 'S', 1, 45000),
(20, 20, 18, 'Đen', 'M', 1, 85000),
(21, 20, 16, 'Trắng', 'L', 1, 45000),
(22, 21, 16, 'Trắng', 'L', 1, 30000),
(23, 22, 16, 'Trắng', 'L', 1, 30000),
(24, 23, 16, 'Trắng', 'M', 1, 50000),
(25, 24, 16, 'Trắng', 'M', 2, 40000),
(26, 24, 16, 'Trắng', 'S', 1, 40000),
(27, 25, 20, 'Hồng', 'M', 3, 299000),
(28, 25, 22, 'Trắng', 'L', 5, 419000),
(29, 25, 30, 'Đen', 'L', 5, 369000),
(30, 26, 20, 'Hồng', 'M', 1, 299000),
(31, 26, 22, 'Trắng', 'L', 1, 419000),
(32, 27, 34, 'Trắng', 'L', 8, 598000),
(33, 29, 20, '0', '0', 3, 299000),
(34, 29, 20, '0', '0', 2, 299000),
(35, 29, 20, '0', '0', 2, 299000),
(36, 29, 22, '0', '0', 2, 419000),
(37, 29, 34, '0', '0', 8, 598000),
(38, 29, 34, '0', '0', 1, 598000),
(39, 30, 34, '0', '0', 1, 598000),
(40, 30, 20, '0', '0', 1, 299000),
(41, 31, 34, '0', '0', 1, 598000),
(42, 32, 20, '0', '0', 5, 299000),
(43, 32, 34, '0', '0', 6, 598000),
(44, 32, 22, '0', '0', 5, 419000),
(45, 33, 20, '0', '0', 1, 299000),
(46, 34, 22, '0', '0', 1, 419000),
(47, 35, 22, '0', '0', 2, 419000),
(48, 36, 22, '0', '0', 1, 419000),
(49, 37, 22, '0', '0', 1, 377100),
(50, 38, 22, '0', '0', 1, 377100),
(51, 39, 22, '0', '0', 1, 377100),
(52, 39, 36, '0', '0', 1, 1998000),
(53, 40, 36, 'Nâu', 'M', 1, 1998000),
(54, 40, 22, 'Trắng', 'S', 1, 377100),
(55, 41, 20, 'Hồng', 'XL', 1, 299000),
(56, 42, 36, 'Nâu', 'S', 1, 1998000),
(57, 42, 20, 'Hồng', 'S', 2, 299000),
(58, 43, 20, 'Hồng', 'XL', 3, 299000),
(59, 43, 34, 'Trắng', 'S', 2, 598000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietloainam`
--

CREATE TABLE `chitietloainam` (
  `MaChiTiet` int(11) NOT NULL,
  `TenChiTiet` varchar(100) NOT NULL,
  `MaLoai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietloainam`
--

INSERT INTO `chitietloainam` (`MaChiTiet`, `TenChiTiet`, `MaLoai`) VALUES
(12, 'Áo sơ mi', 5),
(13, 'Áo vest', 5),
(14, 'Áo demi', 5),
(15, 'Áo dài', 5),
(16, 'Áo dệt kim', 5),
(17, 'Áo len', 5),
(18, 'Đầm công sở', 4),
(19, 'Đầm dạo phố', 4),
(20, 'Đầm dạ hội', 4),
(21, 'Quần dài', 6),
(22, 'Quần lửng', 6),
(23, 'Quần short', 6),
(24, 'Quần jean', 6),
(25, 'Áo sơ mi', 1),
(26, 'Áo polo', 1),
(27, 'Quần âu', 2),
(28, 'Chân váy dài', 3),
(29, 'Chân váy ngắn', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietloainu`
--

CREATE TABLE `chitietloainu` (
  `MaChiTiet` int(11) NOT NULL,
  `TenChiTiet` varchar(100) NOT NULL,
  `MaLoai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietsanphamnam`
--

CREATE TABLE `chitietsanphamnam` (
  `MaSP` int(11) NOT NULL,
  `MaSize` varchar(10) NOT NULL,
  `MaMau` varchar(50) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietsanphamnam`
--

INSERT INTO `chitietsanphamnam` (`MaSP`, `MaSize`, `MaMau`, `SoLuong`, `HinhAnh`) VALUES
(22, 'L', 'Trắng', 6, 'Áo sơ mi nam vải trơn màu có túi ngực trái form Casual Fit kiểu dáng ngắn tay trắng.webp'),
(22, 'M', 'Trắng', 5, 'Áo sơ mi nam vải trơn màu có túi ngực trái form Casual Fit kiểu dáng ngắn tay trắng.webp'),
(22, 'S', 'Trắng', 8, 'Áo sơ mi nam vải trơn màu có túi ngực trái form Casual Fit kiểu dáng ngắn tay trắng.webp'),
(22, 'XL', 'Trắng', 8, 'Áo sơ mi nam vải trơn màu có túi ngực trái form Casual Fit kiểu dáng ngắn tay trắng.webp'),
(23, 'L', 'Hồng', 16, 'Áo sơ mi nam vải hoạ tiết form Casual Fit kiểu dáng ngắn tay hồng.webp'),
(23, 'M', 'Hồng', 20, 'Áo sơ mi nam vải hoạ tiết form Casual Fit kiểu dáng ngắn tay hồng.webp'),
(23, 'S', 'Hồng', 16, 'Áo sơ mi nam vải hoạ tiết form Casual Fit kiểu dáng ngắn tay hồng.webp'),
(23, 'L', 'Xanh da trời', 20, ''),
(23, 'M', 'Xanh da trời', 16, ''),
(23, 'S', 'Xanh da trời', 16, ''),
(21, 'M', 'Đen', 0, 'áo sơ mi nam tay ngắn đen.webp'),
(21, 'S', 'Đen', 0, 'áo sơ mi nam tay ngắn đen.webp'),
(21, 'XL', 'Đen', 0, 'áo sơ mi nam tay ngắn đen.webp'),
(24, 'L', 'Hồng', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay  đen.jpg'),
(24, 'M', 'Hồng', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay  đen.jpg'),
(24, 'S', 'Hồng', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay  đen.jpg'),
(24, 'XL', 'Hồng', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay  đen.jpg'),
(24, 'L', 'Xanh da trời', 0, ''),
(24, 'M', 'Xanh da trời', 0, ''),
(24, 'S', 'Xanh da trời', 0, ''),
(24, 'XL', 'Xanh da trời', 0, ''),
(24, 'L', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(24, 'M', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(24, 'S', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(24, 'XL', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(25, 'L', 'Trắng', 0, 'Áo Polo nam vải trơn màu form Regular Fit kiểu dáng ngắn tay trắng.webp'),
(25, 'M', 'Trắng', 0, 'Áo Polo nam vải trơn màu form Regular Fit kiểu dáng ngắn tay trắng.webp'),
(25, 'S', 'Trắng', 0, 'Áo Polo nam vải trơn màu form Regular Fit kiểu dáng ngắn tay trắng.webp'),
(25, 'XL', 'Trắng', 0, 'Áo Polo nam vải trơn màu form Regular Fit kiểu dáng ngắn tay trắng.webp'),
(25, 'L', 'Xanh da trời', 0, ''),
(25, 'M', 'Xanh da trời', 0, ''),
(25, 'S', 'Xanh da trời', 0, ''),
(25, 'XL', 'Xanh da trời', 0, ''),
(25, 'L', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(25, 'M', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(25, 'S', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(25, 'XL', 'Đen', 0, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng ngắn tay đen.webp'),
(26, 'L', 'Vàng', 2, 'Áo Polo nam vải trơn màu hoạ tiết kẻ cổ áo form slim fit kiểu dáng ngắn tay vàng.webp'),
(26, 'M', 'Vàng', 1, 'Áo Polo nam vải trơn màu hoạ tiết kẻ cổ áo form slim fit kiểu dáng ngắn tay vàng.webp'),
(26, 'S', 'Vàng', 0, 'Áo Polo nam vải trơn màu hoạ tiết kẻ cổ áo form slim fit kiểu dáng ngắn tay vàng.webp'),
(26, 'L', 'Xanh da trời', 0, ''),
(26, 'M', 'Xanh da trời', 0, ''),
(26, 'S', 'Xanh da trời', 0, ''),
(27, 'M', 'Đen', 0, 'Áo Polo thể thao nam vải trơn màu form Sport kiểu dáng ngắn tay đen.webp'),
(27, 'S', 'Đen', 0, 'Áo Polo thể thao nam vải trơn màu form Sport kiểu dáng ngắn tay đen.webp'),
(27, 'XL', 'Đen', 0, 'Áo Polo thể thao nam vải trơn màu form Sport kiểu dáng ngắn tay đen.webp'),
(30, 'L', 'Đen', 5, 'Quần dài nam.webp'),
(30, 'M', 'Đen', 0, 'Quần dài nam.webp'),
(31, 'L', 'Cam', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai cam.jpg'),
(31, 'M', 'Cam', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai cam.jpg'),
(31, 'S', 'Cam', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai cam.jpg'),
(31, 'L', 'Xanh lá cây', 0, ''),
(31, 'M', 'Xanh lá cây', 0, ''),
(31, 'S', 'Xanh lá cây', 0, ''),
(31, 'L', 'Đỏ', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai xanh lá.webp'),
(31, 'M', 'Đỏ', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai xanh lá.webp'),
(31, 'S', 'Đỏ', 0, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai xanh lá.webp'),
(32, 'L', 'Nâu', 0, 'Áo sơ mi công sở vải thô lụa dáng suông cổ đức tay măng sec ngực áo phối viền thời trang nâu.webp'),
(32, 'M', 'Nâu', 0, 'Áo sơ mi công sở vải thô lụa dáng suông cổ đức tay măng sec ngực áo phối viền thời trang nâu.webp'),
(32, 'S', 'Nâu', 0, 'Áo sơ mi công sở vải thô lụa dáng suông cổ đức tay măng sec ngực áo phối viền thời trang nâu.webp'),
(33, 'L', 'Vàng', 0, 'Áo sơ mi công sở vải thô hoa dáng lửng cổ xẻ V tay áo bo chun gấu phối vạt thắt tạo kiểu vàng.webp'),
(33, 'M', 'Vàng', 0, 'Áo sơ mi công sở vải thô hoa dáng lửng cổ xẻ V tay áo bo chun gấu phối vạt thắt tạo kiểu vàng.webp'),
(33, 'S', 'Vàng', 0, 'Áo sơ mi công sở vải thô hoa dáng lửng cổ xẻ V tay áo bo chun gấu phối vạt thắt tạo kiểu vàng.webp'),
(33, 'L', 'Xanh da trời', 0, ''),
(33, 'M', 'Xanh da trời', 0, ''),
(33, 'S', 'Xanh da trời', 0, ''),
(34, 'L', 'Trắng', 8, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang trắng.webp'),
(34, 'M', 'Trắng', 10, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang trắng.webp'),
(34, 'S', 'Trắng', 8, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang trắng.webp'),
(34, 'L', 'Đen', 10, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang đen.webp'),
(34, 'M', 'Đen', 4, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang đen.webp'),
(34, 'S', 'Đen', 10, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang đen.webp'),
(35, 'L', 'Trắng', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu trắng.webp'),
(35, 'M', 'Trắng', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu trắng.webp'),
(35, 'S', 'Trắng', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu trắng.webp'),
(35, 'L', 'Đen', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu đen.webp'),
(35, 'M', 'Đen', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu đen.webp'),
(35, 'S', 'Đen', 0, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu đen.webp'),
(36, 'L', 'Nâu', 9, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước nâu.webp'),
(36, 'M', 'Nâu', 9, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước nâu.webp'),
(36, 'S', 'Nâu', 9, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước nâu.webp'),
(36, 'L', 'Trắng', 10, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước trắng.webp'),
(36, 'M', 'Trắng', 10, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước trắng.webp'),
(36, 'S', 'Trắng', 10, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước trắng.webp'),
(37, 'L', 'Hồng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong hồng.webp'),
(37, 'M', 'Hồng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong hồng.webp'),
(37, 'S', 'Hồng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong hồng.webp'),
(37, 'L', 'Vàng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong vàng.webp'),
(37, 'M', 'Vàng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong vàng.webp'),
(37, 'S', 'Vàng', 0, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong vàng.webp'),
(38, 'L', 'Đen', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đen.webp'),
(38, 'M', 'Đen', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đen.webp'),
(38, 'S', 'Đen', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đen.webp'),
(38, 'L', 'Đỏ', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đỏ.webp'),
(38, 'M', 'Đỏ', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đỏ.webp'),
(38, 'S', 'Đỏ', 0, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân đỏ.webp'),
(20, 'M', 'Hồng', 7, 'áo sơ mi nam hồng.webp'),
(20, 'S', 'Hồng', 7, 'áo sơ mi nam hồng.webp'),
(20, 'XL', 'Hồng', 8, 'áo sơ mi nam hồng.webp'),
(20, 'M', 'Trắng', 10, 'áo sơ mi nam.webp'),
(20, 'S', 'Trắng', 10, 'áo sơ mi nam.webp'),
(20, 'XL', 'Trắng', 10, 'áo sơ mi nam.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietsanphamnu`
--

CREATE TABLE `chitietsanphamnu` (
  `MaSP` int(11) NOT NULL,
  `MaSize` varchar(10) NOT NULL,
  `MaMau` varchar(50) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `HinhAnh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `MaDonHang` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `TenNguoiNhan` varchar(100) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `DiaChi` text DEFAULT NULL,
  `NgayDat` datetime DEFAULT NULL,
  `TongTien` double DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `PhuongThucThanhToan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`MaDonHang`, `MaKH`, `TenNguoiNhan`, `SDT`, `DiaChi`, `NgayDat`, `TongTien`, `TrangThai`, `PhuongThucThanhToan`) VALUES
(16, NULL, 'Lê Đức Mạnh', '0866889003', 'HD', '2025-06-06 21:27:42', 50000, 'Đã giao hàng', 'cod'),
(17, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-06 22:32:38', 250000, 'Đã giao hàng', 'cod'),
(18, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải DƯơng', '2025-06-07 04:05:58', 100000, 'Chờ xác nhận', 'cod'),
(19, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-07 04:13:36', 90000, 'Đã giao hàng', 'cod'),
(20, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-12 04:42:21', 130000, 'Đã giao hàng', 'cod'),
(21, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-07 05:35:33', 30000, 'Đã xác nhận', 'chuyenkhoan'),
(22, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-07 05:36:11', 30000, 'Chờ xác nhận', 'cod'),
(23, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-07 05:40:13', 50000, 'Hủy', 'cod'),
(24, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-07 05:42:48', 120000, 'Hủy', 'cod'),
(25, NULL, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-17 17:13:07', 4837000, 'Chờ xác nhận', 'cod'),
(26, NULL, 'Nguyễn Văn A', '0913087903', 'Hà Nội', '2025-06-18 06:32:27', 718000, 'Chờ xác nhận', 'cod'),
(27, NULL, 'Lê Đức Mạnh', '0866889003', 'Hà Nội', '2025-06-18 10:13:00', 4784000, 'Đã giao hàng', 'cod'),
(28, NULL, 'Lê Đức Mạnh', '0913087903', 'Hải Dương', '2025-06-29 21:45:51', 8014000, 'Chờ xác nhận', 'cod'),
(29, 2, 'Lê Đức Mạnh', '0866889003', 'HD', '2025-06-29 21:47:46', 8313000, 'Chờ xác nhận', 'cod'),
(30, 2, 'Lê Đức Mạnh', '0866889003', 'Hải Dương', '2025-06-29 22:11:45', 897000, 'Chờ xác nhận', 'cod'),
(31, 2, 'Nguyễn Văn A', '0913087903', 'a', '2025-06-29 22:14:04', 598000, 'Chờ xác nhận', 'cod'),
(32, 2, 'Nguyễn Văn A', '0913087903', 'Hà Nội', '2025-07-01 06:27:48', 7178000, 'Đã giao hàng', 'cod'),
(33, 4, 'Lê Đức Mạnh', '0866889003', 'aa', '2025-07-07 19:19:46', 299000, 'Chờ xác nhận', 'online'),
(34, 4, 'Lê Đức Mạnh', '0866889003', 'đasadsda', '2025-07-07 19:20:16', 419000, 'Chờ xác nhận', 'online'),
(35, 4, 'Lê Đức Mạnh', '0866889003', 'a', '2025-07-07 19:21:22', 838000, 'Chờ xác nhận', 'online'),
(36, 4, 'd', '0866889003', 'd', '2025-07-07 19:21:59', 419000, 'Chờ xác nhận', 'online'),
(37, 4, 'Lê Đức Mạnh', '0866889003', 'hd', '2025-07-07 21:41:31', 377100, 'Đã thanh toán', 'VNPay'),
(38, 4, 'Lê Đức Mạnh', '0866889003', 'hd', '2025-07-07 21:52:22', 377100, 'Đã giao hàng', 'VNPay'),
(39, 4, 'Lê Đức Mạnh', '0866889003', 'hd', '2025-07-07 22:47:03', 2375100, 'Đã giao hàng', 'VNPay'),
(40, 4, 'Lê Đức Mạnh', '0866889003', 'hd', '2025-07-07 22:48:17', 2375100, 'Chờ xác nhận', 'VNPay'),
(41, 4, 'Lê Đức Mạnh', '0866889003', 'hn', '2025-07-07 22:50:46', 299000, 'Đã giao hàng', 'cod'),
(42, 4, 'Lê Đức Mạnh', '0866889003', 'Triều Khúc', '2025-07-09 03:56:52', 2596000, 'Chờ xác nhận', 'VNPay'),
(43, 10, 'Nguyễn Văn A', '0913087903', 'Triều Khúc, Thanh Xuân, Hà Nội', '2025-07-09 04:26:27', 2093000, 'Đã giao hàng', 'VNPay');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giaohang`
--

CREATE TABLE `giaohang` (
  `MaGiaoHang` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `NgayGiao` datetime DEFAULT current_timestamp(),
  `TrangThai` enum('Đang giao hàng','Đã giao hàng','Hủy') DEFAULT 'Đang giao hàng',
  `NgayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `giaohang`
--

INSERT INTO `giaohang` (`MaGiaoHang`, `MaDonHang`, `NgayGiao`, `TrangThai`, `NgayCapNhat`) VALUES
(9, 16, '2025-06-07 02:28:08', 'Đã giao hàng', '2025-06-07 02:58:51'),
(10, 17, '2025-06-07 03:32:51', 'Đã giao hàng', '2025-06-07 03:33:07'),
(11, 19, '2025-06-07 09:14:13', 'Đã giao hàng', '2025-06-07 09:14:18'),
(12, 20, '2025-06-12 09:42:31', 'Đã giao hàng', '2025-06-12 09:42:37'),
(13, 24, '2025-06-07 20:57:01', 'Hủy', '2025-06-07 20:57:09'),
(14, 23, '2025-06-07 20:57:04', 'Hủy', '2025-06-07 20:57:11'),
(15, 21, '2025-06-07 20:57:16', 'Đang giao hàng', '2025-06-07 20:57:16'),
(16, 27, '2025-06-18 15:13:13', 'Đã giao hàng', '2025-07-09 08:49:01'),
(17, 32, '2025-07-01 11:29:16', 'Đã giao hàng', '2025-07-01 11:30:25'),
(18, 38, '2025-07-08 02:52:28', 'Đã giao hàng', '2025-07-08 02:52:32'),
(19, 39, '2025-07-08 04:04:38', 'Đã giao hàng', '2025-07-08 04:05:44'),
(20, 39, '2025-07-08 04:05:13', 'Đã giao hàng', '2025-07-08 04:05:44'),
(21, 41, '2025-07-08 04:22:28', 'Đã giao hàng', '2025-07-08 04:22:31'),
(22, 43, '2025-07-09 09:26:49', 'Đã giao hàng', '2025-07-09 09:27:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang_tam`
--

CREATE TABLE `giohang_tam` (
  `MaKH` int(11) NOT NULL,
  `MaSP` varchar(10) NOT NULL,
  `MaMau` varchar(10) NOT NULL,
  `MaSize` varchar(10) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `MaHD` int(11) NOT NULL,
  `MaKH` int(11) NOT NULL,
  `MaNV` int(11) DEFAULT NULL,
  `NgayDat` datetime DEFAULT current_timestamp(),
  `NgayGiao` datetime DEFAULT NULL,
  `TinhTrang` varchar(20) DEFAULT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `MaNVGH` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`MaHD`, `MaKH`, `MaNV`, `NgayDat`, `NgayGiao`, `TinhTrang`, `TongTien`, `MaNVGH`) VALUES
(70, 6, NULL, '2023-06-16 16:55:54', NULL, 'chưa duyệt', 11996000, NULL),
(86, 1, 3, '2023-06-19 16:52:51', NULL, 'Hủy Bỏ', 12658000, NULL),
(87, 1, NULL, '2023-06-19 17:20:30', NULL, 'chưa duyệt', 1460000, NULL),
(88, 1, 3, '2023-06-20 09:18:27', '2023-06-21 10:23:59', 'hoàn thành', 9577000, '3'),
(89, 1, 3, '2023-06-21 08:31:12', '2023-06-22 08:32:24', 'Đã duyệt', 3679000, NULL),
(90, 16, 3, '2023-09-04 18:51:14', '2023-09-05 19:06:11', 'Đã duyệt', 11078000, NULL),
(91, 16, 3, '2023-09-04 18:51:19', '2023-09-05 18:53:00', 'Đã duyệt', 11078000, NULL),
(92, 16, 3, '2023-09-04 18:54:46', '2023-09-05 18:55:09', 'hoàn thành', 10369000, '3'),
(93, 16, 3, '2023-09-04 19:04:18', '2023-09-05 19:04:53', 'hoàn thành', 11078000, '3'),
(94, 16, NULL, '2023-09-04 19:15:41', NULL, 'chưa duyệt', 3469000, NULL),
(95, 16, NULL, '2023-09-04 19:16:59', NULL, 'chưa duyệt', 3469000, NULL),
(96, 16, NULL, '2023-09-04 19:17:09', NULL, 'chưa duyệt', 3469000, NULL),
(97, 16, 3, '2023-09-16 23:23:09', '2023-09-17 23:24:07', 'Đã duyệt', 6938000, NULL),
(98, 16, 3, '2023-12-08 16:24:26', '2023-12-09 16:25:04', 'hoàn thành', 3469000, '3'),
(99, 16, 3, '2023-12-16 00:22:13', '2023-12-17 00:22:51', 'hoàn thành', 7558000, '3'),
(100, 16, NULL, '2024-03-20 16:43:58', NULL, 'chưa duyệt', 1200000, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` int(11) NOT NULL,
  `TenKH` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `SDT` bigint(12) NOT NULL,
  `DiaChi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `MatKhau` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `Email`, `SDT`, `DiaChi`, `MatKhau`) VALUES
(4, 'Lê Đức Mạnh', 'cuusoi@gmail.com', 866889003, 'Hải Dương', '14112003'),
(5, 'Nguyễn Văn Hùng', 'hung.nguyen@example.com', 901234567, '123 Đường Láng, Đống Đa, Hà Nội', 'hung123'),
(6, 'Trần Thị Mai', 'mai.tran@example.com', 912345678, '45 Phố Huế, Hai Bà Trưng, Hà Nội', 'maimai@2025'),
(7, 'Lê Minh Tuấn', 'tuan.le@example.com', 934567890, '67 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'lemtuan!'),
(8, 'Phạm Hoàng Anh', 'hoanganh.pham@example.com', 971122334, '89 Trần Duy Hưng, Cầu Giấy, Hà Nội', 'hoanganh456'),
(9, 'Đỗ Thị Hương', 'huong.do@example.com', 969988776, '12 Lạc Long Quân, Tây Hồ, Hà Nội', 'Huong#99'),
(10, 'Nguyễn Văn A', 'nva@gmail.com', 913087903, 'Triều Khúc, Hà Nội', '123456');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `MaKM` int(20) NOT NULL,
  `TenKM` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `KM_PT` int(11) DEFAULT 0,
  `TienKM` decimal(12,2) DEFAULT 0.00,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyenmai`
--

INSERT INTO `khuyenmai` (`MaKM`, `TenKM`, `MoTa`, `KM_PT`, `TienKM`, `NgayBatDau`, `NgayKetThuc`) VALUES
(2, 'giảm 15000', '', 0, 15000.00, '2025-06-04', '2025-07-03'),
(7, 'Giảm 10 %', 'Giảm 10 %', 10, 0.00, '2025-06-30', '2025-07-10'),
(8, 'Giảm 15%', '', 15, 0.00, '2025-06-30', '2025-07-06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kichco`
--

CREATE TABLE `kichco` (
  `MaSize` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `kichco`
--

INSERT INTO `kichco` (`MaSize`) VALUES
('L'),
('M'),
('S'),
('XL');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lienhe`
--

CREATE TABLE `lienhe` (
  `MaLH` int(11) NOT NULL,
  `HoTen` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `NoiDung` text DEFAULT NULL,
  `NgayGui` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lienhe`
--

INSERT INTO `lienhe` (`MaLH`, `HoTen`, `Email`, `NoiDung`, `NgayGui`) VALUES
(2, 'Nguyễn Văn A', 'nva@gmail.com', 'mong shop thêm quần âu', '2025-07-09 09:29:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loainam`
--

CREATE TABLE `loainam` (
  `MaLoai` int(11) NOT NULL,
  `TenLoai` varchar(100) NOT NULL,
  `GioiTinh` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loainam`
--

INSERT INTO `loainam` (`MaLoai`, `TenLoai`, `GioiTinh`) VALUES
(1, 'Áo', 'Nam'),
(2, 'Quần', 'Nam'),
(3, 'Chân váy', 'Nữ'),
(4, 'Đầm', 'Nữ'),
(5, 'Áo cho nữ', 'Nữ'),
(6, 'Quần', 'Nữ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loainu`
--

CREATE TABLE `loainu` (
  `MaLoai` int(11) NOT NULL,
  `TenLoai` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mau`
--

CREATE TABLE `mau` (
  `MaMau` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `mau`
--

INSERT INTO `mau` (`MaMau`) VALUES
('Cam'),
('Hồng'),
('Nâu'),
('Trắng'),
('Vàng'),
('Xanh da trời'),
('Xanh lá cây'),
('Xanh lam'),
('Đen'),
('Đỏ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNV` int(11) NOT NULL,
  `TenNV` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `SDT` int(12) NOT NULL,
  `DiaChi` text NOT NULL,
  `MatKhau` varchar(50) NOT NULL,
  `Quyen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`MaNV`, `TenNV`, `Email`, `SDT`, `DiaChi`, `MatKhau`, `Quyen`) VALUES
(1, 'admin', 'admin@gmail.com', 866889003, 'Hải Dương', '123456', 1),
(3, 'Nguyễn Văn A', 'A@gmail.com', 912567889, 'HN', '123456', 3),
(4, 'Trần Văn B', 'B@gmail.com', 2147483647, 'Hải Dương', '123456', 5),
(5, 'Phạm Thị C', 'C@gmail.com', 789651231, 'Hà Nội', '123456', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MaPN` int(11) NOT NULL,
  `MaNV` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(10,0) DEFAULT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `NgayNhap` datetime NOT NULL DEFAULT current_timestamp(),
  `Note` varchar(100) DEFAULT NULL,
  `Size` int(11) NOT NULL,
  `Mau` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `phieunhap`
--

INSERT INTO `phieunhap` (`MaPN`, `MaNV`, `MaSP`, `SoLuong`, `DonGia`, `TongTien`, `NgayNhap`, `Note`, `Size`, `Mau`) VALUES
(18, 1, 22, 9, NULL, 3771000, '2025-06-07 00:00:00', '', 0, 'Trắng'),
(19, 1, 20, 10, NULL, 2990000, '2025-06-07 00:00:00', '', 0, 'Hồng'),
(20, 1, 30, 10, NULL, 3690000, '2025-06-07 00:00:00', '', 0, 'Đen'),
(21, 1, 20, 5, NULL, 1495000, '2025-06-07 00:00:00', '', 0, 'Hồng'),
(22, 1, 20, 5, NULL, 1495000, '2025-06-07 00:00:00', '', 0, 'Hồng'),
(23, 1, 20, 7, NULL, 2093000, '2025-06-07 00:00:00', '', 0, 'Hồng'),
(24, 1, 20, 6, NULL, 1794000, '2025-06-07 00:00:00', '', 0, 'Trắng'),
(25, 1, 20, 5, NULL, 1495000, '2025-06-07 00:00:00', '', 0, 'Trắng'),
(26, 1, 20, 7, NULL, 2093000, '2025-06-07 00:00:00', '', 0, 'Trắng'),
(27, 1, 34, 7, NULL, 4186000, '2025-06-18 00:00:00', '', 0, 'Trắng'),
(28, 1, 34, 7, NULL, 4186000, '2025-06-18 00:00:00', '', 0, 'Trắng'),
(29, 1, 34, 5, NULL, 2990000, '2025-06-18 00:00:00', '', 0, 'Trắng'),
(30, 1, 34, 4, NULL, 2392000, '2025-06-18 00:00:00', '', 0, 'Đen'),
(31, 1, 34, 4, NULL, 2392000, '2025-06-18 00:00:00', '', 0, 'Đen'),
(32, 1, 34, 4, NULL, 2392000, '2025-06-18 00:00:00', '', 0, 'Đen'),
(33, 1, 34, 10, NULL, 5980000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(34, 1, 34, 9, NULL, 5382000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(35, 1, 34, 4, NULL, 2392000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(36, 1, 34, 5, NULL, 2990000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(37, 1, 34, 6, NULL, 3588000, '2025-06-29 00:00:00', '', 0, 'Đen'),
(38, 1, 34, 6, NULL, 3588000, '2025-06-29 00:00:00', '', 0, 'Đen'),
(39, 1, 34, 6, NULL, 3588000, '2025-06-29 00:00:00', '', 0, 'Đen'),
(40, 1, 20, 12, NULL, 3588000, '2025-06-29 00:00:00', '', 0, 'Hồng'),
(41, 1, 20, 7, NULL, 2093000, '2025-06-29 00:00:00', '', 0, 'Hồng'),
(42, 1, 20, 5, NULL, 1495000, '2025-06-29 00:00:00', '', 0, 'Hồng'),
(43, 1, 20, 4, NULL, 1196000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(44, 1, 20, 5, NULL, 1495000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(45, 1, 20, 3, NULL, 897000, '2025-06-29 00:00:00', '', 0, 'Trắng'),
(46, 1, 22, 9, NULL, 3771000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(47, 1, 22, 10, NULL, 4190000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(48, 1, 22, 10, NULL, 4190000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(49, 1, 22, 10, NULL, 4190000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(50, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Nâu'),
(51, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Nâu'),
(52, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Nâu'),
(53, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(54, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(55, 1, 36, 10, NULL, 19980000, '2025-07-01 00:00:00', '', 0, 'Trắng'),
(56, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(57, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(58, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(59, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(60, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(61, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(62, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(63, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(64, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(65, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(66, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(67, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(68, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(69, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(70, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(71, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(72, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(73, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(74, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(75, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(76, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(77, 1, 23, 5, NULL, 2745000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(78, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(79, 1, 23, 4, NULL, 2196000, '2025-07-09 00:00:00', '', 0, 'Xanh da trời'),
(80, 1, 26, 1, NULL, 499000, '2025-07-09 00:00:00', '', 0, 'Vàng'),
(81, 1, 26, 1, NULL, 499000, '2025-07-09 00:00:00', '', 0, 'Vàng'),
(82, 1, 26, 1, NULL, 499000, '2025-07-09 00:00:00', '', 0, 'Vàng'),
(83, 1, 20, 7, NULL, 2093000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(84, 1, 20, 3, NULL, 897000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(85, 1, 20, 4, NULL, 1196000, '2025-07-09 00:00:00', '', 0, 'Hồng'),
(86, 1, 20, 1, NULL, 299000, '2025-07-09 00:00:00', '', 0, 'Trắng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuxuat`
--

CREATE TABLE `phieuxuat` (
  `MaPX` int(11) NOT NULL,
  `MaNV` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `Mau` varchar(100) NOT NULL,
  `Size` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(10,0) NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `Note` varchar(500) NOT NULL,
  `NgayXuat` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `phieuxuat`
--

INSERT INTO `phieuxuat` (`MaPX`, `MaNV`, `MaSP`, `Mau`, `Size`, `SoLuong`, `DonGia`, `TongTien`, `Note`, `NgayXuat`) VALUES
(1, 0, 11, 'Trắng', 0, 20, 0, 1000000, '', '2025-05-26 00:00:00'),
(2, 0, 11, 'Trắng', 0, 20, 0, 1000000, '', '2025-05-26 00:00:00'),
(3, 0, 11, 'Trắng', 0, 10, 0, 500000, 'xuất', '2025-05-26 00:00:00'),
(4, 0, 11, 'Trắng', 0, 10, 0, 500000, 'xuất', '2025-05-26 00:00:00'),
(5, 0, 11, 'Xanh lam', 0, 1, 0, 50000, 'xuất', '2025-05-26 00:00:00'),
(6, 0, 11, 'Trắng', 0, 20, 0, 1000000, 'trừ', '2025-05-26 00:00:00'),
(7, 0, 11, 'Trắng', 0, 10, 0, 500000, 'ra', '2025-05-26 00:00:00'),
(8, 1, 11, 'Trắng', 0, 20, 0, 1000000, '', '2025-05-26 00:00:00'),
(9, 1, 11, 'Trắng', 0, 20, 0, 1000000, '', '2025-05-26 00:00:00'),
(10, 1, 20, 'Hồng', 0, 1, 0, 299000, '', '2025-07-09 00:00:00'),
(11, 1, 20, 'Trắng', 0, 1, 0, 299000, '', '2025-07-09 00:00:00'),
(12, 1, 20, 'Hồng', 0, 3, 0, 897000, '', '2025-07-09 00:00:00'),
(13, 1, 20, 'Hồng', 0, 3, 0, 897000, '', '2025-07-09 00:00:00'),
(14, 1, 20, 'Hồng', 0, 2, 0, 598000, '', '2025-07-09 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quyen`
--

CREATE TABLE `quyen` (
  `id` int(11) NOT NULL,
  `Ten` varchar(100) NOT NULL,
  `MoTa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quyen`
--

INSERT INTO `quyen` (`id`, `Ten`, `MoTa`) VALUES
(1, 'Manager', 'chủ cửa hàng'),
(2, 'Project Manager', 'quản trị viên'),
(3, 'Quản lý Kho', ''),
(4, 'Nhân viên Bán Hàng', ''),
(5, 'Nhân viên giao hàng', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanphamnam`
--

CREATE TABLE `sanphamnam` (
  `MaSP` int(11) NOT NULL,
  `TenSanPham` varchar(200) NOT NULL,
  `Gia` decimal(18,2) NOT NULL,
  `MaChiTiet` int(11) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `GioiTinh` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanphamnam`
--

INSERT INTO `sanphamnam` (`MaSP`, `TenSanPham`, `Gia`, `MaChiTiet`, `MoTa`, `GioiTinh`) VALUES
(20, 'ÁO SƠ MI NAM', 299000.00, 25, 'Áo thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu: 100% cotton Form: Slim Kiểu dáng: Dài tay', 'nam'),
(21, 'Áo sơ mi nam hoạ tiết tay ngắn', 299000.00, 25, 'Áo thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio\r\n\r\nChất liệu: 100% cotton\r\n\r\nForm: Slim\r\n\r\nKiểu dáng: Ngắn tay', 'nam'),
(22, 'Áo sơ mi nam vải trơn màu có túi ngực trái form Casual Fit kiểu dáng ngắn tay ', 419000.00, 25, 'Áo sơ mi nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu:100% Ogarnic', 'nam'),
(23, 'Áo sơ mi nam vải hoạ tiết form Casual Fit kiểu dáng ngắn tay', 549000.00, 25, 'Áo sơ mi nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu:100% Lyocell', 'nam'),
(24, 'Áo Polo nam vải trơn màu hoạ tiết logo thời trang Pantio kiểu dáng :ngắn tay -', 299000.00, 26, 'Áo Polo nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu:49% cotton 47% Polyester 4% Spandex', 'nam'),
(25, ' Áo Polo nam vải trơn màu form Regular Fit kiểu dáng ngắn tay', 499000.00, 26, 'Áo Polo nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu:63.5% Polyester 33.5% Cotton 3% Silk', 'nam'),
(26, 'Áo Polo nam vải trơn màu hoạ tiết kẻ cổ áo form slim fit kiểu dáng ngắn tay', 499000.00, 26, 'Áo Polo nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio. Chất liệu:46% Polyester 45% cotton 4.8% silk 4.2% Spandex', 'nam'),
(27, 'Áo Polo thể thao nam vải trơn màu form Sport kiểu dáng ngắn tay', 494000.00, 26, 'Áo Polo nam là thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu:87% Nylon 13% Spandex', 'nam'),
(30, 'Quần dài nam', 369000.00, 27, 'Quần thiết kế độc quyền bởi thương hiệu thời trang cao cấp Pantio Chất liệu: Polyester/Rayon 75/25 Kiểu dáng: Slim, không ly', 'nam'),
(31, 'Áo sơ mi công sở vải chiffon xốp dáng suông cổ xẻ V tay chờm có đệm vai', 789000.00, 12, 'Mẫu áo sơ mi là thiết kế độc quyền của Pantio. mang đến cho nàng sự chỉn chu chuyên nghịệp mà không mất đi nét nữ tính, thời thượng. Làm nàng nổi bật nét đẹp, ấn tượng giữa đám đông. Chất liệu:Chiffon xốp - 98% polyester - 2 % spandex', 'nu'),
(32, 'Áo sơ mi công sở vải thô lụa dáng suông cổ đức tay măng sec ngực áo phối viền thời trang', 998000.00, 12, 'Mẫu áo sơ mi là thiết kế độc quyền của Pantio. mang đến cho nàng sự chỉn chu chuyên nghịệp mà không mất đi nét nữ tính, thời thượng. Làm nàng nổi bật nét đẹp, ấn tượng giữa đám đông. Chất liệu:Thô lụa - 100% polyester', 'nu'),
(33, 'Áo sơ mi công sở vải thô hoa dáng lửng cổ xẻ V tay áo bo chun gấu phối vạt thắt tạo kiểu', 789000.00, 12, 'Mẫu áo sơ mi là thiết kế độc quyền của Pantio. mang đến cho nàng sự chỉn chu chuyên nghịệp mà không mất đi nét nữ tính, thời thượng. Làm nàng nổi bật nét đẹp, ấn tượng giữa đám đông. Chất liệu:Thô boi - 100% cotton', 'nu'),
(34, 'Áo thun vải len cao cấp dáng ôm cổ tròn thân áo hoạ tiết nơ ruy băng thời trang', 598000.00, 12, 'Mẫu áo thun là thiết kế độc quyền của Pantio. mang đến cho nàng sự chỉn chu chuyên nghịệp mà không mất đi nét nữ tính, thời thượng. Làm nàng nổi bật nét đẹp, ấn tượng giữa đám đông. Chất liệu:Vải len - 96% Cotton 4% Spandex', 'nu'),
(35, 'Áo khoác vest vải tuyp xy dáng suông cổ hai ve thân áo có túi hoạ tiết đường kẻ tạo kiểu', 944000.00, 13, 'Mẫu áo vest là thiết kế độc quyền của Pantio. mang đến cho nàng sự chỉn chu chuyên nghịệp mà không mất đi nét nữ tính, thời thượng. Làm nàng nổi bật nét đẹp, ấn tượng giữa đám đông. Chất liệu:Tuyp xy - 90% polyester 10% rayon', 'nu'),
(36, 'Đầm công sở vải kaki dáng suông cổ đức thân váy phối ly bung tạo xoè kèm dây đai thắt eo cài khuy thân trước', 1998000.00, 18, 'Đầm công sở đơn giản, dễ mặc, dễ phối giúp nàng thoải mái hoạt động đặc biệt kiểu đầm này không hề kén người mặc lại vô cùng phóng khoáng để ứng dụng trong nhiều hoàn cảnh khác nhau. Chất liệu:vải kaki - 82% Tencel 18% Cotton', 'nu'),
(37, 'Đầm dạo phố vải linen dáng suông cổ đức xẻ V sâu kèm áo dây mặc trong', 1698000.00, 19, 'Đầm dạo phố đơn giản, dễ mặc, dễ phối giúp nàng thoải mái hoạt động đặc biệt kiểu đầm này không hề kén người mặc lại vô cùng phóng khoáng để ứng dụng trong nhiều hoàn cảnh khác nhau. Chất liệu:Linen - 55% Linen 45% Rayon', 'nu'),
(38, 'Đầm dạ hội cao cấp vải thô dày dáng A cổ tròn đính đá thời trang vai phối vải lưới tay bồng nhẹ thân váy hoạ tiết đường gân', 740000.00, 20, 'Đầm dự tiệc tôn dáng tạo lên vẻ đẹp quyến rũ toát lên vẻ đẹp hiện đại và trẻ trung. phù hợp cho những chị em yêu thích sự đơn giản nhưng vẫn muốn nổi bật giữa đám đông. Chất liệu:Thô dày - 95% Polyester 5% Spandex', 'nu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanphamnu`
--

CREATE TABLE `sanphamnu` (
  `MaSP` int(11) NOT NULL,
  `TenSanPham` varchar(200) NOT NULL,
  `Gia` decimal(18,2) NOT NULL,
  `MaChiTiet` int(11) NOT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham_khuyenmai`
--

CREATE TABLE `sanpham_khuyenmai` (
  `ID` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `MaKM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham_khuyenmai`
--

INSERT INTO `sanpham_khuyenmai` (`ID`, `MaSP`, `MaKM`) VALUES
(59, 21, 2),
(62, 31, 7),
(63, 38, 7),
(65, 22, 7),
(83, 20, 8),
(85, 23, 8);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`MaBL`,`MaSP`,`MaKH`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaCT`),
  ADD KEY `MaDonHang` (`MaDonHang`);

--
-- Chỉ mục cho bảng `chitietloainam`
--
ALTER TABLE `chitietloainam`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `MaLoai` (`MaLoai`);

--
-- Chỉ mục cho bảng `chitietloainu`
--
ALTER TABLE `chitietloainu`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `MaLoai` (`MaLoai`);

--
-- Chỉ mục cho bảng `chitietsanphamnam`
--
ALTER TABLE `chitietsanphamnam`
  ADD KEY `KichCo` (`MaSize`,`MaMau`),
  ADD KEY `MaSize` (`MaSize`,`MaMau`),
  ADD KEY `MaMau` (`MaMau`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `chitietsanphamnu`
--
ALTER TABLE `chitietsanphamnu`
  ADD KEY `MaSP` (`MaSP`,`MaSize`,`MaMau`),
  ADD KEY `MaSize` (`MaSize`),
  ADD KEY `MaMau` (`MaMau`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDonHang`);

--
-- Chỉ mục cho bảng `giaohang`
--
ALTER TABLE `giaohang`
  ADD PRIMARY KEY (`MaGiaoHang`),
  ADD KEY `MaDonHang` (`MaDonHang`);

--
-- Chỉ mục cho bảng `giohang_tam`
--
ALTER TABLE `giohang_tam`
  ADD PRIMARY KEY (`MaKH`,`MaSP`,`MaMau`,`MaSize`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`MaHD`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `MaNV` (`MaNV`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`MaKM`),
  ADD KEY `MaKM` (`MaKM`);

--
-- Chỉ mục cho bảng `kichco`
--
ALTER TABLE `kichco`
  ADD PRIMARY KEY (`MaSize`);

--
-- Chỉ mục cho bảng `lienhe`
--
ALTER TABLE `lienhe`
  ADD PRIMARY KEY (`MaLH`);

--
-- Chỉ mục cho bảng `loainam`
--
ALTER TABLE `loainam`
  ADD PRIMARY KEY (`MaLoai`);

--
-- Chỉ mục cho bảng `loainu`
--
ALTER TABLE `loainu`
  ADD PRIMARY KEY (`MaLoai`);

--
-- Chỉ mục cho bảng `mau`
--
ALTER TABLE `mau`
  ADD PRIMARY KEY (`MaMau`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNV`),
  ADD KEY `Quyen` (`Quyen`);

--
-- Chỉ mục cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPN`),
  ADD KEY `MaNV` (`MaNV`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD PRIMARY KEY (`MaPX`),
  ADD KEY `MaNV` (`MaNV`),
  ADD KEY `MauSP` (`MaSP`),
  ADD KEY `Mau` (`Mau`),
  ADD KEY `Size` (`Size`);

--
-- Chỉ mục cho bảng `quyen`
--
ALTER TABLE `quyen`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sanphamnam`
--
ALTER TABLE `sanphamnam`
  ADD PRIMARY KEY (`MaSP`),
  ADD KEY `MaChiTiet` (`MaChiTiet`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `sanphamnu`
--
ALTER TABLE `sanphamnu`
  ADD PRIMARY KEY (`MaSP`),
  ADD KEY `MaChiTiet` (`MaChiTiet`);

--
-- Chỉ mục cho bảng `sanpham_khuyenmai`
--
ALTER TABLE `sanpham_khuyenmai`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `MaSP` (`MaSP`),
  ADD KEY `MaKM` (`MaKM`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `MaBL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `MaCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT cho bảng `chitietloainam`
--
ALTER TABLE `chitietloainam`
  MODIFY `MaChiTiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `MaDonHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `giaohang`
--
ALTER TABLE `giaohang`
  MODIFY `MaGiaoHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `MaHD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `MaKM` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `lienhe`
--
ALTER TABLE `lienhe`
  MODIFY `MaLH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `loainam`
--
ALTER TABLE `loainam`
  MODIFY `MaLoai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `MaNV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  MODIFY `MaPN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  MODIFY `MaPX` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `sanphamnam`
--
ALTER TABLE `sanphamnam`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT cho bảng `sanphamnu`
--
ALTER TABLE `sanphamnu`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `sanpham_khuyenmai`
--
ALTER TABLE `sanpham_khuyenmai`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`);

--
-- Các ràng buộc cho bảng `chitietloainam`
--
ALTER TABLE `chitietloainam`
  ADD CONSTRAINT `chitietloainam_ibfk_1` FOREIGN KEY (`MaLoai`) REFERENCES `loainam` (`MaLoai`);

--
-- Các ràng buộc cho bảng `chitietloainu`
--
ALTER TABLE `chitietloainu`
  ADD CONSTRAINT `chitietloainu_ibfk_1` FOREIGN KEY (`MaLoai`) REFERENCES `loainu` (`MaLoai`);

--
-- Các ràng buộc cho bảng `chitietsanphamnam`
--
ALTER TABLE `chitietsanphamnam`
  ADD CONSTRAINT `chitietsanphamnam_ibfk_2` FOREIGN KEY (`MaSize`) REFERENCES `kichco` (`MaSize`),
  ADD CONSTRAINT `chitietsanphamnam_ibfk_3` FOREIGN KEY (`MaMau`) REFERENCES `mau` (`MaMau`),
  ADD CONSTRAINT `chitietsanphamnam_ibfk_4` FOREIGN KEY (`MaSP`) REFERENCES `sanphamnam` (`MaSP`);

--
-- Các ràng buộc cho bảng `chitietsanphamnu`
--
ALTER TABLE `chitietsanphamnu`
  ADD CONSTRAINT `chitietsanphamnu_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanphamnu` (`MaSP`),
  ADD CONSTRAINT `chitietsanphamnu_ibfk_2` FOREIGN KEY (`MaSize`) REFERENCES `kichco` (`MaSize`),
  ADD CONSTRAINT `chitietsanphamnu_ibfk_3` FOREIGN KEY (`MaMau`) REFERENCES `mau` (`MaMau`);

--
-- Các ràng buộc cho bảng `giaohang`
--
ALTER TABLE `giaohang`
  ADD CONSTRAINT `giaohang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`);

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`Quyen`) REFERENCES `quyen` (`id`);

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanphamnam` (`MaSP`);

--
-- Các ràng buộc cho bảng `sanphamnam`
--
ALTER TABLE `sanphamnam`
  ADD CONSTRAINT `sanphamnam_ibfk_1` FOREIGN KEY (`MaChiTiet`) REFERENCES `chitietloainam` (`MaChiTiet`);

--
-- Các ràng buộc cho bảng `sanphamnu`
--
ALTER TABLE `sanphamnu`
  ADD CONSTRAINT `sanphamnu_ibfk_1` FOREIGN KEY (`MaChiTiet`) REFERENCES `chitietloainu` (`MaChiTiet`);

--
-- Các ràng buộc cho bảng `sanpham_khuyenmai`
--
ALTER TABLE `sanpham_khuyenmai`
  ADD CONSTRAINT `sanpham_khuyenmai_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanphamnam` (`MaSP`),
  ADD CONSTRAINT `sanpham_khuyenmai_ibfk_2` FOREIGN KEY (`MaKM`) REFERENCES `khuyenmai` (`MaKM`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
