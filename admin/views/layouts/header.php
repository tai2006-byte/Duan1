<?php
require_once __DIR__ . '/helpers.php';
$action = is_string($_GET['action'] ?? null) && $_GET['action'] !== '' ? $_GET['action'] : 'dashboard';
$navigation = [
    'dashboard' => ['Tổng quan', 'dashboard', 'grid-1x2'],
    'taikhoan' => ['Quản lý tài khoản', 'listtaikhoan', 'person'],
    'danhmuc' => ['Danh mục sản phẩm', 'listdanhmuc', 'grid'],
    'sanpham' => ['Sản phẩm', 'listsanpham', 'box-seam'],
    'hoadon' => ['Quản lý hóa đơn', 'listhoadon', 'receipt'],
    'thongke' => ['Thống kê', 'thongke', 'bar-chart-line']
];
$adminName = $_SESSION['admin_login']['username'] ?? 'Admin';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản trị cửa hàng đồ chơi</title>
    <link rel="stylesheet" href="views/assets/css/bootstrap.css">
    <link rel="stylesheet" href="views/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="views/assets/vendors/simple-datatables/style.css">
    <link rel="stylesheet" href="views/assets/css/admin-basic.css?v=7">
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu quản trị">
    <a class="admin-brand" href="index.php?action=dashboard"><i class="bi bi-box" aria-hidden="true"></i><span>Quản trị cửa hàng</span></a>
    <nav class="admin-menu" aria-label="Điều hướng chính">
        <?php foreach ($navigation as $key => [$label, $route, $icon]): $active = str_contains($action, $key) || ($key === 'hoadon' && $action === 'update_status'); ?>
        <a class="admin-menu-link <?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?> href="index.php?action=<?= $route ?>"><i class="bi bi-<?= $icon ?>" aria-hidden="true"></i><span><?= $label ?></span><?php if ($key === 'hoadon' && !empty($pendingOrders)): ?><span class="admin-menu-count"><?= (int)$pendingOrders ?></span><?php endif; ?></a>
        <?php endforeach; ?>
        <div class="admin-menu-divider"></div>
        <a class="admin-menu-link" href="../index.php"><i class="bi bi-shop" aria-hidden="true"></i><span>Xem cửa hàng</span></a>
        <a class="admin-menu-link" href="index.php?action=logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i><span>Đăng xuất</span></a>
    </nav>
    <div class="admin-sidebar-foot">Cửa hàng đồ chơi</div>
</aside>
<button class="admin-sidebar-backdrop" id="sidebarBackdrop" aria-label="Đóng menu" hidden></button>
<div class="admin-shell">
    <header class="admin-topbar">
        <button type="button" class="admin-icon-button" id="sidebarToggle" aria-label="Thu gọn hoặc mở menu" aria-controls="adminSidebar" aria-expanded="true"><i class="bi bi-list" aria-hidden="true"></i></button>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <a class="admin-icon-button admin-notification" href="index.php?action=listhoadon" aria-label="Xem đơn hàng<?= !empty($pendingOrders) ? ', ' . (int)$pendingOrders . ' đơn chờ xác nhận' : '' ?>"><i class="bi bi-bell" aria-hidden="true"></i><?php if (!empty($pendingOrders)): ?><span class="notification-dot"></span><?php endif; ?></a>
            <div class="dropdown">
                <button class="admin-user" type="button" id="adminUserMenu" data-bs-toggle="dropdown" aria-expanded="false"><span class="admin-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></span><span><?= adminEscape($adminName) ?></span><i class="bi bi-chevron-down small" aria-hidden="true"></i></button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserMenu"><li><a class="dropdown-item" href="../index.php">Xem cửa hàng</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-danger" href="index.php?action=logout">Đăng xuất</a></li></ul>
            </div>
        </div>
    </header>
    <main class="admin-content flex-grow-1">
