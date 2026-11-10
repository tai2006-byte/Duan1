<?php
class ThongKe {
    
    // 1. Hàm thống kê doanh thu (Chỉ lấy Hoàn thành)
    public function load_thongke_doanhthu($ngay_bat_dau, $ngay_ket_thuc) {
        $sql = "SELECT 
                    DATE(ngay_dat) as ngay, 
                    COUNT(id_hoadon) as so_luong_don, 
                    SUM(tongtien) as doanh_thu 
                FROM hoadon 
                WHERE trang_thai = 'Hoàn thành' 
                AND DATE(ngay_dat) >= ? 
                AND DATE(ngay_dat) <= ? 
                GROUP BY DATE(ngay_dat) 
                ORDER BY DATE(ngay_dat) ASC";
        return pdo_query($sql, $ngay_bat_dau, $ngay_ket_thuc);
    }

    // 2. Doanh thu lịch sử tính cả sản phẩm đã ẩn; số sản phẩm chỉ đếm đang bán.
    public function load_thongke_sanpham_danhmuc($ngay_bat_dau, $ngay_ket_thuc) {
        $sql = "SELECT 
                    danh_muc.id_danh_muc AS id, 
                    danh_muc.name_danh_muc AS name, 
                    
                    -- Đếm số lượng sản phẩm (Dùng DISTINCT để không đếm trùng)
                    COUNT(DISTINCT CASE WHEN san_pham.trang_thai = 1 THEN san_pham.id_sp END) AS countSp, 
                    
                    -- TÍNH TỔNG TIỀN TRỰC TIẾP (Dùng hàm SUM kết hợp điều kiện)
                    -- Logic: Nếu đơn Hoàn thành và đúng ngày thì cộng tiền, ngược lại thì cộng 0  
                    COALESCE(SUM(
                        CASE 
                            WHEN hoadon.trang_thai = 'Hoàn thành' 
                                AND DATE(hoadon.ngay_dat) >= ? 
                                AND DATE(hoadon.ngay_dat) <= ? 
                            THEN chi_tiet_hoa_don.so_luong * chi_tiet_hoa_don.gia 
                            ELSE 0 
                        END
                    ), 0) AS totalRevenue 

                FROM danh_muc 
                
                -- Giữ cả sản phẩm đã ẩn để không làm mất doanh thu lịch sử.
                LEFT JOIN san_pham 
                    ON danh_muc.id_danh_muc = san_pham.id_danh_muc 
                
                -- Nối bảng Chi tiết hóa đơn
                LEFT JOIN chi_tiet_hoa_don 
                    ON san_pham.id_sp = chi_tiet_hoa_don.id_sanpham 
                
                -- Nối bảng Hóa đơn (Để lấy ngày và trạng thái)
                LEFT JOIN hoadon 
                    ON chi_tiet_hoa_don.id_hoadon = hoadon.id_hoadon

                GROUP BY danh_muc.id_danh_muc, danh_muc.name_danh_muc 
                ORDER BY totalRevenue DESC";
        
        return pdo_query($sql, $ngay_bat_dau, $ngay_ket_thuc);
    }
    
    // 3. Hàm Doanh thu gần đây (Sidebar)
    public function load_doanhthu_ganday() {
         $sql = "SELECT 
                    DATE(ngay_dat) as ngay, 
                    SUM(tongtien) as doanh_thu 
                 FROM hoadon 
                 WHERE trang_thai = 'Hoàn thành' 
                 GROUP BY DATE(ngay_dat) 
                 ORDER BY DATE(ngay_dat) DESC 
                 LIMIT 5";
         return pdo_query($sql);
    }
}
?>
