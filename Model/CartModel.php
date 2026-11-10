<?php
// Nhúng file kết nối CSDL vào để sử dụng các hàm pdo_execute, pdo_query...
include_once "Model/pdo.php";

class CartModel {
    private $connection;

    public function __construct(?PDO $connection = null) {
        $this->connection = $connection;
    }

    // Một kết nối và một transaction cho cả hóa đơn lẫn chi tiết.
    public function createOrderWithDetails($ho_ten, $sdt, $dia_chi, $tong_tien, $pttt, array $items) {
        if (empty($items)) {
            throw new InvalidArgumentException('Không thể lưu đơn hàng trống.');
        }
        $conn = $this->connection ?? pdo_get_connection();
        $conn->beginTransaction();
        try {
            // Cộng gộp theo ID sản phẩm để kiểm tra tồn kho.
            $quantities = [];
            foreach ($items as $item) {
                $id = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                $quantity = filter_var($item['so_luong'] ?? null, FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
                if ($id === false || $quantity === false || ($quantities[$id] ?? 0) > 2147483647 - $quantity) {
                    throw new DomainException('Số lượng sản phẩm không hợp lệ.');
                }
                $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
            }
            ksort($quantities, SORT_NUMERIC);
            $stock = $conn->prepare('UPDATE san_pham SET so_luong = so_luong - ? WHERE id_sp = ? AND trang_thai = 1 AND so_luong >= ?');
            foreach ($quantities as $id => $quantity) {
                // UPDATE có điều kiện và khóa dòng: hai khách không thể cùng mua vượt kho.
                $stock->execute([$quantity, $id, $quantity]);
                if ($stock->rowCount() !== 1) {
                    throw new DomainException('Sản phẩm đã ngừng bán hoặc không còn đủ số lượng. Vui lòng điều chỉnh giỏ hàng.');
                }
            }
            $id_hoadon = $this->createOrder($conn, $ho_ten, $sdt, $dia_chi, $tong_tien, $pttt);
            foreach ($items as $item) {
                $this->createOrderDetail($conn, $id_hoadon, $item['id'], $item['gia'],
                    $item['so_luong']);
            }
            $conn->commit();
            return $id_hoadon;
        } catch (Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    // ------------------------------------------------------------------
    // 1. HÀM TẠO HÓA ĐƠN MỚI (Lưu thông tin người mua vào bảng hoadon)
    // ------------------------------------------------------------------
    private function createOrder(PDO $conn, $ho_ten, $sdt, $dia_chi, $tong_tien, $pttt) {
        // Chuẩn bị câu lệnh SQL để thêm mới một dòng vào bảng 'hoadon'
        // Các dấu ? là nơi sẽ điền dữ liệu vào sau này để tránh bị hack SQL Injection
        // NOW() là hàm của SQL lấy thời gian hiện tại
        $sql = "INSERT INTO hoadon (
                    id_khachhang, 
                    ho_ten, 
                    sdt, 
                    dia_chi, 
                    tongtien, 
                    hinh_thuc_thanh_toan, 
                    ngay_dat, 
                    trang_thai,
                    da_tru_kho
                ) VALUES (
                    0,  -- ID khách hàng = 0 vì đây là khách vãng lai (không đăng nhập)
                    ?,  -- Họ tên người nhận
                    ?,  -- Số điện thoại
                    ?,  -- Địa chỉ giao hàng
                    ?,  -- Tổng tiền đơn hàng
                    ?,  -- Phương thức thanh toán
                    NOW(), -- Ngày giờ đặt hàng (lấy giờ hiện tại của server)
                    'Chờ xác nhận', -- Trạng thái mặc định của đơn hàng mới
                    1 -- Kho đã trừ trong cùng transaction
                )";
        
        // Thực thi câu lệnh SQL và lấy về ID của hóa đơn vừa tạo
        // (Ví dụ: Hóa đơn số 105) -> Cần ID này để lưu chi tiết sản phẩm ở bước sau
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ho_ten, $sdt, $dia_chi, $tong_tien, $pttt]);
        return $conn->lastInsertId();
    }

    // ------------------------------------------------------------------
    // 2. HÀM LƯU CHI TIẾT SẢN PHẨM (Lưu từng món hàng vào bảng chi_tiet_hoa_don)
    // ------------------------------------------------------------------
    private function createOrderDetail(PDO $conn, $id_hoadon, $id_sanpham, $gia, $so_luong) {
        // Câu lệnh SQL để thêm sản phẩm vào chi tiết hóa đơn
        $sql = "INSERT INTO chi_tiet_hoa_don (id_hoadon, id_sanpham, gia, so_luong)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_hoadon, $id_sanpham, $gia, $so_luong]);
    }

    // ------------------------------------------------------------------
    // 3. HÀM TRA CỨU ĐƠN HÀNG THEO SỐ ĐIỆN THOẠI
    // ------------------------------------------------------------------
    public function getOrdersByPhone($sdt) {
        // Câu lệnh SQL: Chọn tất cả từ bảng hoadon
        // Điều kiện WHERE: Số điện thoại phải bằng số người dùng nhập
        // ORDER BY id_hoadon DESC: Sắp xếp đơn mới nhất lên đầu
        $sql = "SELECT * FROM hoadon WHERE sdt = ? ORDER BY id_hoadon DESC";
        
        // Thực thi và trả về danh sách các hóa đơn tìm được (dạng mảng)
        return pdo_query($sql, $sdt);
    }

    // ------------------------------------------------------------------
    // 4. HÀM LẤY DANH SÁCH SẢN PHẨM CỦA MỘT ĐƠN HÀNG CỤ THỂ
    // ------------------------------------------------------------------
    public function getBillDetail($id_hoadon) {
        // Câu lệnh này nối 2 bảng lại với nhau:
        // 1. Bảng 'chi_tiet_hoa_don' (ct): Để lấy số lượng và giá
        // 2. Bảng 'san_pham' (sp): Để lấy Tên sản phẩm và Ảnh sản phẩm
        $sql = "SELECT 
                    ct.*,        -- Lấy hết thông tin trong bảng chi tiết
                    sp.ten_sp,   -- Lấy thêm tên sản phẩm
                    sp.hinh_anh  -- Lấy thêm ảnh sản phẩm
                FROM chi_tiet_hoa_don ct 
                JOIN san_pham sp ON ct.id_sanpham = sp.id_sp 
                WHERE ct.id_hoadon = ?"; // Điều kiện: Lấy theo ID hóa đơn
        
        // Trả về danh sách các sản phẩm trong đơn hàng đó
        return pdo_query($sql, $id_hoadon);
    }
    
    // ------------------------------------------------------------------
    // 5. HÀM LẤY THÔNG TIN CỦA 1 HÓA ĐƠN (Để xem người nhận, địa chỉ...)
    // ------------------------------------------------------------------
    public function getOneBill($id_hoadon) {
        // Lấy duy nhất 1 dòng từ bảng hóa đơn theo ID
        $sql = "SELECT * FROM hoadon WHERE id_hoadon = ?";
        
        // pdo_query_one: Trả về 1 dòng dữ liệu (không phải mảng danh sách)
        return pdo_query_one($sql, $id_hoadon);
    }
}
?>
