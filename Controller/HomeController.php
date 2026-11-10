<?php
// Nhúng file Model để lấy dữ liệu từ Database
// Nhớ kiểm tra kỹ tên thư mục là "Model" hay "models" trên máy bạn nhé
include_once "Model/UserSanpham.php"; 

class HomeController {
    // Khai báo biến để chứa đối tượng Model
    private $userSanpham;

    // Hàm khởi tạo: Chạy đầu tiên khi class được gọi
    public function __construct() {
        // Tạo mới đối tượng UserSanpham để dùng các hàm lấy dữ liệu
        $this->userSanpham = new UserSanpham();
    }

    // ------------------------------------------------------------------
    // TRANG CHỦ (HOME)
    // ------------------------------------------------------------------
    public function home() {
        // 1. Lấy danh sách sản phẩm MỚI NHẤT từ database
        $newProducts = $this->userSanpham->getNewArrivals();
        
        // 2. Lấy danh sách sản phẩm BÁN CHẠY từ database
        $bestSellerProducts = $this->userSanpham->getBestSellers();
        
        // 3. Gọi file giao diện trang chủ để hiển thị dữ liệu
        include "views/home.php";
    }

    // ------------------------------------------------------------------
    // TRANG CỬA HÀNG (SHOP) - CÓ CHỨC NĂNG LỌC VÀ TÌM KIẾM
    // ------------------------------------------------------------------
    public function shop() {
        // Bước 1: Lấy danh sách TẤT CẢ danh mục để hiện ở cột bên trái (Sidebar)
        $categories = $this->userSanpham->getAllCategories();

        // Bước 2: Chuẩn bị các biến để lọc dữ liệu
        $categoryId = 0;  // Mặc định là 0 (Lấy tất cả danh mục)
        $keyword = "";    // Mặc định rỗng (Không tìm theo tên)
        $minPrice = 0;    // Giá thấp nhất mặc định
        $maxPrice = 0;    // Giá cao nhất mặc định

        // Bước 3: Kiểm tra xem người dùng có chọn lọc theo DANH MỤC không?
        // (Kiểm tra trên thanh địa chỉ có ?iddm=... không)
        if (isset($_GET['iddm'])) {
            $categoryId = filter_var($_GET['iddm'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1]
            ]) ?: 0;
        }

        // Bước 4: Kiểm tra xem người dùng có nhập TỪ KHÓA tìm kiếm không?
        // (Kiểm tra trên thanh địa chỉ có ?keyword=... không)
        if (isset($_GET['keyword']) && is_string($_GET['keyword'])) {
            $keyword = $_GET['keyword']; // Lấy từ khóa người dùng nhập
        }
        
        // Bước 5: Kiểm tra xem người dùng có lọc theo KHOẢNG GIÁ không?
        // (Kiểm tra trên thanh địa chỉ có ?price=... không)
        // Ví dụ: price=100000-200000
        if (isset($_GET['price']) && is_string($_GET['price'])) {
            // Lấy chuỗi giá từ URL
            $chuoiGia = $_GET['price'];
            
            // Tách chuỗi thành mảng dựa vào dấu gạch ngang '-'
            // Ví dụ: "100000-200000" thành mảng [100000, 200000]
            $mangGia = explode('-', $chuoiGia); 
            
            // Kiểm tra xem tách ra có đủ 2 số không
            if (count($mangGia) == 2) {
                $minPrice = (int)$mangGia[0]; // Số đầu là giá thấp nhất
                $maxPrice = (int)$mangGia[1]; // Số sau là giá cao nhất
            }
        }

        // Bước 6: Gọi Model để lấy danh sách sản phẩm dựa trên các điều kiện lọc ở trên
        // Truyền tất cả các biến (danh mục, giá, từ khóa) vào hàm getAllShop
        // 1. Quy định mỗi trang hiện bao nhiêu sản phẩm (ví dụ 9 cái)
        $so_luong_1_trang = 9;

        // 2. Kiểm tra xem đang ở trang số mấy (Lấy từ URL, nếu ko có thì là trang 1)
        if (isset($_GET['page'])) {
            $trang_hien_tai = filter_var($_GET['page'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 2147483647]
            ]) ?: 1;
        } else {
            $trang_hien_tai = 1;
        }

        // 4. Gọi hàm đếm tổng số lượng sản phẩm (Hàm mới tạo ở Model bước trước)
        // Phải đếm trước thì mới biết chia ra được bao nhiêu trang
        $tong_so_san_pham = $this->userSanpham->getCountAllShop($categoryId, $minPrice, $maxPrice, $keyword);
        
        // 5. Tính tổng số trang (Làm tròn lên)
        $tong_so_trang = max(1, (int)ceil($tong_so_san_pham / $so_luong_1_trang));

        // Giới hạn theo kết quả đã lọc trước khi tính OFFSET.
        // Không có sản phẩm thì vẫn dùng trang 1, OFFSET 0.
        $trang_hien_tai = min($trang_hien_tai, $tong_so_trang);
        $vi_tri_bat_dau = ($trang_hien_tai - 1) * $so_luong_1_trang;

        // 6. Gọi hàm lấy sản phẩm theo trang (Hàm mới tạo ở Model bước trước)
        // Lưu ý: Biến kết quả vẫn đặt tên là $allProducts để bên View không bị lỗi
        $allProducts = $this->userSanpham->getAllShop_PhanTrang(
            $categoryId, 
            $minPrice, 
            $maxPrice, 
            $keyword, 
            $vi_tri_bat_dau, 
            $so_luong_1_trang
        );
        
        // Bước 7: Gọi file giao diện cửa hàng để hiển thị kết quả
        include "views/shop.php";
    }

    // ------------------------------------------------------------------
    // CÁC TRANG TĨNH KHÁC
    // ------------------------------------------------------------------
    
    // Trang giới thiệu
    public function about() { 
        include "views/about.php"; 
    }
    
    // Trang liên hệ
    public function contact() { 
        include "views/contact.php"; 
    }
}
?>
