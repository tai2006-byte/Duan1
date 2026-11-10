<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0">Quản lý sản phẩm</h1><a href="index.php?action=createsanpham" class="btn btn-primary">Thêm sản phẩm</a></div>
<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table1">
<thead class="table-light"><tr><th scope="col">ID</th><th scope="col">Ảnh</th><th scope="col">Sản phẩm</th><th scope="col">Danh mục</th><th scope="col">Giá bán</th><th scope="col">Tồn kho</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr></thead>
<tbody><?php foreach ($allSanPham as $sp): $visible = (int)$sp['trang_thai'] === 1; ?>
<tr><td><?= (int)$sp['id_sp'] ?></td><td><?php if ($sp['hinh_anh']): ?><img class="admin-thumbnail" src="image/<?= adminEscape($sp['hinh_anh']) ?>" alt="<?= adminEscape($sp['ten_sp']) ?>" loading="lazy"><?php else: ?><span class="text-muted small">Chưa có ảnh</span><?php endif; ?></td><td><?= adminEscape($sp['ten_sp']) ?></td><td><?= adminEscape($sp['name_danh_muc'] ?? 'Chưa phân loại') ?></td><td class="text-nowrap"><?= number_format($sp['gia_sp'], 0, ',', '.') ?> đ</td><td><?= (int)$sp['so_luong'] ?></td><td><span class="badge bg-<?= $visible ? 'success' : 'secondary' ?>"><?= $visible ? 'Đang bán' : 'Đã ẩn' ?></span></td>
<td class="admin-actions"><a class="btn btn-sm btn-outline-secondary" href="index.php?action=editsanpham&id=<?= (int)$sp['id_sp'] ?>">Sửa</a><a class="btn btn-sm btn-outline-<?= $visible ? 'danger' : 'primary' ?>" href="index.php?action=<?= $visible ? 'deletesanpham' : 'restoresanpham' ?>&id=<?= (int)$sp['id_sp'] ?>" onclick="return confirm('<?= $visible ? 'Ẩn sản phẩm này?' : 'Khôi phục sản phẩm này?' ?>')"><?= $visible ? 'Ẩn' : 'Khôi phục' ?></a></td></tr>
<?php endforeach; ?></tbody>
</table>
<?php if (!$allSanPham): ?><p class="text-muted text-center py-4 mb-0">Chưa có sản phẩm. Hãy thêm sản phẩm đầu tiên.</p><?php endif; ?>
</div></div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
