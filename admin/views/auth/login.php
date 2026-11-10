<?php require_once __DIR__ . '/../layouts/helpers.php'; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị</title>
    <link rel="stylesheet" href="views/assets/css/bootstrap.css">
    <link rel="stylesheet" href="views/assets/css/admin-basic.css?v=7">
</head>
<body class="bg-light">
<main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="admin-login">
        <div class="text-center mb-4"><h1 class="fw-bold mb-2">Quản trị cửa hàng đồ chơi</h1><p class="text-muted mb-0">Đăng nhập bằng tài khoản quản trị viên.</p></div>
        <div class="card"><div class="card-body p-4">
            <h2 class="mb-4">Đăng nhập</h2>
            <?php if (isset($error_login)): ?><div class="alert alert-danger" role="alert"><?= adminEscape($error_login) ?></div><?php endif; ?>
            <form action="index.php?action=checklogin" method="post">
                <div class="mb-3"><label for="username" class="form-label">Tên đăng nhập</label><input id="username" type="text" name="username" class="form-control" autocomplete="username" required autofocus value="<?= adminEscape(is_string($_POST['username'] ?? null) ? $_POST['username'] : '') ?>"></div>
                <div class="mb-4"><label for="password" class="form-label">Mật khẩu</label><input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required></div>
                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
        </div></div>
        <p class="text-center small mb-0"><a class="text-secondary" href="../index.php">Quay lại cửa hàng</a></p>
    </div>
</main>
</body>
</html>
