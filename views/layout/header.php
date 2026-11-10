<?php
require_once __DIR__ . '/helpers.php';
$storeAct = is_string($_GET['act'] ?? null) ? $_GET['act'] : '/';
$storeTitles = ['shop'=>'Cửa hàng', 'detail'=>'Chi tiết sản phẩm', 'cart'=>'Giỏ hàng', 'checkout'=>'Thanh toán', 'check_order'=>'Tra cứu đơn hàng', 'history_detail'=>'Chi tiết đơn hàng', 'about'=>'Về Bé Vui', 'contact'=>'Liên hệ', 'login'=>'Đăng nhập', 'register'=>'Đăng ký'];
$cartCount = array_sum(array_column($_SESSION['cart'] ?? [], 'so_luong'));
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Bé Vui — Cửa hàng đồ chơi cho trẻ em. Khám phá sản phẩm, đặt hàng và theo dõi đơn hàng dễ dàng.">
<title><?= storeEscape($storeTitles[$storeAct] ?? 'Thế giới đồ chơi cho bé') ?> | Bé Vui</title>
<link rel="stylesheet" href="admin/views/assets/css/bootstrap.css">
<link rel="stylesheet" href="admin/views/assets/vendors/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="css/storefront.css?v=3">
</head>
<body class="store-body d-flex flex-column min-vh-100">
<a href="#store-main" class="visually-hidden-focusable skip-link">Đến nội dung chính</a>
<div class="store-topline"><div class="container d-flex justify-content-between gap-3"><span><i class="bi bi-star" aria-hidden="true"></i> Niềm vui nhỏ, khám phá lớn</span><a href="index.php?act=check_order">Tra cứu đơn hàng <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div></div>
<header class="store-header">
<div class="container py-3 d-flex flex-wrap align-items-center gap-3 gap-lg-4">
<a href="index.php" class="store-brand"><span class="brand-symbol"><i class="bi bi-box-seam" aria-hidden="true"></i></span><span>Bé<span class="brand-orange">Vui</span><small>THẾ GIỚI ĐỒ CHƠI</small></span></a>
<form class="store-search input-group" action="index.php" method="get" role="search"><input type="hidden" name="act" value="shop"><label class="visually-hidden" for="store-search">Tìm sản phẩm</label><input id="store-search" class="form-control" name="keyword" type="search" placeholder="Hôm nay bé muốn chơi gì?" value="<?= storeEscape(is_string($_GET['keyword'] ?? null) ? $_GET['keyword'] : '') ?>"><button class="btn btn-primary" aria-label="Tìm kiếm"><i class="bi bi-search" aria-hidden="true"></i></button></form>
<div class="store-header-actions ms-auto d-flex align-items-center gap-3">
<?php if (isset($_SESSION['user_login'])): ?><div class="dropdown"><button type="button" class="btn store-account dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person" aria-hidden="true"></i><span class="d-none d-lg-inline"><?= storeEscape($_SESSION['user_login']['ho_ten'] ?: $_SESSION['user_login']['username']) ?></span></button><div class="dropdown-menu dropdown-menu-end p-3"><span class="dropdown-item-text small text-muted mb-2"><?= storeEscape($_SESSION['user_login']['username']) ?></span><form method="post" action="index.php?act=logout"><input type="hidden" name="csrf_token" value="<?= storeEscape($_SESSION['front_csrf'] ?? '') ?>"><button class="btn btn-outline-secondary w-100 btn-sm">Đăng xuất</button></form></div></div>
<?php else: ?><a class="store-account" href="index.php?act=login"><i class="bi bi-person" aria-hidden="true"></i><span class="d-none d-lg-inline">Tài khoản</span></a><?php endif; ?>
<a href="index.php?act=cart" class="store-cart" aria-label="Giỏ hàng, <?= (int)$cartCount ?> sản phẩm"><i class="bi bi-cart3" aria-hidden="true"></i><span class="cart-count"><?= (int)$cartCount ?></span><span class="d-none d-lg-inline">Giỏ hàng</span></a>
</div>
</div>
<nav class="navbar navbar-expand-md navbar-light store-nav" aria-label="Điều hướng cửa hàng"><div class="container"><button class="navbar-toggler my-2" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavigation" aria-controls="storeNavigation" aria-expanded="false" aria-label="Mở menu"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="storeNavigation"><ul class="navbar-nav gap-md-4">
<?php foreach (['/'=>'Trang chủ','shop'=>'Tất cả đồ chơi','about'=>'Về Bé Vui','contact'=>'Liên hệ','check_order'=>'Theo dõi đơn hàng'] as $route=>$label): $active = $storeAct === $route || ($route==='shop' && $storeAct==='detail') || ($route==='check_order' && $storeAct==='history_detail'); ?><li class="nav-item"><a class="nav-link <?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?> href="<?= $route==='/' ? 'index.php' : 'index.php?act='.$route ?>"><?= $label ?></a></li><?php endforeach; ?>
</ul><span class="ms-auto small text-muted d-none d-lg-block"><i class="bi bi-heart" aria-hidden="true"></i> Cùng bé lớn lên qua từng trò chơi</span></div></div></nav>
</header>
<main id="store-main" class="flex-grow-1">
