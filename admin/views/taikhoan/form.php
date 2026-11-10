<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0"><?= $editing ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></h1><a href="index.php?action=listtaikhoan" class="btn btn-outline-secondary">Quay lại</a></div>
<div class="card admin-form"><div class="card-body">
    <?php if (isset($error)): ?><div class="alert alert-danger" role="alert"><?= adminEscape($error) ?></div><?php endif; ?>
    <form method="post" action="index.php?action=<?= $editing ? 'updatetaikhoan' : 'storetaikhoan' ?>">
        <input type="hidden" name="csrf_token" value="<?= adminEscape($csrfToken) ?>">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$account['id'] ?>"><?php endif; ?>
        <div class="mb-3"><label for="account-name" class="form-label">Họ tên <span class="text-danger">*</span></label><input id="account-name" name="ho_ten" class="form-control" required maxlength="100" autocomplete="name" value="<?= adminEscape($account['ho_ten'] ?? '') ?>"></div>
        <div class="mb-3"><label for="account-username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label><input id="account-username" name="username" class="form-control" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_.\-]{3,50}" autocomplete="off" aria-describedby="username-help" value="<?= adminEscape($account['username'] ?? '') ?>"><div id="username-help" class="form-text">3–50 ký tự: chữ không dấu, số, dấu chấm, gạch dưới hoặc gạch ngang.</div></div>
        <div class="mb-3"><label for="account-role" class="form-label">Vai trò <span class="text-danger">*</span></label><select id="account-role" name="role" class="form-select" required>
            <?php foreach (AccountRole::labels() as $value => $label): ?><option value="<?= adminEscape($value) ?>" <?= ($account['role'] ?? AccountRole::CUSTOMER) === $value ? 'selected' : '' ?>><?= adminEscape($label) ?></option><?php endforeach; ?>
        </select><div class="form-text">Chỉ quản trị viên được gán hoặc thay đổi vai trò. Không thể đổi vai trò tài khoản đang đăng nhập.</div></div>
        <div class="mb-4"><label for="account-password" class="form-label"><?= $editing ? 'Mật khẩu mới' : 'Mật khẩu' ?><?= !$editing ? ' *' : '' ?></label><input id="account-password" type="password" name="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" <?= !$editing ? 'required' : '' ?> aria-describedby="password-help"><div id="password-help" class="form-text">Ít nhất 8 ký tự. <?= $editing ? 'Bỏ trống để giữ mật khẩu hiện tại. Khi đổi mật khẩu, các phiên đăng nhập khác sẽ mất hiệu lực.' : 'Tài khoản mới được kích hoạt ngay sau khi thêm.' ?></div></div>
        <div class="d-flex gap-2"><button class="btn btn-primary" type="submit"><?= $editing ? 'Lưu thay đổi' : 'Thêm tài khoản' ?></button><a class="btn btn-outline-secondary" href="index.php?action=listtaikhoan">Hủy</a></div>
    </form>
</div></div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
