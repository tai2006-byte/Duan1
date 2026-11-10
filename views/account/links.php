<?php if (isset($_SESSION['user_login'])): ?>
    <span class="mr-2">Xin chào, <?= htmlspecialchars($_SESSION['user_login']['ho_ten'] ?? $_SESSION['user_login']['username'], ENT_QUOTES, 'UTF-8') ?></span>
    <form class="d-inline" method="post" action="index.php?act=logout"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['front_csrf'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-sm btn-outline-secondary" type="submit">Đăng xuất</button></form>
<?php else: ?>
    <a href="index.php?act=login" class="mr-3">Đăng nhập</a><a href="index.php?act=register">Đăng ký</a>
<?php endif; ?>
