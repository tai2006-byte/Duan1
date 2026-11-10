-- Chạy một lần trên database cũ. Các đơn hủy trước đây chưa có lý do giữ NULL.
ALTER TABLE hoadon ADD COLUMN ly_do_huy VARCHAR(1000) NULL DEFAULT NULL;
