<?php
// Nhúng các file Model cần thiết để xử lý dữ liệu
include_once "Model/UserSanpham.php"; // Model lấy thông tin sản phẩm
include_once "Model/CartModel.php";   // Model xử lý đơn hàng (lưu hóa đơn)
require_once __DIR__ . '/../Support/RequestGuard.php';

class CartController {
    // Khai báo 2 thuộc tính để chứa đối tượng Model
    private $userSanpham;
    private $cartModel;

    // Hàm khởi tạo (Chạy ngay khi Controller được gọi)
    public function __construct() {
        $this->userSanpham = new UserSanpham();
        $this->cartModel = new CartModel();
        
        // Kiểm tra xem giỏ hàng đã tồn tại chưa
        // Nếu chưa có thì tạo mới một mảng rỗng để chứa sản phẩm
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->normalizeCart();
        if (empty($_SESSION['cart_csrf'])) $_SESSION['cart_csrf'] = bin2hex(random_bytes(32));
    }

    // ----------------------------------------------------------------
    // 1. CHỨC NĂNG: XEM CHI TIẾT SẢN PHẨM
    // ----------------------------------------------------------------
    public function detail() {
        // Bước 1: Kiểm tra xem trên URL có ID sản phẩm không (ví dụ: &id=10)
        $id = RequestGuard::positiveId($_GET['id'] ?? null);
        if ($id !== false) {
            
            // Bước 2: Gọi Model để lấy thông tin chi tiết của sản phẩm đó
            $sp = $this->userSanpham->getProductDetail($id);
            if (!$sp || (int)$sp['trang_thai'] !== 1) {
                RequestGuard::notFound('Sản phẩm không tồn tại hoặc đã ngừng bán.', 'index.php?act=shop');
                return;
            }
            
            include "views/shop-details.php";
        } else {
            // Nếu không có ID thì đuổi về trang chủ
            RequestGuard::notFound('Sản phẩm không tồn tại hoặc đã ngừng bán.', 'index.php?act=shop');
        }
    }

    // ----------------------------------------------------------------
    // 2. CHỨC NĂNG: THÊM VÀO GIỎ HÀNG
    // ----------------------------------------------------------------
    public function addToCart() {
        // Kiểm tra xem người dùng có bấm nút "Thêm vào giỏ" không
        if (isset($_POST['add_to_cart'])) {
            
            // Bước 1: Lấy dữ liệu từ Form gửi lên
            $id = $this->positiveInteger($_POST['id_sp'] ?? null);
            $soluong = $this->positiveInteger($_POST['quantity'] ?? 1);
            if ($id === false || $soluong === false) {
                $this->cartError('Số lượng phải là số nguyên dương hợp lệ.');
                return;
            }
            $sp = $this->userSanpham->getProductDetail($id);
            if (!$sp || (int)$sp['trang_thai'] !== 1) {
                $this->cartError('Sản phẩm không tồn tại hoặc đã ngừng bán.');
                return;
            }
            $ten = $sp['ten_sp'];
            $gia = $sp['gia_sp'];
            $img = $sp['hinh_anh'];
            if (!$this->hasStock($id, $soluong, (int)$sp['so_luong'])) {
                $this->cartError('Số lượng yêu cầu vượt quá tồn kho hiện có.');
                return;
            }
            
            // Mỗi sản phẩm chỉ có một dòng trong giỏ, không còn biến thể.
            $key = 'product_' . $id;

            // Bước 3: Kiểm tra xem sản phẩm này đã có trong giỏ hàng chưa
            if (isset($_SESSION['cart'][$key])) {
                // Nếu có rồi -> Chỉ cần tăng số lượng lên
                $oldQuantity = $this->positiveInteger($_SESSION['cart'][$key]['so_luong'] ?? null);
                if ($oldQuantity === false || $oldQuantity > 2147483647 - $soluong) {
                    $this->cartError('Số lượng trong giỏ hàng không hợp lệ. Vui lòng xóa sản phẩm và thêm lại.');
                    return;
                }
                $_SESSION['cart'][$key]['so_luong'] = $oldQuantity + $soluong;
                $_SESSION['cart'][$key]['ten'] = $ten;
                $_SESSION['cart'][$key]['gia'] = $gia;
                $_SESSION['cart'][$key]['img'] = $img;
            } else {
                // Nếu chưa có -> Thêm mới vào mảng giỏ hàng
                $_SESSION['cart'][$key] = [
                    'id' => $id,
                    'ten' => $ten,
                    'gia' => $gia,
                    'img' => $img,
                    'so_luong' => $soluong
                ];
            }
            
            // Bước 4: Chuyển hướng người dùng sang trang xem giỏ hàng
            header("Location: index.php?act=cart"); 
        }
    }

    // ----------------------------------------------------------------
    // 3. CHỨC NĂNG: XEM GIỎ HÀNG
    // ----------------------------------------------------------------
    public function viewCart() {
        if (!$this->refreshCart()) return;
        // Chỉ đơn giản là gọi giao diện giỏ hàng ra
        // Dữ liệu giỏ hàng nằm trong $_SESSION['cart'] rồi nên View tự lấy được
        include "views/shopping-cart.php";
    }

    // ----------------------------------------------------------------
    // 4. CHỨC NĂNG: XÓA SẢN PHẨM KHỎI GIỎ
    // ----------------------------------------------------------------
    public function deleteItem() {
        // Kiểm tra xem trên URL có key sản phẩm cần xóa không
        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            
            // Dùng hàm unset để xóa phần tử đó khỏi session
            unset($_SESSION['cart'][$key]);
        }
        // Xóa xong thì quay lại trang giỏ hàng
        header("Location: index.php?act=cart");
    }

    // ----------------------------------------------------------------
    // 5. CHỨC NĂNG: TRANG THANH TOÁN (CHECKOUT)
    // ----------------------------------------------------------------
    public function checkout() {
        if (!$this->refreshCart()) return;
        // Gọi giao diện nhập thông tin thanh toán
        include "views/checkout.php";
    }

    // ----------------------------------------------------------------
    // 6. CHỨC NĂNG: XỬ LÝ ĐẶT HÀNG (LƯU VÀO DB)
    // ----------------------------------------------------------------
    public function placeOrder() {
        // Kiểm tra người dùng có bấm nút ĐẶT HÀNG không
        // Và giỏ hàng phải có sản phẩm thì mới cho đặt
        if (isset($_POST['site-btn']) && !empty($_SESSION['cart'])) {
            // Kiểm tra lại cả giỏ cũ và lấy giá hiện tại trước khi lưu hóa đơn.
            if (!$this->refreshCart()) return;
            
            // Bước 1: Lấy thông tin người nhận từ Form
            $ho_ten = $_POST['ho_ten'];
            $dia_chi = $_POST['dia_chi'];
            $sdt = $_POST['sdt'];
            
            // Chỉ hỗ trợ COD, không lấy phương thức thanh toán từ client.
            $pttt = 'Tiền mặt (COD)';
            
            // Bước 2: Tính tổng tiền đơn hàng
            $tong_tien = 0;
            foreach ($_SESSION['cart'] as $item) {
                // Thành tiền = Giá x Số lượng
                $thanhTien = $item['gia'] * $item['so_luong'];
                $tong_tien += $thanhTien;
            }

            // Bước 3-4: Chỉ thành công khi cả hóa đơn và mọi dòng chi tiết đã được commit.
            try {
                $id_hoadon = $this->cartModel->createOrderWithDetails(
                    $ho_ten, $sdt, $dia_chi, $tong_tien, $pttt, $_SESSION['cart']
                );
            } catch (DomainException $e) {
                $this->cartError($e->getMessage());
                return;
            } catch (Throwable $e) {
                error_log('Lưu đơn hàng thất bại: ' . $e->getMessage());
                $this->cartError('Không thể lưu đơn hàng. Giỏ hàng của bạn vẫn được giữ lại, vui lòng thử lại.');
                return;
            }

            // Bước 5: Xóa sạch giỏ hàng sau khi đặt thành công
            unset($_SESSION['cart']);

            // Bước 6: Thông báo cho khách và quay về trang chủ
            echo "<script>
                alert('Đặt hàng thành công! Mã đơn hàng của bạn là: #$id_hoadon');
                window.location.href='index.php';
            </script>";

        } else {
            // Nếu giỏ hàng trống mà cố tình vào đặt hàng
            echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php?act=shop';</script>";
        }
    }

    // ----------------------------------------------------------------
    // 7. CHỨC NĂNG: TRA CỨU ĐƠN HÀNG (KHÁCH VÃNG LAI)
    // ----------------------------------------------------------------
    public function checkOrder() {
        $orders = [];  // Biến chứa danh sách đơn hàng tìm được
        $message = ""; // Biến chứa thông báo lỗi (nếu có)
        
        // Kiểm tra xem người dùng có bấm nút "Tra cứu" không
        if (isset($_POST['btn_check'])) {
            $sdt = $_POST['sdt']; // Lấy số điện thoại người dùng nhập
            
            // Nếu số điện thoại không được để trống
            if (!empty($sdt)) {
                // Gọi Model để tìm các đơn hàng có SĐT này
                $orders = $this->cartModel->getOrdersByPhone($sdt);
                
                // Nếu tìm xong mà danh sách vẫn rỗng -> Không có đơn nào
                if (empty($orders)) {
                    $message = "Không tìm thấy đơn hàng nào với số điện thoại này.";
                }
            } else {
                $message = "Vui lòng nhập số điện thoại.";
            }
        }
        // Hiện giao diện tra cứu kèm kết quả
        include "views/check_order.php";
    }

    // ----------------------------------------------------------------
    // 8. CHỨC NĂNG: XEM CHI TIẾT LỊCH SỬ ĐƠN HÀNG
    // ----------------------------------------------------------------
    public function historyDetail() {
        // Kiểm tra xem trên URL có ID hóa đơn không
        $id_hoadon = RequestGuard::positiveId($_GET['id'] ?? null);
        if ($id_hoadon !== false) {
            
            // Lấy thông tin chung của hóa đơn (Tên người nhận, địa chỉ...)
            $bill = $this->cartModel->getOneBill($id_hoadon);
            if (!$bill) {
                RequestGuard::notFound('Không tìm thấy đơn hàng.', 'index.php?act=check_order');
                return;
            }
            
            // Lấy danh sách sản phẩm trong hóa đơn đó
            $billDetail = $this->cartModel->getBillDetail($id_hoadon);
            
            // Hiện giao diện chi tiết
            include "views/history_detail.php";
        } else {
            // Nếu không có ID thì quay lại trang tra cứu
            RequestGuard::notFound('Không tìm thấy đơn hàng.', 'index.php?act=check_order');
        }
    }
    public function updateCart() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            try {
                $this->validateCartToken();
                $this->setCartQuantity($_POST['key'] ?? null, $_POST['quantity'] ?? null);
            } catch (DomainException $e) {
                $this->cartError($e->getMessage());
                return;
            }
            header('Location: index.php?act=cart');
            return;
        }
        if (isset($_GET['key']) && isset($_GET['type'])) {
            $key = $_GET['key'];
            $type = $_GET['type'];

            if (isset($_SESSION['cart'][$key])) {
                if ($type == 'increase') {
                    // Nếu là tăng -> cộng thêm 1
                    $quantity = $this->positiveInteger($_SESSION['cart'][$key]['so_luong'] ?? null);
                    if ($quantity === false || $quantity >= 2147483647) {
                        $this->cartError('Số lượng trong giỏ hàng không hợp lệ.');
                        return;
                    }
                    $id = $_SESSION['cart'][$key]['id'];
                    $sp = $this->userSanpham->getProductDetail($id);
                    if (!$sp || (int)$sp['trang_thai'] !== 1 || !$this->hasStock($id, 1, (int)$sp['so_luong'])) {
                        $this->cartError('Sản phẩm không còn đủ số lượng để tăng thêm.');
                        return;
                    }
                    $_SESSION['cart'][$key]['so_luong']++;
                } else if ($type == 'decrease') {
                    // Nếu là giảm
                    if ($_SESSION['cart'][$key]['so_luong'] > 1) {
                        // Nếu số lượng > 1 thì trừ đi 1
                        $_SESSION['cart'][$key]['so_luong']--;
                    } else {
                        // Nếu số lượng đang là 1 mà bấm giảm -> Xóa luôn sản phẩm
                        unset($_SESSION['cart'][$key]);
                    }
                }
            }
        }
        // Quay lại trang giỏ hàng
        header("Location: index.php?act=cart");
    }

    private function validateCartToken() {
        $token = $_POST['csrf_token'] ?? null;
        if (!is_string($token) || !hash_equals($_SESSION['cart_csrf'], $token)) {
            throw new DomainException('Phiên giỏ hàng không hợp lệ. Vui lòng tải lại trang.');
        }
    }

    private function setCartQuantity($key, $value) {
        if (!is_string($key) || !isset($_SESSION['cart'][$key])) throw new DomainException('Sản phẩm không còn trong giỏ hàng.');
        $quantity = $this->positiveInteger($value);
        if ($quantity === false) throw new DomainException('Số lượng phải là số nguyên từ 1 trở lên.');
        $item = $_SESSION['cart'][$key];
        $product = $this->userSanpham->getProductDetail($item['id']);
        if (!$product || (int)$product['trang_thai'] !== 1) throw new DomainException('Sản phẩm đã ngừng bán. Vui lòng xóa khỏi giỏ hàng.');
        if (!$this->hasStock($item['id'], $quantity - $item['so_luong'], (int)$product['so_luong'])) {
            throw new DomainException('Sản phẩm chỉ còn ' . max(0, (int)$product['so_luong']) . ' sản phẩm trong kho.');
        }
        $_SESSION['cart'][$key]['so_luong'] = $quantity;
        $_SESSION['cart'][$key]['gia'] = $product['gia_sp'];
        $_SESSION['cart'][$key]['ten'] = $product['ten_sp'];
        $_SESSION['cart'][$key]['img'] = $product['hinh_anh'];
    }

    public function updateCartAjax() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Allow: POST');
            $this->cartJson(false, 'Yêu cầu phải dùng POST.', 405);
            return;
        }
        try {
            try { $this->validateCartToken(); }
            catch (DomainException $e) { $this->cartJson(false, $e->getMessage(), 403); return; }
            $operation = $_POST['operation'] ?? 'set';
            if ($operation === 'set') {
                $this->setCartQuantity($_POST['key'] ?? null, $_POST['quantity'] ?? null);
            } elseif ($operation === 'remove') {
                $key = $_POST['key'] ?? null;
                if (!is_string($key)) throw new DomainException('Sản phẩm không hợp lệ.');
                unset($_SESSION['cart'][$key]); // Xóa lặp không làm thay đổi các món khác.
            } elseif ($operation !== 'state') {
                throw new DomainException('Thao tác giỏ hàng không hợp lệ.');
            }
            $this->cartJson(true, 'Đã cập nhật giỏ hàng.');
        } catch (DomainException $e) {
            $this->cartJson(false, $e->getMessage(), 422);
        } catch (Throwable $e) {
            error_log('Cập nhật giỏ hàng: ' . $e->getMessage());
            $this->cartJson(false, 'Chưa thể cập nhật giỏ hàng. Vui lòng thử lại.', 500);
        }
    }

    private function cartJson($success, $message, $status = 200) {
        $items = []; $total = 0; $count = 0;
        foreach ($_SESSION['cart'] as $key => $item) {
            $subtotal = (float)$item['gia'] * $item['so_luong'];
            $total += $subtotal; $count += $item['so_luong'];
            $items[] = ['key' => $key, 'quantity' => (int)$item['so_luong'], 'price' => (float)$item['gia'], 'subtotal' => $subtotal, 'name' => $item['ten']];
        }
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(['success' => $success, 'message' => $message, 'items' => $items, 'count' => $count, 'total' => $total], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    // Gộp giỏ hàng cũ theo ID, giữ nguyên tổng số lượng khi bỏ thuộc tính.
    private function normalizeCart() {
        $cart = [];
        foreach ($_SESSION['cart'] as $oldKey => $item) {
            if (!is_array($item)) continue;
            $id = $this->positiveInteger($item['id'] ?? null);
            $quantity = $this->positiveInteger($item['so_luong'] ?? null);
            if ($id === false || $quantity === false) continue;
            $key = 'product_' . $id;
            if (isset($cart[$key])) {
                $total = $cart[$key]['so_luong'] + $quantity;
                // Để bước xác thực loại bỏ giỏ lỗi thay vì âm thầm giảm số lượng.
                $cart[$key]['so_luong'] = $total <= 2147483647 ? $total : 0;
            } else {
                $cart[$key] = [
                    'id' => $id, 'ten' => $item['ten'] ?? '',
                    'gia' => $item['gia'] ?? 0, 'img' => $item['img'] ?? '',
                    'so_luong' => $quantity
                ];
            }
        }
        $_SESSION['cart'] = $cart;
    }

    private function hasStock($id, $additional, $stock) {
        $total = $additional;
        foreach ($_SESSION['cart'] as $item) {
            if ((int)$item['id'] === (int)$id) {
                $quantity = $this->positiveInteger($item['so_luong'] ?? null);
                if ($quantity === false) return false;
                $total += $quantity;
            }
        }
        return $total <= $stock;
    }

    private function positiveInteger($value) {
        if (!is_string($value) && !is_int($value)) return false;
        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 2147483647]
        ]);
    }

    private function cartError($message) {
        echo '<script>alert(' . json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
            . '); window.location.href="index.php?act=cart";</script>';
    }

    private function refreshCart() {
        $cart = $_SESSION['cart'];
        foreach ($cart as $key => $item) {
            $id = $this->positiveInteger($item['id'] ?? null);
            $quantity = $this->positiveInteger($item['so_luong'] ?? null);
            $sp = $id === false ? false : $this->userSanpham->getProductDetail($id);
            if ($quantity === false || !$sp || (int)$sp['trang_thai'] !== 1) {
                unset($_SESSION['cart'][$key]);
                $this->cartError('Một sản phẩm không còn bán hoặc có số lượng không hợp lệ đã được xóa khỏi giỏ. Vui lòng kiểm tra lại.');
                return false;
            }
            $cart[$key]['id'] = $id;
            $cart[$key]['so_luong'] = $quantity;
            $cart[$key]['ten'] = $sp['ten_sp'];
            $cart[$key]['gia'] = $sp['gia_sp'];
            $cart[$key]['img'] = $sp['hinh_anh'];
        }
        $_SESSION['cart'] = $cart;
        return true;
    }
}
?>
