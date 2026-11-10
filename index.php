<?php
session_start(); // Bắt buộc phải có để dùng giỏ hàng (lưu thông tin sản phẩm đã chọn)

// Nhúng các file Controller để sử dụng
include_once "Controller/HomeController.php";
include_once "Controller/CartController.php"; 
include_once "Controller/AccountController.php";

// Khởi tạo các đối tượng Controller
$homeController = new HomeController();
$cartController = new CartController();
$accountController = new AccountController();

// -------------------------------------------------------------
// PHẦN SỬA ĐỔI: Dùng If-Else để kiểm tra người dùng đang muốn vào trang nào
// -------------------------------------------------------------

// Kiểm tra xem trên thanh địa chỉ (URL) có tham số 'act' hay không?
// Ví dụ: index.php?act=shop -> Có act
// Ví dụ: index.php -> Không có act
if (isset($_GET['act'])) {
    // Nếu có, lấy giá trị đó gán cho biến $act
    $act = $_GET['act']; 
} else {
    // Nếu không có (người dùng vào trang chủ lần đầu), gán giá trị mặc định là '/'
    $act = '/'; 
}

// -------------------------------------------------------------
// ĐIỀU HƯỚNG: Dựa vào biến $act để gọi hàm tương ứng
// -------------------------------------------------------------
switch ($act) {
    case 'register':
        $accountController->register();
        break;
    case 'login':
        $accountController->login();
        break;
    case 'logout':
        $accountController->logout();
        break;
    case 'shop':
        $homeController->shop();
        break;
        
    // --- CÁC CHỨC NĂNG GIỎ HÀNG & THANH TOÁN ---
    case 'detail':
        $cartController->detail(); // Xem chi tiết sản phẩm
        break;
        
    case 'add_to_cart':
        $cartController->addToCart(); // Thêm sản phẩm vào giỏ
        break;
        
    case 'cart':
        $cartController->viewCart(); // Xem giỏ hàng hiện tại
        break;
        
    case 'delete_cart':
        $cartController->deleteItem(); // Xóa sản phẩm khỏi giỏ
        break;
        
    case 'checkout':
        $cartController->checkout(); // Trang nhập thông tin thanh toán
        break;
        
    case 'place_order':
        $cartController->placeOrder(); // Xử lý lưu hóa đơn vào Database
        break;
    // -------------------------------------------

    case 'about':
        $homeController->about();
        break;
        
    case 'contact':
        $homeController->contact();
        break;
    
    // --- CHỨC NĂNG TRA CỨU ĐƠN HÀNG (KHÔNG CẦN ĐĂNG NHẬP) ---
    case 'check_order':
        $cartController->checkOrder(); // Form nhập số điện thoại tra cứu
        break;
        
    case 'history_detail':
        $cartController->historyDetail(); // Xem chi tiết lịch sử đơn hàng đã mua
        break;

    case 'update_cart': // Thêm case này
        $cartController->updateCart();
        break;
    case 'cart_update':
        $cartController->updateCartAjax();
        break;

    // Mặc định: Nếu $act = '/' hoặc tên act lạ không khớp cái nào ở trên
    // Thì sẽ chạy vào trang chủ
    default:
        $homeController->home();
        break;
}
?>
