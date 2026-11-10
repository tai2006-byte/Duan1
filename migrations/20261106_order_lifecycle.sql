-- Chạy một lần hoặc chạy lại an toàn. Không thay đổi tồn kho hay cờ da_tru_kho.
START TRANSACTION;
UPDATE hoadon SET trang_thai = 'Chờ xác nhận' WHERE trang_thai = 'Mới';
UPDATE hoadon SET trang_thai = 'Hoàn thành' WHERE trang_thai IN ('Đã giao', 'Hoàn tất');
UPDATE hoadon SET trang_thai = 'Hủy đơn' WHERE trang_thai = 'Đã hủy';
COMMIT;
