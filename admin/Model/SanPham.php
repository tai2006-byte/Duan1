<?php 
include_once("pdo.php");

class SanPham {

    // Danh sách sản phẩm, bao gồm sản phẩm đã ẩn để có thể khôi phục.
    public function getAll() {
        $sql = "SELECT sp.*, dm.name_danh_muc
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
                ORDER BY sp.id_sp DESC";
        return pdo_query($sql);
    }

    // 2. Lấy 1 sản phẩm chi tiết để sửa
    public function getOne($id) {
        $sql = "SELECT * FROM san_pham WHERE id_sp = ?";
        return pdo_query_one($sql, $id);
    }

    // 3. Thêm mới sản phẩm
    public function insert($ten, $gia, $anh, $moTa, $idDanhMuc, $soLuong, $loai) {
        // SỬA: Mặc định thêm vào là trang_thai = 1 (Hiện)
        $sql = "INSERT INTO san_pham (
                    ten_sp, 
                    gia_sp, 
                    hinh_anh, 
                    Mo_ta, 
                    id_danh_muc, 
                    so_luong, 
                    loai, 
                    trang_thai,
                    luot_xem
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0)"; 
        
        pdo_execute($sql, $ten, $gia, $anh, $moTa, $idDanhMuc, $soLuong, $loai);
    }

    // 4. Cập nhật sản phẩm
    public function update($id, $ten, $gia, $anh, $moTa, $idDanhMuc, $soLuong, $loai) {
        if($anh != "") {
            // Có thay ảnh
            $sql = "UPDATE san_pham SET 
                    ten_sp = ?, 
                    gia_sp = ?, 
                    hinh_anh = ?, 
                    Mo_ta = ?, 
                    id_danh_muc = ?, 
                    so_luong = ?, 
                    loai = ? 
                    WHERE id_sp = ?";
            pdo_execute($sql, $ten, $gia, $anh, $moTa, $idDanhMuc, $soLuong, $loai, $id);
        } else {
            // Không thay ảnh
            $sql = "UPDATE san_pham SET 
                    ten_sp = ?, 
                    gia_sp = ?, 
                    Mo_ta = ?, 
                    id_danh_muc = ?, 
                    so_luong = ?, 
                    loai = ? 
                    WHERE id_sp = ?";
            pdo_execute($sql, $ten, $gia, $moTa, $idDanhMuc, $soLuong, $loai, $id);
        }
    }

    // 5. Xóa mềm (SỬA: Set trang_thai = 0 để ẩn đi)
    public function delete($id) {
        $sql = "UPDATE san_pham SET trang_thai = 0 WHERE id_sp = ?";
        pdo_execute($sql, $id);
    }    
    
    // 6. Khôi phục sản phẩm (SỬA: Set trang_thai = 1 để hiện lại)
    public function restore($id) {
        $sql = "UPDATE san_pham SET trang_thai = 1 WHERE id_sp = ?";
        pdo_execute($sql, $id);
    }
}
?>
