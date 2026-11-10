<?php
include_once("Model/ThongKe.php");

class ThongKeController {
    public function index() {
        $thongKeModel = new ThongKe();

        // 1. Xử lý ngày tháng (Lọc)
        if (isset($_GET['date_from']) && isset($_GET['date_to'])) {
            $dateFrom = $_GET['date_from'];
            $dateTo = $_GET['date_to'];
        } else {
            $dateFrom = date('Y-m-d', strtotime('-30 days'));
            $dateTo = date('Y-m-d');
        }

        // 2. Lấy dữ liệu cho Biểu đồ cột
        $listDoanhThu = $thongKeModel->load_thongke_doanhthu($dateFrom, $dateTo);

        // 3. Lấy dữ liệu cho Bảng danh mục và Tổng sản phẩm
        $listDanhMuc = $thongKeModel->load_thongke_sanpham_danhmuc($dateFrom, $dateTo);
        
        // 4. Lấy dữ liệu Doanh thu gần đây (Sidebar)
        $listGanDay = $thongKeModel->load_doanhthu_ganday();

        // 5. Gọi view
        include "views/thongke/list.php";
    }
}
?>