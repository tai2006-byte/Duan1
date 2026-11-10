<?php
require_once "pdo.php"; // Gọi file kết nối DB của bạn
require_once __DIR__ . '/../../Support/OrderStatus.php';

class HoaDon {
    private $connection;

    public function __construct(?PDO $connection = null) {
        $this->connection = $connection;
    }
    // Lấy tất cả danh sách hóa đơn
    public function getAllOrders() {
        $sql = "SELECT * FROM hoadon ORDER BY id_hoadon DESC";
        return pdo_query($sql);
    }

    // Lấy thông tin 1 hóa đơn theo ID
    public function getOrderById($id) {
        $sql = "SELECT * FROM hoadon WHERE id_hoadon = ?";
        return pdo_query_one($sql, $id);
    }

    // Lấy chi tiết sản phẩm trong hóa đơn (Kèm tên SP và Ảnh)
    public function getOrderDetail($id_hoadon) {
        $sql = "SELECT 
                    chi_tiet_hoa_don.so_luong,
                    chi_tiet_hoa_don.gia,
                    san_pham.ten_sp,
                    san_pham.hinh_anh 
                FROM chi_tiet_hoa_don 
                JOIN san_pham ON chi_tiet_hoa_don.id_sanpham = san_pham.id_sp
                WHERE chi_tiet_hoa_don.id_hoadon = ?";
        return pdo_query($sql, $id_hoadon);
    }
    // Cập nhật trạng thái đơn hàng (Ví dụ: Chờ xử lý -> Đang giao)
    public function updateStatus($id, $status, $reason = '') {
        $allowed = OrderStatus::all();
        if (!in_array($status, $allowed, true)) {
            throw new DomainException('Trạng thái đơn hàng không hợp lệ.');
        }
        if ($status === OrderStatus::CANCELLED) {
            if (!is_string($reason) || !preg_match('/\S/u', $reason) || mb_strlen($reason, 'UTF-8') > 1000) {
                throw new DomainException('Vui lòng nhập lý do hủy đơn, tối đa 1000 ký tự.');
            }
            $reason = trim($reason);
        }
        $conn = $this->connection ?? pdo_get_connection();
        $conn->beginTransaction();
        try {
            // Khóa đơn trước khi kiểm tra, tránh hai yêu cầu hủy cùng hoàn kho.
            $stmt = $conn->prepare('SELECT trang_thai, da_tru_kho FROM hoadon WHERE id_hoadon = ? FOR UPDATE');
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) throw new DomainException('Không tìm thấy đơn hàng.');
            $current = $order['trang_thai'];
            if ($current === $status) {
                $conn->commit();
                return;
            }
            if (!in_array($status, OrderStatus::next($current), true)) {
                throw new DomainException('Chỉ được chuyển sang bước kế tiếp. Chỉ đơn Chờ xác nhận hoặc Đang giao hàng mới được Hủy đơn.');
            }
            if ($status === OrderStatus::CANCELLED && (int)$order['da_tru_kho'] === 1) {
                $details = $conn->prepare('SELECT id_sanpham, SUM(so_luong) AS quantity FROM chi_tiet_hoa_don WHERE id_hoadon = ? GROUP BY id_sanpham ORDER BY id_sanpham');
                $details->execute([$id]);
                $restore = $conn->prepare('UPDATE san_pham SET so_luong = so_luong + ? WHERE id_sp = ? AND so_luong <= ?');
                foreach ($details->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $quantity = (int)$item['quantity'];
                    if ($quantity < 1 || $quantity > 2147483647) throw new DomainException('Chi tiết đơn hàng không hợp lệ, không thể hoàn kho.');
                    $restore->execute([$quantity, $item['id_sanpham'], 2147483647 - $quantity]);
                    if ($restore->rowCount() !== 1) throw new DomainException('Không thể hoàn kho sản phẩm.');
                }
            }
            if ($status === OrderStatus::CANCELLED) {
                $stmt = $conn->prepare('UPDATE hoadon SET trang_thai = ?, ly_do_huy = ?, da_tru_kho = 0 WHERE id_hoadon = ?');
                $stmt->execute([$status, $reason, $id]);
            } else {
                // Đơn cũ chưa từng trừ kho thì hủy cũng không cộng kho.
                $stmt = $conn->prepare('UPDATE hoadon SET trang_thai = ? WHERE id_hoadon = ?');
                $stmt->execute([$status, $id]);
            }
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            throw $e;
        }
    }
}
?>
