-- Trạng thái trước khi đổi luồng 5 bước
START TRANSACTION;
UPDATE hoadon SET trang_thai='Chờ xác nhận' WHERE id_hoadon=1;
UPDATE hoadon SET trang_thai='Đang giao hàng' WHERE id_hoadon=2;
UPDATE hoadon SET trang_thai='Đã giao' WHERE id_hoadon=3;
UPDATE hoadon SET trang_thai='Đã hủy' WHERE id_hoadon=4;
UPDATE hoadon SET trang_thai='Chờ xác nhận' WHERE id_hoadon=5;
UPDATE hoadon SET trang_thai='Đang giao hàng' WHERE id_hoadon=6;
UPDATE hoadon SET trang_thai='Đã giao' WHERE id_hoadon=7;
UPDATE hoadon SET trang_thai='Đã giao' WHERE id_hoadon=8;
UPDATE hoadon SET trang_thai='Đang giao hàng' WHERE id_hoadon=9;
UPDATE hoadon SET trang_thai='Đang giao hàng' WHERE id_hoadon=10;
UPDATE hoadon SET trang_thai='Chờ xác nhận' WHERE id_hoadon=11;
UPDATE hoadon SET trang_thai='Chờ xác nhận' WHERE id_hoadon=12;
UPDATE hoadon SET trang_thai='Đã giao' WHERE id_hoadon=13;
UPDATE hoadon SET trang_thai='Đã giao' WHERE id_hoadon=14;
UPDATE hoadon SET trang_thai='Chờ xác nhận' WHERE id_hoadon=15;
COMMIT;
