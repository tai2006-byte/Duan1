<?php
session_start(); // BẮT BUỘC PHẢI CÓ DÒNG NÀY Ở ĐẦU TIÊN

// Include các Controller
include_once("Controller/DanhMucController.php");
include_once("Controller/SanPhamController.php");
include_once("Controller/ThongKeController.php");
include_once("Controller/TaiKhoanController.php"); // Include controller tài khoản
include_once("Controller/HoaDonController.php");
include_once("Controller/DashboardController.php");

// Khởi tạo đối tượng
$danhMuc = new DanhMucController();
$sanPham = new SanPhamController();
$thongKe = new ThongKeController();
$taiKhoanCtrl = new TaiKhoanController(); // Khởi tạo controller tài khoản
$hoaDonCtrl = new HoaDonController();
$dashboard = new DashboardController();

// --- PHẦN KIỂM TRA ĐĂNG NHẬP (GÁC CỔNG) ---
// Kiểm tra lại ở mỗi yêu cầu để phiên của tài khoản bị khóa không còn hiệu lực.
if (isset($_SESSION['admin_login']) && !$taiKhoanCtrl->validateSession()) {
    $taiKhoanCtrl->login('Phiên đăng nhập đã hết hiệu lực hoặc tài khoản đã bị khóa. Vui lòng đăng nhập lại.');
    exit();
}
// Nếu chưa có Session 'admin_login' thì bắt buộc đăng nhập
if (!isset($_SESSION['admin_login'])) {
    // Nếu người dùng đang gửi dữ liệu đăng nhập (action=checklogin) thì cho phép đi tiếp
    if (isset($_GET['action']) && $_GET['action'] == 'checklogin') {
        $taiKhoanCtrl->checkLogin();
    } else {
        // Còn lại tất cả các trường hợp khác đều bắt hiện form login
        $taiKhoanCtrl->login();
    }
    // Dừng chương trình tại đây, không cho chạy xuống phần switch bên dưới
    exit(); 
}
// ------------------------------------------

// Nếu đã đăng nhập rồi thì chạy Switch này
if(isset($_GET['action']) && $_GET['action'] != "") {
    $action = $_GET['action'];
    switch($action) {
        case 'dashboard':
            $dashboard->index();
            break;
        // --- CÁC CASE ĐĂNG NHẬP / ĐĂNG XUẤT ---
        case 'logout':
            $taiKhoanCtrl->logout();
            break;
        case 'listtaikhoan':
            $taiKhoanCtrl->index();
            break;
        case 'createtaikhoan':
            $taiKhoanCtrl->create();
            break;
        case 'storetaikhoan':
            $taiKhoanCtrl->store();
            break;
        case 'edittaikhoan':
            $taiKhoanCtrl->edit();
            break;
        case 'updatetaikhoan':
            $taiKhoanCtrl->update();
            break;
        case 'statustaikhoan':
            $taiKhoanCtrl->setActive();
            break;

        // --- DANH MỤC ---
        case "listdanhmuc":
            $danhMuc->index();
            break;
        case "createdanhmuc":
            $danhMuc->create();
            break;
        case "storedanhmuc":
            $danhMuc->store();
            break;
        case "editdanhmuc":
            $danhMuc->edit();
            break;
        case "updatedanhmuc":
            $danhMuc->update();
            break;
        case "deletedanhmuc":
            $danhMuc->delete();
            break;
        case "restoredanhmuc":
            $danhMuc->restore();
            break;

        // --- THỐNG KÊ ---
        case "thongke":
            $thongKe->index();
            break;

        // --- SẢN PHẨM ---
        case "listsanpham":
            $sanPham->index();
            break;
        case "createsanpham":
            $sanPham->create();
            break;
        case "storesanpham":
            $sanPham->store();
            break;
        case "editsanpham":
            $sanPham->edit();
            break;
        case "updatesanpham":
            $sanPham->update();
            break;
        case "deletesanpham":
            $sanPham->delete();
            break;
        case "restoresanpham":
            $sanPham->restore();
            break;

        // ----- Hóa đơn ----
        case 'listhoadon':
            $hoaDonCtrl->index();
            break;
        case 'hoadon_detail':
            $hoaDonCtrl->detail();
            break;
        case 'update_status':
            $hoaDonCtrl->update_status();
            break;
            
        default:
            $dashboard->index();
            break;
    }
} else {
    // Mặc định vào trang danh mục hoặc Dashboard
    $dashboard->index();
}

?>
