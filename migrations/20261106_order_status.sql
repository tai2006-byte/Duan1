-- Chuẩn hóa trạng thái cũ; không cập nhật số lượng hoặc cờ da_tru_kho.
-- Có thể chạy lại an toàn.
START TRANSACTION;
UPDATE hoadon SET trang_thai = 'Chờ xác nhận' WHERE trang_thai = 'Mới';
UPDATE hoadon SET trang_thai = 'Đã giao' WHERE trang_thai = 'Hoàn tất';
COMMIT;
