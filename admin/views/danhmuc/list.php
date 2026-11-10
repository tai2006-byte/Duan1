<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0">Quản lý danh mục</h1><a href="index.php?action=createdanhmuc" class="btn btn-primary">Thêm danh mục</a></div>
<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table1">
    <thead class="table-light"><tr><th scope="col">ID</th><th scope="col">Tên danh mục</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr></thead>
    <tbody><?php foreach ($allDanhMuc as $item): $visible = (int)$item['Trangthai'] === 1; ?>
    <tr><td><?= (int)$item['id_danh_muc'] ?></td><td><?= adminEscape($item['name_danh_muc']) ?></td><td><span class="badge bg-<?= $visible ? 'success' : 'secondary' ?>"><?= $visible ? 'Đang hiển thị' : 'Đã ẩn' ?></span></td>
        <td class="admin-actions"><a class="btn btn-sm btn-outline-secondary" href="index.php?action=editdanhmuc&id=<?= (int)$item['id_danh_muc'] ?>">Sửa</a>
        <a class="btn btn-sm btn-outline-<?= $visible ? 'danger' : 'primary' ?>" href="index.php?action=<?= $visible ? 'deletedanhmuc' : 'restoredanhmuc' ?>&id=<?= (int)$item['id_danh_muc'] ?>" onclick="return confirm('<?= $visible ? 'Ẩn danh mục này?' : 'Khôi phục danh mục này?' ?>')"><?= $visible ? 'Ẩn' : 'Khôi phục' ?></a></td></tr>
    <?php endforeach; ?></tbody>
</table>
<?php if (!$allDanhMuc): ?><p class="text-muted text-center py-4 mb-0">Chưa có danh mục. Hãy thêm danh mục đầu tiên.</p><?php endif; ?>
</div></div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
