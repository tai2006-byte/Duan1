<?php 
include_once("pdo.php");

class DanhMuc {
    // 1. Lấy TẤT CẢ danh mục (Bỏ điều kiện WHERE Trangthai = 1 đi)
    // Để danh mục đã xóa vẫn hiện ra trong list cho bạn khôi phục
    public function getAll() {
        $sql = "SELECT * FROM danh_muc ORDER BY id_danh_muc DESC";
        return pdo_query($sql);
    }

    // 2. Thêm mới (Mặc định Trangthai = 1)
    public function insert($ten) {
        $sql = "INSERT INTO danh_muc (name_danh_muc, Trangthai) VALUES (?, 1)";
        pdo_execute($sql, $ten);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM danh_muc WHERE id_danh_muc = ?";
        return pdo_query_one($sql, $id);
    }

    public function update($id, $ten) {
        $sql = "UPDATE danh_muc SET name_danh_muc = ? WHERE id_danh_muc = ?";
        pdo_execute($sql, $ten, $id);
    }

    // 3. Xóa mềm (Quan trọng): Chỉ sửa Trangthai thành 0 (Ẩn) chứ KHÔNG xóa khỏi DB
    public function delete($id) {
        $sql = "UPDATE danh_muc SET Trangthai = 0 WHERE id_danh_muc = ?";
        pdo_execute($sql, $id);
    }    

    // 4. Khôi phục: Sửa Trangthai thành 1 (Hiện)
    public function restore($id) {
        $sql = "UPDATE danh_muc SET Trangthai = 1 WHERE id_danh_muc = ?";
        pdo_execute($sql, $id);
    }
}
?>