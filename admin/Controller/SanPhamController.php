<?php
include_once("Model/SanPham.php");
include_once("Model/DanhMuc.php"); // Nhớ include cả Model DanhMuc

require_once __DIR__ . "/ProductImageUpload.php";

require_once __DIR__ . '/../../Support/RequestGuard.php';

class SanPhamController
{
    private $sanPham;
    private $danhMuc;

    public function __construct()
    {
        $this->sanPham = new SanPham();
        $this->danhMuc = new DanhMuc();
    }

    // --- HIỂN THỊ DANH SÁCH ---
    public function index()
    {
        // Gọi hàm getAll mới đã có đủ thông tin
        $allSanPham = $this->sanPham->getAll();
        
        include_once("./views/sanpham/list.php");
    }

    public function create()
    {
        $allDanhMuc = $this->danhMuc->getAll();
        include_once("./views/sanpham/create.php");
    }

    // --- THÊM MỚI (STORE) ---
    public function store() {
        if(isset($_POST['ten'])) {
            $ten = $_POST['ten'];
            $gia = $_POST['gia'];
            $moTa = $_POST['mota'];
            $idDanhMuc = $_POST['danhmuc'];
            
            $soLuong = $_POST['so_luong'] ?? 0;
            $loai = $_POST['loai'] ?? 'Thường'; 

            try {
                $imageName = ProductImageUpload::save($_FILES['anh'] ?? null, true);
            } catch (RuntimeException $e) {
                $this->uploadError($e->getMessage(), 'index.php?action=createsanpham');
                return;
            }

            $this->sanPham->insert($ten, $gia, $imageName, $moTa, $idDanhMuc, $soLuong, $loai);
            
            header("Location:index.php?action=listsanpham"); 
        }
    }

    public function edit() {
        $sanPham = RequestGuard::findRecord($this->sanPham, $_GET['id'] ?? null,
            'Sản phẩm không tồn tại.', 'index.php?action=listsanpham');
        if (!$sanPham) return;
        $allDanhMuc = $this->danhMuc->getAll();
        include "views/sanpham/edit.php";
    }

    // --- CẬP NHẬT (UPDATE) ---
    public function update() {
        $record = RequestGuard::findRecord($this->sanPham, $_POST['id'] ?? null,
            'Sản phẩm không tồn tại.', 'index.php?action=listsanpham');
        if (!$record) return;

        if(isset($_POST['id'])) { 
            $id = $_POST['id'];
            $ten = $_POST['ten'];
            $gia = $_POST['gia'];
            $moTa = $_POST['mota'];
            $idDanhMuc = $_POST['danhmuc'];
            
            $soLuong = $_POST['so_luong'] ?? 0;
            $loai = $_POST['loai'] ?? 'Thường';

            $spCu = $this->sanPham->getOne($id);
            if (!$spCu) {
                $this->uploadError('Không tìm thấy sản phẩm.', 'index.php?action=listsanpham');
                return;
            }
            try {
                $newImage = ProductImageUpload::save($_FILES['anh'] ?? null);
            } catch (RuntimeException $e) {
                $this->uploadError($e->getMessage(), 'index.php?action=editsanpham&id=' . urlencode($id));
                return;
            }
            $imageName = $newImage !== '' ? $newImage : $spCu['hinh_anh'];

            $this->sanPham->update($id, $ten, $gia, $imageName, $moTa, $idDanhMuc, $soLuong, $loai);
            
            header("Location:index.php?action=listsanpham");
        }
    }

    private function uploadError($message, $url) {
        $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
        echo '<script>alert(' . json_encode($message, $flags)
            . '); window.location.href=' . json_encode($url, $flags) . ';</script>';
    }

    public function delete() {
        $record = RequestGuard::findRecord($this->sanPham, $_GET['id'] ?? null,
            'Sản phẩm không tồn tại.', 'index.php?action=listsanpham');
        if (!$record) return;

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->sanPham->delete($id);
            header("Location:index.php?action=listsanpham");
        }
    }

    public function restore() {
        $record = RequestGuard::findRecord($this->sanPham, $_GET['id'] ?? null,
            'Sản phẩm không tồn tại.', 'index.php?action=listsanpham');
        if (!$record) return;

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->sanPham->restore($id); // Đã có hàm restore bên Model
            header("Location:index.php?action=listsanpham");
        }
    }
}
?>