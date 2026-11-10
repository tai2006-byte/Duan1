<?php
include_once("Model/DanhMuc.php");
require_once __DIR__ . '/../../Support/RequestGuard.php';

class DanhMucController
{
    private $danhMuc;

    public function __construct()
    {
        $this->danhMuc = new DanhMuc();
    }

    // Phương thức list
    public function index()
    {
        $allDanhMuc = $this->danhMuc->getAll();
        include_once("./views/danhmuc/list.php");
    }

    public function create()
    {
        include_once("./views/danhmuc/create.php");
    }

    public function store() {
        if(isset($_POST['ten'])) {
            $ten = $_POST['ten'];
            $this->danhMuc->insert($ten); // Gọi function insert ở model.
            header("Location:index.php"); // Điều hướng trở lại trang index
        }
    }

    public function edit() {
        $danhMuc = RequestGuard::findRecord($this->danhMuc, $_GET['id'] ?? null,
            'Danh mục không tồn tại.', 'index.php?action=listdanhmuc');
        if (!$danhMuc) return;
        include "views/danhmuc/edit.php";
    }

    public function update() {
        $record = RequestGuard::findRecord($this->danhMuc, $_POST['id'] ?? null,
            'Danh mục không tồn tại.', 'index.php?action=listdanhmuc');
        if (!$record) return;

        if(isset($_POST['id'])) {
            $id = $_POST['id'];
            $ten = $_POST['ten'];
            $this->danhMuc->update($id, $ten);
            header("Location:index.php");
        }
    }

    // Hàm xóa
    public function delete() {
        $record = RequestGuard::findRecord($this->danhMuc, $_GET['id'] ?? null,
            'Danh mục không tồn tại.', 'index.php?action=listdanhmuc');
        if (!$record) return;

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->danhMuc->delete($id);
            // Xóa xong quay lại trang list
            header("Location:index.php?action=listdanhmuc");
        }
    }

    // Hàm khôi phục (Bắt buộc phải có)
    public function restore() {
        $record = RequestGuard::findRecord($this->danhMuc, $_GET['id'] ?? null,
            'Danh mục không tồn tại.', 'index.php?action=listdanhmuc');
        if (!$record) return;

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->danhMuc->restore($id);
            // Khôi phục xong quay lại trang list
            header("Location:index.php?action=listdanhmuc");
        }
    }
}

?>