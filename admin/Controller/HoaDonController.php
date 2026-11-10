<?php
include_once "Model/HoaDon.php";
require_once __DIR__ . '/../../Support/RequestGuard.php';

class HoaDonController {
    private $hoaDonModel;

    public function __construct() {
        $this->hoaDonModel = new HoaDon();
    }

    // Hiển thị danh sách đơn hàng
    public function index() {
        $listHoaDon = $this->hoaDonModel->getAllOrders();
        include "views/hoadon/list.php";
    }

    // Xem chi tiết đơn hàng
    public function detail() {
        $id = RequestGuard::positiveId($_GET['id'] ?? null);
        if ($id !== false) {
            // 1. Lấy thông tin người mua
            $order = $this->hoaDonModel->getOrderById($id);
            if (!$order) {
                RequestGuard::notFound('Không tìm thấy hóa đơn.', 'index.php?action=listhoadon');
                return;
            }
            // 2. Lấy danh sách sản phẩm họ mua
            $orderDetails = $this->hoaDonModel->getOrderDetail($id);
            
            include "views/hoadon/detail.php";
        } else {
            RequestGuard::notFound('Không tìm thấy hóa đơn.', 'index.php?action=listhoadon');
        }
    }

    // Cập nhật trạng thái
    public function update_status() {
        $id = RequestGuard::positiveId($_POST['id'] ?? null);
        if ($id === false || !$this->hoaDonModel->getOrderById($id)) {
            RequestGuard::notFound('Không tìm thấy hóa đơn.', 'index.php?action=listhoadon');
            return;
        }
        if (isset($_POST['id']) && isset($_POST['trang_thai'])) {
            $id = $_POST['id'];
            $status = $_POST['trang_thai'];
            try {
                $this->hoaDonModel->updateStatus($id, $status, $_POST['ly_do_huy'] ?? '');
            } catch (DomainException $e) {
                $order = $this->hoaDonModel->getOrderById($id);
                if (!$order) {
                    RequestGuard::notFound('Không tìm thấy hóa đơn.', 'index.php?action=listhoadon');
                    return;
                }
                $orderDetails = $this->hoaDonModel->getOrderDetail($id);
                $statusError = $e->getMessage();
                $selectedStatus = is_string($status) ? $status : $order['trang_thai'];
                $cancelReason = is_string($_POST['ly_do_huy'] ?? null) ? $_POST['ly_do_huy'] : '';
                http_response_code(422);
                include 'views/hoadon/detail.php';
                return;
            } catch (Throwable $e) {
                error_log('Cập nhật đơn hàng thất bại: ' . $e->getMessage());
                $this->statusError('Không thể cập nhật đơn hàng. Dữ liệu và tồn kho chưa thay đổi.');
                return;
            }
            
            // Quay lại trang chi tiết
            header("Location: index.php?action=listhoadon");
        }
    }

    private function statusError($message) {
        echo '<script>alert(' . json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
            . '); window.location.href="index.php?action=listhoadon";</script>';
    }
}
?>
