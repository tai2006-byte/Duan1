-- Chạy một lần trên database dochoi trước khi dùng chức năng tồn kho.
-- Đơn cũ mặc định 0 vì trước đây hệ thống chưa trừ kho.
ALTER TABLE hoadon ADD COLUMN da_tru_kho TINYINT NOT NULL DEFAULT 0;
