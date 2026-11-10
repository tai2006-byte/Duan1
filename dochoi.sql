-- Database dochoi
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `binh_luan` (
  `id_binhluan` int NOT NULL,
  `id_taikhoan` int DEFAULT NULL,
  `noi_dung` text COLLATE utf8mb4_general_ci,
  `ngay_tao` date DEFAULT NULL,
  PRIMARY KEY (`id_binhluan`),
  KEY `id_taikhoan` (`id_taikhoan`),
  CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chi_tiet_hoa_don` (
  `id_cthd` int NOT NULL AUTO_INCREMENT,
  `id_hoadon` int NOT NULL,
  `id_sanpham` int NOT NULL,
  `gia` decimal(10,2) DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id_cthd`),
  KEY `id_hoadon` (`id_hoadon`),
  KEY `id_sanpham` (`id_sanpham`),
  CONSTRAINT `chi_tiet_hoa_don_ibfk_1` FOREIGN KEY (`id_hoadon`) REFERENCES `hoadon` (`id_hoadon`) ON DELETE CASCADE,
  CONSTRAINT `chi_tiet_hoa_don_ibfk_2` FOREIGN KEY (`id_sanpham`) REFERENCES `san_pham` (`id_sp`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('1', '1', '1', '350000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('2', '2', '1', '350000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('3', '2', '2', '350000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('4', '3', '2', '150000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('5', '4', '1', '250000.00', '2');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('6', '5', '1', '300000.00', '4');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('7', '6', '2', '250000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('8', '7', '1', '300000.00', '3');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('9', '8', '2', '450000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('10', '9', '1', '300000.00', '2');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('11', '10', '1', '1500000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('12', '10', '2', '1500000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('13', '1', '15', '350000.00', '50');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('14', '2', '22', '280000.00', '45');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('15', '3', '8', '450000.00', '40');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('16', '1', '12', '150000.00', '38');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('17', '4', '5', '300000.00', '35');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('18', '5', '19', '250000.00', '30');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('19', '2', '30', '550000.00', '28');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('20', '6', '45', '180000.00', '25');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('21', '7', '3', '220000.00', '20');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('22', '8', '50', '400000.00', '18');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('24', '11', '88', '220000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('25', '12', '87', '280000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('26', '13', '93', '260000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('27', '13', '91', '270000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('28', '13', '93', '260000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('29', '14', '93', '260000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('30', '14', '92', '160000.00', '1');
INSERT INTO `chi_tiet_hoa_don` (`id_cthd`, `id_hoadon`, `id_sanpham`, `gia`, `so_luong`) VALUES ('31', '15', '91', '270000.00', '1');

CREATE TABLE `danh_muc` (
  `id_danh_muc` int NOT NULL AUTO_INCREMENT,
  `name_danh_muc` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Trangthai` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('1', 'Quần Âu', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('2', 'Áo len', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('3', 'Áo thun', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('5', 'Áo sơ mi', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('8', 'Áo Polo', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('9', 'Áo khoác', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('10', 'Quần Jeans', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('11', 'Quần gió', '1');
INSERT INTO `danh_muc` (`id_danh_muc`, `name_danh_muc`, `Trangthai`) VALUES ('12', 'Quần Short', '1');

CREATE TABLE `gio_hang` (
  `id_giohang` int NOT NULL,
  `id_sp` int DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  `id_taikhoan` int DEFAULT NULL,
  PRIMARY KEY (`id_giohang`),
  KEY `id_sp` (`id_sp`),
  KEY `id_taikhoan` (`id_taikhoan`),
  CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`id_sp`) REFERENCES `san_pham` (`id_sp`),
  CONSTRAINT `gio_hang_ibfk_2` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `hoadon` (
  `id_hoadon` int NOT NULL AUTO_INCREMENT,
  `id_khachhang` int DEFAULT NULL,
  `hinh_thuc_thanh_toan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `trang_thai` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tongtien` decimal(10,2) DEFAULT NULL,
  `ngay_dat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ho_ten` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `da_tru_kho` tinyint NOT NULL DEFAULT '0',
  `ly_do_huy` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_hoadon`),
  KEY `id_khachhang` (`id_khachhang`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('1', '1', 'Tiền mặt (COD)', 'Chờ xác nhận', '350000.00', '2025-12-03 22:49:07', 'Nguyễn Văn An', '123 Cầu Giấy, Hà Nội', '0901234567', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('2', '2', 'Chuyển khoản', 'Đang giao hàng', '700000.00', '2025-12-03 22:49:07', 'Trần Thị Bích', '45 Lê Lợi, Q1, HCM', '0912345678', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('3', '1', 'Tiền mặt (COD)', 'Hoàn thành', '150000.00', '2025-12-03 22:49:07', 'Lê Văn Cường', 'Số 5 Ngõ 10, Đống Đa, HN', '0987654321', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('4', '3', 'Chuyển khoản', 'Hủy đơn', '500000.00', '2025-12-03 22:49:07', 'Phạm Thu Hương', '88 Trần Hưng Đạo, Đà Nẵng', '0977889900', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('5', '4', 'Tiền mặt (COD)', 'Chờ xác nhận', '1200000.00', '2025-12-03 22:49:07', 'Hoàng Văn Dũng', 'Khu CN VSIP, Bình Dương', '0966554433', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('6', '2', 'Chuyển khoản', 'Đang giao hàng', '250000.00', '2025-12-03 22:49:07', 'Vũ Thị Lan', 'Chung cư EcoHome, Hà Nội', '0911223344', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('7', '5', 'Tiền mặt (COD)', 'Hoàn thành', '900000.00', '2025-12-03 22:49:07', 'Đỗ Minh Tuấn', 'Thành phố Thủ Đức, HCM', '0933445566', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('8', '1', 'Chuyển khoản', 'Hoàn thành', '450000.00', '2025-12-03 22:49:07', 'Ngô Kiến Huy', 'Phố Huế, Hai Bà Trưng, HN', '0944556677', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('9', '3', 'Tiền mặt (COD)', 'Đang giao hàng', '600000.00', '2025-12-03 22:49:07', 'Bùi Anh Tuấn', 'Đà Lạt, Lâm Đồng', '0955667788', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('10', '2', 'Chuyển khoản', 'Đang giao hàng', '3000000.00', '2025-12-03 22:49:07', 'Sơn Tùng MTP', 'Thái Bình', '0999888777', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('11', '0', 'Tiền mặt (COD)', 'Chờ xác nhận', '220000.00', '2025-12-03 22:56:59', 'Ôn Dực Uy 2', 'Số nhà 14 , Móng Cái , Quảng Ninh', '0776781111', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('12', '0', 'Tiền mặt (COD)', 'Chờ xác nhận', '280000.00', '2025-12-03 22:58:01', 'Ôn Dực Uy 3', 'Số nhà 14 , Móng Cái , Quảng Ninh', '0776781111', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('13', '0', 'Chuyển khoản', 'Hoàn thành', '790000.00', '2025-12-03 23:50:43', 'Ôn Dực Uy', 'Số nhà 14 , Móng Cái , Quảng Ninh', '0776781111', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('14', '0', 'Tiền mặt (COD)', 'Hoàn thành', '420000.00', '2025-12-04 13:28:52', 'Ôn Dực Uy', 'Số nhà 14 , Móng Cái , Quảng Ninh', '0776781111', '0', NULL);
INSERT INTO `hoadon` (`id_hoadon`, `id_khachhang`, `hinh_thuc_thanh_toan`, `trang_thai`, `tongtien`, `ngay_dat`, `ho_ten`, `dia_chi`, `sdt`, `da_tru_kho`, `ly_do_huy`) VALUES ('15', '0', 'Chuyển khoản', 'Hoàn thành', '270000.00', '2025-12-04 14:11:22', 'Bùi Gia Hưng', 'Số nhà 14 , Móng Cái , Quảng Ninh', '0123456', '0', NULL);

CREATE TABLE `khach_hang` (
  `id_khachhang` int NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hinh_anh` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_khachhang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `khach_hang` (`id_khachhang`, `ho_ten`, `ngay_sinh`, `email`, `sdt`, `dia_chi`, `hinh_anh`) VALUES ('1', 'Nguyễn Văn An', '1990-05-10', 'an@gmail.com', '0901234567', '123 Cầu Giấy, Hà Nội', 'user.png');
INSERT INTO `khach_hang` (`id_khachhang`, `ho_ten`, `ngay_sinh`, `email`, `sdt`, `dia_chi`, `hinh_anh`) VALUES ('2', 'Trần Thị Bích', '1995-08-20', 'bich@gmail.com', '0912345678', '45 Lê Lợi, Q1, HCM', 'user.png');
INSERT INTO `khach_hang` (`id_khachhang`, `ho_ten`, `ngay_sinh`, `email`, `sdt`, `dia_chi`, `hinh_anh`) VALUES ('3', 'Phạm Thu Hương', '1998-12-12', 'huong@gmail.com', '0977889900', '88 Trần Hưng Đạo, Đà Nẵng', 'user.png');
INSERT INTO `khach_hang` (`id_khachhang`, `ho_ten`, `ngay_sinh`, `email`, `sdt`, `dia_chi`, `hinh_anh`) VALUES ('4', 'Hoàng Văn Dũng', '1988-03-25', 'dung@gmail.com', '0966554433', 'Khu CN VSIP, Bình Dương', 'user.png');
INSERT INTO `khach_hang` (`id_khachhang`, `ho_ten`, `ngay_sinh`, `email`, `sdt`, `dia_chi`, `hinh_anh`) VALUES ('5', 'Đỗ Minh Tuấn', '2000-11-05', 'tuan@gmail.com', '0933445566', 'Thành phố Thủ Đức, HCM', 'user.png');

CREATE TABLE `kho_anh` (
  `id_hinhanh` int NOT NULL,
  `id_sp` int DEFAULT NULL,
  `ten_hinhanh` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_hinhanh`),
  KEY `id_sp` (`id_sp`),
  CONSTRAINT `kho_anh_ibfk_1` FOREIGN KEY (`id_sp`) REFERENCES `san_pham` (`id_sp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `san_pham` (
  `id_sp` int NOT NULL AUTO_INCREMENT,
  `hinh_anh` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ten_sp` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gia_sp` decimal(10,2) DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  `loai` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_danh_muc` int DEFAULT NULL,
  `Mo_ta` text COLLATE utf8mb4_general_ci NOT NULL,
  `trang_thai` tinyint NOT NULL DEFAULT '0',
  `luot_xem` int DEFAULT '0',
  PRIMARY KEY (`id_sp`),
  KEY `fk_sanpham_danhmuc` (`id_danh_muc`),
  CONSTRAINT `fk_sanpham_danhmuc` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc` (`id_danh_muc`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('1', 'anh1.png', 'Ao', '18000.00', '3', 'Dat', '1', 'Chat lieu cotton', '1', '0');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('2', '692d45a61f447_anhmaytinh6675.jpg', 'Áo khoác mùa đông', '3000000.00', '10', 'Hot', '2', 'Sản phẩm tốt cực kì', '1', '0');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('3', '692fddf562a6a_54034716996_f05587c93a_c_59eb19b7070844199078bad134ac2b18_master.jpg', 'Áo khoác gió', '720000.00', '1000', 'Hot', '9', 'Sản phẩm thiết kế bền bỉ đẹp', '0', '0');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('4', 'quan_au_01.jpg', 'Quần Âu Nam Slimfit', '350000.00', '100', 'Hot', '1', 'Chất liệu vải tuyết mưa cao cấp, không nhăn, giữ form chuẩn.', '1', '50');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('5', '692ffa36b5458_quan-tay-5.jpg', 'Quần Tây Công Sở Đen', '450000.00', '80', 'New', '1', 'Thiết kế sang trọng, lịch lãm, phù hợp môi trường công sở.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('6', 'quan_au_03.jpg', 'Quần Âu Hàn Quốc Ghi', '320000.00', '150', 'Sale', '1', 'Phong cách trẻ trung, ống côn vừa vặn, dễ phối đồ.', '1', '30');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('7', 'quan_au_04.jpg', 'Quần Âu Co Giãn Nhẹ', '380000.00', '90', 'Hot', '1', 'Vải co giãn 4 chiều, thoải mái vận động cả ngày dài.', '1', '45');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('8', '692ff989b395c_dang-quan-au-ong-com_ec64b335ed9c487b87dace1f62fe5fe5.jpg', 'Quần Tây Ống Đứng', '400000.00', '60', 'New', '1', 'Form ống đứng cổ điển, che khuyết điểm chân tốt.', '1', '10');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('9', 'quan_au_06.jpg', 'Quần Âu Chinos Be', '360000.00', '110', 'Sale', '1', 'Màu be thời thượng, chất vải dày dặn, đứng form.', '1', '75');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('10', 'quan_au_07.jpg', 'Quần Âu Kẻ Caro', '420000.00', '70', 'Hot', '1', 'Họa tiết kẻ caro tinh tế, tạo điểm nhấn nổi bật.', '1', '88');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('11', 'quan_au_08.jpg', 'Quần Tây Lưng Cao', '480000.00', '50', 'New', '1', 'Hack dáng cực đỉnh, giúp chân trông dài hơn.', '1', '92');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('12', '692ffa01994c1_mf6630_3bc47c85fbea47a8aa340e1a376f95bf.jpg', 'Quần Âu Xám Chuột', '340000.00', '130', 'Sale', '1', 'Màu xám nam tính, dễ dàng phối với sơ mi trắng.', '1', '20');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('13', 'quan_au_10.jpg', 'Quần Âu Vải Wool', '550000.00', '40', 'Hot', '1', 'Chất liệu pha len cao cấp, mềm mại và ấm áp.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('14', 'ao_len_01.jpg', 'Áo Len Cổ Lọ Basic', '250000.00', '200', 'Hot', '2', 'Giữ ấm cực tốt, thiết kế cổ lọ cổ điển không lỗi mốt.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('15', '692ff8cf4ae80_b48f4a02858b2dc8d6bd882c3c37a24d.jpg', 'Áo Len Thừng Vặn', '320000.00', '100', 'New', '2', 'Họa tiết vặn thừng dày dặn, phong cách vintage.', '1', '40');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('16', 'ao_len_03.jpg', 'Áo Len Cổ Tim Mỏng', '180000.00', '150', 'Sale', '2', 'Chất len mỏng nhẹ, phù hợp tiết trời se lạnh mùa thu.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('17', 'ao_len_04.jpg', 'Áo Len Oversize', '290000.00', '120', 'Hot', '2', 'Form rộng thoải mái, phong cách Hàn Quốc năng động.', '1', '110');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('18', 'ao_len_05.jpg', 'Áo Len Gile Trơn', '220000.00', '80', 'New', '2', 'Dễ dàng layer cùng áo sơ mi hoặc áo thun.', '1', '35');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('19', '692ffadfc0dcc_vn-11134207-7r98o-m09o66n5lfnz11.jpg', 'Áo Len Lông Cừu', '450000.00', '50', 'Sale', '2', 'Siêu mềm mịn, không gây ngứa, giữ nhiệt hiệu quả.', '1', '15');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('20', 'ao_len_07.jpg', 'Áo Len Cardigan', '350000.00', '90', 'Hot', '2', 'Khoác nhẹ nhàng, thanh lịch cho các chàng trai.', '1', '70');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('21', 'ao_len_08.jpg', 'Áo Len Kẻ Ngang', '270000.00', '110', 'New', '2', 'Họa tiết kẻ ngang trẻ trung, hack dáng người gầy.', '1', '55');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('22', '692ff93611538_sg-11134201-22100-dedn4sxg3vivf1.jpg', 'Áo Len Cổ Tròn Dày', '300000.00', '100', 'Sale', '2', 'Cổ tròn basic, bo tay và gấu áo gọn gàng.', '1', '25');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('23', 'ao_len_10.jpg', 'Áo Len Họa Tiết Bắc Âu', '380000.00', '60', 'Hot', '2', 'Họa tiết nổi bật, phù hợp cho dịp lễ hội cuối năm.', '1', '85');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('24', 'ao_thun_01.jpg', 'Áo Thun Trơn Cotton', '150000.00', '300', 'Hot', '3', '100% Cotton thấm hút mồ hôi, thoáng mát.', '1', '200');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('25', 'ao_thun_02.jpg', 'Áo Thun In Hình Gấu', '180000.00', '200', 'New', '3', 'Hình in sắc nét, công nghệ in decal chuyển nhiệt bền màu.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('26', 'ao_thun_03.jpg', 'Áo Thun Raglan', '160000.00', '180', 'Sale', '3', 'Phối màu tay áo độc đáo, phong cách thể thao.', '1', '80');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('27', 'ao_thun_04.jpg', 'Áo Thun Cổ V', '140000.00', '250', 'Hot', '3', 'Thiết kế cổ tim quyến rũ, khoe xương quai xanh.', '1', '95');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('28', 'ao_thun_05.jpg', 'Áo Thun Dài Tay', '190000.00', '120', 'New', '3', 'Thích hợp mặc lót hoặc mặc vào ngày trời mát.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('29', 'ao_thun_06.jpg', 'Áo Thun Unisex', '200000.00', '150', 'Sale', '3', 'Form rộng phù hợp cho cả nam và nữ.', '1', '110');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('30', '692ffb0a30afb_vn-11134207-7r98o-lmqaum1188gvc9.jpg', 'Áo Thun Kẻ Sọc', '170000.00', '200', 'Hot', '3', 'Kẻ sọc ngang nhỏ, tạo cảm giác đầy đặn hơn.', '1', '130');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('31', 'ao_thun_08.jpg', 'Áo Thun Wash Loang', '220000.00', '90', 'New', '3', 'Màu wash loang bụi bặm, cá tính đường phố.', '1', '45');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('32', 'ao_thun_09.jpg', 'Áo Thun Local Brand', '250000.00', '100', 'Sale', '3', 'Thiết kế độc quyền, chất liệu vải dày dặn.', '1', '180');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('33', 'ao_thun_10.jpg', 'Áo Thun Lạnh', '130000.00', '300', 'Hot', '3', 'Vải thun lạnh co giãn 4 chiều, mặc cực mát.', '1', '220');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('34', 'somi_01.jpg', 'Sơ Mi Trắng Oxford', '300000.00', '150', 'Hot', '5', 'Chuẩn soái ca, chất vải Oxford đứng form.', '1', '300');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('35', 'somi_02.jpg', 'Sơ Mi Kẻ Caro Đỏ', '280000.00', '120', 'New', '5', 'Năng động, trẻ trung, phù hợp đi học đi làm.', '1', '100');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('36', 'somi_03.jpg', 'Sơ Mi Lụa Mềm', '350000.00', '80', 'Sale', '5', 'Mềm mại, chống nhăn, thoáng mát.', '1', '50');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('37', 'somi_04.jpg', 'Sơ Mi Ngắn Tay Hoa', '250000.00', '200', 'Hot', '5', 'Họa tiết nhiệt đới, phù hợp đi biển, du lịch.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('38', 'somi_05.jpg', 'Sơ Mi Denim', '320000.00', '100', 'New', '5', 'Chất bò mềm, bụi bặm và nam tính.', '1', '80');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('39', 'somi_06.jpg', 'Sơ Mi Cổ Tàu', '290000.00', '110', 'Sale', '5', 'Thiết kế cổ trụ lạ mắt, phong cách vintage.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('40', 'somi_07.jpg', 'Sơ Mi Flanel', '310000.00', '90', 'Hot', '5', 'Vải dạ nỉ giữ ấm, họa tiết caro cổ điển.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('41', 'somi_08.jpg', 'Sơ Mi Đen Trơn', '270000.00', '130', 'New', '5', 'Huyền bí, sang trọng, dễ phối đồ.', '1', '200');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('42', 'somi_09.jpg', 'Sơ Mi Dài Tay Kẻ Sọc', '330000.00', '70', 'Sale', '5', 'Kẻ sọc dọc giúp người mặc trông cao hơn.', '1', '40');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('43', 'somi_10.jpg', 'Sơ Mi Linen', '380000.00', '60', 'Hot', '5', 'Vải đũi tự nhiên, thấm hút tốt, thân thiện da.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('44', 'polo_01.jpg', 'Polo Cá Sấu Classic', '280000.00', '200', 'Hot', '8', 'Vải thun cá sấu 4 chiều, co giãn tốt.', '1', '180');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('45', '692ffb50d80bc_vn-11134207-7qukw-lg0l844v0zjr3b.jpg', 'Polo Phối Cổ', '250000.00', '150', 'New', '8', 'Điểm nhấn phối màu ở cổ và tay áo.', '1', '100');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('46', 'polo_03.jpg', 'Polo Thể Thao', '220000.00', '180', 'Sale', '8', 'Chất liệu poly thái, khô thoáng nhanh.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('47', 'polo_04.jpg', 'Polo Họa Tiết Chìm', '300000.00', '90', 'Hot', '8', 'Họa tiết dệt chìm sang trọng, tinh tế.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('48', 'polo_05.jpg', 'Polo Dáng Suông', '260000.00', '130', 'New', '8', 'Form regular fit, không quá bó sát.', '1', '85');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('49', 'polo_06.jpg', 'Polo Zip Khóa Kéo', '320000.00', '70', 'Sale', '8', 'Thay cúc bằng khóa zip hiện đại, cá tính.', '1', '40');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('50', 'polo_07.jpg', 'Polo Len Dệt Kim', '350000.00', '50', 'Hot', '8', 'Chất len dệt mỏng, phong cách retro.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('51', 'polo_08.jpg', 'Polo Sọc Ngang To', '240000.00', '160', 'New', '8', 'Kẻ sọc bản to, trẻ trung năng động.', '1', '95');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('52', 'polo_09.jpg', 'Polo Màu Pastel', '270000.00', '110', 'Sale', '8', 'Màu sắc nhẹ nhàng, tôn da.', '1', '75');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('53', 'polo_10.jpg', 'Polo Logo Thêu', '290000.00', '100', 'Hot', '8', 'Logo thêu nổi bật ngực áo, đẳng cấp.', '1', '210');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('54', 'khoac_01.jpg', 'Áo Khoác Gió 2 Lớp', '450000.00', '100', 'Hot', '9', 'Chống nước nhẹ, cản gió tốt, thích hợp đi phượt.', '1', '250');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('55', 'khoac_02.jpg', 'Áo Bomber Bóng Chày', '550000.00', '80', 'New', '9', 'Phong cách học đường Mỹ, năng động.', '1', '180');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('56', 'khoac_03.jpg', 'Áo Khoác Jeans', '600000.00', '60', 'Sale', '9', 'Bụi bặm, bền bỉ với thời gian.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('57', '692fe95e859ff_54034716996_f05587c93a_c_59eb19b7070844199078bad134ac2b18_master.jpg', 'Áo Blazer Hàn Quốc', '750000.00', '40', 'Hot', '9', 'Lịch lãm như soái ca phim Hàn.', '1', '300');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('58', 'khoac_05.jpg', 'Áo Khoác Phao Béo', '800000.00', '50', 'New', '9', 'Siêu ấm áp cho những ngày đại hàn.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('59', 'khoac_06.jpg', 'Áo Khoác Kaki', '480000.00', '90', 'Sale', '9', 'Chất kaki dày dặn, form đứng, nhiều túi tiện lợi.', '1', '70');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('60', 'khoac_07.jpg', 'Áo Hoodie Zip', '350000.00', '150', 'Hot', '9', 'Nỉ bông ấm áp, có mũ trùm đầu và khóa kéo.', '1', '200');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('61', 'khoac_08.jpg', 'Áo Khoác Da PU', '650000.00', '45', 'New', '9', 'Da PU cao cấp, không nổ, phong cách bad boy.', '1', '160');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('62', 'khoac_09.jpg', 'Áo Cardigan Dày', '400000.00', '70', 'Sale', '9', 'Len dày dặn, giữ ấm tốt, phong cách nhẹ nhàng.', '1', '110');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('63', 'khoac_10.jpg', 'Áo Khoác Măng Tô', '900000.00', '30', 'Hot', '9', 'Dáng dài sang trọng, phù hợp người cao ráo.', '1', '140');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('64', 'jeans_01.jpg', 'Jeans Slimfit Xanh', '400000.00', '120', 'Hot', '10', 'Ôm vừa phải, tôn dáng chân.', '1', '190');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('65', 'jeans_02.jpg', 'Jeans Rách Gối', '450000.00', '100', 'New', '10', 'Cá tính, bụi bặm, phong cách đường phố.', '1', '220');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('66', 'jeans_03.jpg', 'Jeans Đen Trơn', '380000.00', '150', 'Sale', '10', 'Màu đen cơ bản, dễ phối với mọi loại áo.', '1', '300');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('67', 'jeans_04.jpg', 'Jeans Baggy', '420000.00', '80', 'Hot', '10', 'Ống rộng thoải mái, che khuyết điểm chân to.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('68', 'jeans_05.jpg', 'Jeans Ống Suông', '410000.00', '90', 'New', '10', 'Phong cách retro thập niên 90.', '1', '80');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('69', 'jeans_06.jpg', 'Jeans Xám Khói', '430000.00', '70', 'Sale', '10', 'Màu xám khói lạ mắt, thời thượng.', '1', '100');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('70', 'jeans_07.jpg', 'Jeans Short', '300000.00', '200', 'Hot', '10', 'Quần soóc bò năng động mùa hè.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('71', 'jeans_08.jpg', 'Jeans Mài Bạc', '460000.00', '60', 'New', '10', 'Wash màu bạc ấn tượng ở đùi.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('72', 'jeans_09.jpg', 'Jeans Skinny', '350000.00', '130', 'Sale', '10', 'Ôm sát chân, co giãn tốt.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('73', 'jeans_10.jpg', 'Jeans Jogger', '440000.00', '85', 'Hot', '10', 'Bo gấu năng động, lưng chun thoải mái.', '1', '110');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('74', 'gio_01.jpg', 'Quần Gió 3 Sọc', '200000.00', '150', 'Hot', '11', 'Thiết kế kinh điển, phù hợp chơi thể thao.', '1', '180');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('75', 'gio_02.jpg', 'Quần Gió Dù Nhăn', '250000.00', '100', 'New', '11', 'Chất liệu dù nhăn thời trang, không bám bụi.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('76', 'gio_03.jpg', 'Quần Gió Bo Gấu', '220000.00', '120', 'Sale', '11', 'Gọn gàng, dễ vận động.', '1', '70');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('77', 'gio_04.jpg', 'Quần Gió Ống Rộng', '240000.00', '80', 'Hot', '11', 'Phong cách hiphop, ống rộng thùng thình.', '1', '130');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('78', 'gio_05.jpg', 'Quần Gió Phối Màu', '260000.00', '90', 'New', '11', 'Phối mảng màu color block nổi bật.', '1', '50');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('79', 'gio_06.jpg', 'Quần Gió Lót Lưới', '280000.00', '70', 'Sale', '11', 'Bên trong lót lưới thoáng khí.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('80', 'gio_07.jpg', 'Quần Gió Túi Hộp', '300000.00', '60', 'Hot', '11', 'Nhiều túi hộp tiện lợi, phong cách Cargo.', '1', '110');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('81', 'gio_08.jpg', 'Quần Gió Chống Nước', '350000.00', '50', 'New', '11', 'Công nghệ trượt nước, đi mưa nhẹ thoải mái.', '1', '40');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('82', '693066f7e1597_vn-11134207-7ras8-m3bilssbjx0s33.jpg', 'Quần Gió Dây Rút', '210000.00', '140', 'Sale', '11', 'Dây rút eo và gấu quần điều chỉnh độ rộng.', '1', '80');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('83', '693066ac1c46d_0ed63db1b29ed9f300075822ca174eaa.jpg', 'Quần Gió Tập Gym', '190000.00', '160', 'Hot', '11', 'Mỏng nhẹ, thấm hút mồ hôi tốt.', '1', '100');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('84', '6930662eb5809_quan-short-kaki-tron-qs009-mau-den-15969-slide-products-6018cc4bdaf87.png', 'Short Kaki Trơn', '200000.00', '200', 'Hot', '12', 'Đơn giản, dễ mặc, nhiều màu sắc.', '1', '250');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('85', '693065f811da9_vn-11134207-7qukw-lf9exw90mgk74d.jpg', 'Short Nỉ Da Cá', '180000.00', '180', 'New', '12', 'Mềm mại, mặc nhà hay đi chơi đều ổn.', '1', '150');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('86', '692ffcf4189f0_vn-11134207-7r98o-lvsgqsmkmlbtcf.jpg', 'Short Gió Thể Thao', '150000.00', '250', 'Sale', '12', 'Mát mẻ, phù hợp đá bóng, chạy bộ.', '1', '200');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('87', '692ffcd3e7cbf_160_short_jean_3m-13_dc30b6d561134509aebe20b20f19c3f0_1024x1024.jpg', 'Short Jeans Rách', '280000.00', '100', 'Hot', '12', 'Bụi bặm, cá tính cho mùa hè.', '1', '120');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('88', '692ffc8fcc8dc_15412-clean-z2593506647181-19dc26a68de3e6d46f3498f0b0badb34.png', 'Short Họa Tiết', '220000.00', '120', 'New', '12', 'Họa tiết hoa lá đi biển rực rỡ.', '1', '90');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('89', '692ffc487ee1c_z4549879349049-4cc2cab1ca993de2767f4fcefb76b802-1690377158963.webp', 'Short Túi Hộp', '250000.00', '80', 'Sale', '12', 'Phong cách quân đội mạnh mẽ.', '1', '70');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('90', '692ffc13c21e9_vn-11134201-23030-8kiqwmh1ncova1.jpg', 'Short Đũi', '230000.00', '140', 'Hot', '12', 'Vải đũi nhẹ, thoáng, không bí mồ hôi.', '1', '130');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('91', '692ffbf3277ba_z4006056682005_78b390cbbb83e70a4e9250700d32fad1_374c61ebf3134eceb2884980824c9007_master.jpg', 'Short Tây', '270000.00', '90', 'New', '12', 'Lịch sự nhưng vẫn thoải mái.', '1', '60');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('92', '692ffbc7ea270_vn-11134201-23030-8kiqwmh1ncova1.jpg', 'Short Thun Lạnh', '160000.00', '220', 'Sale', '12', 'Mặc cực mát, co giãn tốt.', '1', '180');
INSERT INTO `san_pham` (`id_sp`, `hinh_anh`, `ten_sp`, `gia_sp`, `so_luong`, `loai`, `id_danh_muc`, `Mo_ta`, `trang_thai`, `luot_xem`) VALUES ('93', '692ff2d931595_dockers-khaki-chino-shorts-product-2-3210366-956989973.webp', 'Short Chinos', '260000.00', '110', 'Hot', '12', 'Chất vải Chinos dày dặn, đứng form.', '1', '100');

CREATE TABLE `taikhoan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `auth_version` int NOT NULL DEFAULT '0',
  `role` enum('admin','customer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `taikhoan_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `taikhoan` (`id`, `username`, `password`, `ho_ten`, `is_active`, `auth_version`, `role`) VALUES ('1', 'admin', '$2y$10$IBSk5u4WGqOVRgsWJv9D0u/ZZ41BsK1WEUQQpfCZl0XjQoonlWf3i', 'Quản Trị Viên', '1', '0', 'admin');

SET FOREIGN_KEY_CHECKS=1;
