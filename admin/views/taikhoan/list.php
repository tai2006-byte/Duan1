<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1>Quản lý tài khoản</h1><p class="text-muted mb-0">Quản lý khách hàng và quản trị viên. Chỉ quản trị viên được gán hoặc đổi vai trò.</p></div><a href="index.php?action=createtaikhoan" class="btn btn-primary">Thêm tài khoản</a></div>
<?php if ($success): ?><div class="alert alert-success" role="status"><?= adminEscape($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= adminEscape($error) ?></div><?php endif; ?>
<div class="card"><div class="card-body"><div class="table-responsive">
    <table class="table table-striped table-hover align-middle" id="table1">
        <thead class="table-light"><tr><th scope="col">ID</th><th scope="col">Tên đăng nhập</th><th scope="col">Họ tên</th><th scope="col">Vai trò</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr></thead>
        <tbody><?php foreach ($accounts as $item): $active = (int)$item['is_active'] === 1; $self = (int)$item['id'] === (int)$_SESSION['admin_login']['id']; ?>
        <tr><td><?= (int)$item['id'] ?></td><td><?= adminEscape($item['username']) ?><?php if ($self): ?><span class="badge bg-light text-dark border ms-1">Bạn</span><?php endif; ?></td><td><?= adminEscape($item['ho_ten']) ?></td><td><?= adminEscape(AccountRole::labels()[$item['role']] ?? 'Không xác định') ?></td><td><span class="badge bg-<?= $active ? 'success' : 'secondary' ?>"><?= $active ? 'Đang hoạt động' : 'Đã khóa' ?></span></td><td class="admin-actions">
            <a class="btn btn-sm btn-outline-secondary" href="index.php?action=edittaikhoan&id=<?= (int)$item['id'] ?>">Sửa</a>
            <form class="d-inline" action="index.php?action=statustaikhoan" method="post" onsubmit="return confirm('<?= $active ? 'Khóa tài khoản này? Các phiên đăng nhập sẽ mất hiệu lực.' : 'Mở khóa tài khoản này?' ?>')">
                <input type="hidden" name="csrf_token" value="<?= adminEscape($csrfToken) ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><input type="hidden" name="is_active" value="<?= $active ? 0 : 1 ?>">
                <button type="submit" class="btn btn-sm btn-outline-<?= $active ? 'danger' : 'primary' ?>" <?= $self ? 'disabled title="Không thể khóa tài khoản đang đăng nhập"' : '' ?>><?= $active ? 'Khóa' : 'Mở khóa' ?></button>
            </form>
        </td></tr><?php endforeach; ?></tbody>
    </table>
    <?php if (!$accounts): ?><p class="text-center text-muted py-4">Chưa có tài khoản.</p><?php endif; ?>
</div></div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
