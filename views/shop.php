<?php
include __DIR__ . '/layout/header.php';
$pageTitle='Cửa hàng đồ chơi'; $pageDescription='Khám phá sản phẩm và tìm món quà nhỏ cho giờ chơi của bé.';
include __DIR__ . '/partials/page-heading.php';
$selectedPrice=is_string($_GET['price']??null)?$_GET['price']:'';
$baseQuery=['act'=>'shop','iddm'=>$categoryId,'keyword'=>$keyword,'price'=>$selectedPrice];
?>
<div class="container pb-4"><div class="row g-4"><aside class="col-lg-3"><div class="store-panel store-filter"><h2 class="mb-3">Tìm món bé thích</h2><form action="index.php" method="get"><input type="hidden" name="act" value="shop">
<div class="mb-3"><label for="filter-keyword" class="form-label">Từ khóa</label><input id="filter-keyword" name="keyword" class="form-control" value="<?= storeEscape($keyword) ?>" placeholder="Tên sản phẩm..."></div>
<div class="mb-3"><label for="filter-category" class="form-label">Danh mục</label><select id="filter-category" name="iddm" class="form-select"><option value="0">Tất cả danh mục</option><?php foreach($categories as $category): ?><option value="<?= (int)$category['id_danh_muc'] ?>" <?= (int)$categoryId===(int)$category['id_danh_muc']?'selected':'' ?>><?= storeEscape($category['name_danh_muc']) ?></option><?php endforeach; ?></select></div>
<div class="mb-4"><label for="filter-price" class="form-label">Khoảng giá</label><select id="filter-price" name="price" class="form-select"><?php foreach([''=>'Tất cả mức giá','0-100000'=>'Dưới 100.000 đ','100000-300000'=>'100.000 – 300.000 đ','300000-500000'=>'300.000 – 500.000 đ','500000-1000000'=>'500.000 – 1.000.000 đ','1000000-99999999'=>'Trên 1.000.000 đ'] as $value=>$label): ?><option value="<?= $value ?>" <?= $selectedPrice===$value?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></div>
<button class="btn btn-primary w-100">Áp dụng bộ lọc</button><a href="index.php?act=shop" class="d-block text-center small mt-3">Xóa bộ lọc</a></form></div></aside>
<section class="col-lg-9"><div class="d-flex justify-content-between align-items-center gap-3 mb-3"><span class="text-muted small">Tìm thấy <strong><?= (int)$tong_so_san_pham ?></strong> sản phẩm</span><span class="small text-muted">Trang <?= (int)$trang_hien_tai ?> / <?= (int)$tong_so_trang ?></span></div>
<div class="row g-3 g-xl-4"><?php foreach($allProducts as $product): ?><div class="col-6 col-md-4"><?php include __DIR__ . '/partials/product-card.php'; ?></div><?php endforeach; ?></div>
<?php if(!$allProducts): ?><div class="store-empty"><i class="bi bi-search" aria-hidden="true"></i><h2>Chưa tìm thấy sản phẩm phù hợp</h2><p>Thử từ khóa khác hoặc điều chỉnh bộ lọc để xem thêm sản phẩm.</p><a href="index.php?act=shop" class="btn btn-primary">Xem tất cả sản phẩm</a></div><?php endif; ?>
<?php if($tong_so_trang>1): ?><nav class="mt-4" aria-label="Phân trang sản phẩm"><ul class="pagination flex-wrap justify-content-center gap-1">
<?php $pages=array_unique(array_merge([1],range(max(1,$trang_hien_tai-2),min($tong_so_trang,$trang_hien_tai+2)),[$tong_so_trang])); $previousPage=0; foreach($pages as $number): if($previousPage && $number>$previousPage+1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?><li class="page-item <?= $number===$trang_hien_tai?'active':'' ?>"><a class="page-link" <?= $number===$trang_hien_tai?'aria-current="page"':'' ?> href="<?= storeEscape('index.php?'.http_build_query(array_merge($baseQuery,['page'=>$number]))) ?>"><?= $number ?></a></li><?php $previousPage=$number; endforeach; ?></ul></nav><?php endif; ?>
</section></div></div>
<?php include __DIR__ . '/layout/footer.php'; ?>
