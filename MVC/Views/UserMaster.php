<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$page = $data["page"] ?? "shop";
$pageFile = "./MVC/Views/Pages/" . $page . ".php";
$payload = $data;

$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
$cartCount = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])){
  foreach($_SESSION['cart'] as $q) $cartCount += (int)$q;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý bán hàng - User</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/Master.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/pages.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/user.css">
</head>
<body>

<header class="u-top">
  <div class="u-top-inner">
    <a class="u-brand" href="<?= BASE_URL ?>Shop/Index">
      <img class="u-logo" src="<?= BASE_URL ?>Public/Images/logo.png" alt="logo" onerror="this.style.display='none'">
      <span class="u-brand-name">Shop</span>
    </a>

    <form class="u-search" method="get" action="<?= BASE_URL ?>Shop/Index">
      <input type="text" name="kw" placeholder="Tìm sản phẩm..." value="<?= htmlspecialchars($payload['kw'] ?? '') ?>">
      <button type="submit">Tìm</button>
    </form>

    <nav class="u-nav">
      <a class="u-link" href="<?= BASE_URL ?>Shop/Index">Sản phẩm</a>
      <a class="u-link" href="<?= BASE_URL ?>Cart/Index">Giỏ hàng <span class="u-badge"><?= $cartCount ?></span></a>
      <a class="u-link" href="<?= BASE_URL ?>Orders/Index">Đơn của tôi</a>

      <?php if($role==='KHACHHANG'): ?>
        <div class="u-user">
          <span class="u-user-name"><?= htmlspecialchars($username) ?></span>
          <a class="u-btn" href="<?= BASE_URL ?>Auth/Logout">Đăng xuất</a>
        </div>
      <?php else: ?>
        <a class="u-btn" href="<?= BASE_URL ?>Auth/Login">Đăng nhập</a>
        <a class="u-btn ghost" href="<?= BASE_URL ?>Auth/Register">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="u-main">
  <?php
    // flash
    if(!empty($_SESSION['flash_error'])){
      echo "<div class='alert error'>".htmlspecialchars($_SESSION['flash_error'])."</div>";
      unset($_SESSION['flash_error']);
    }
    if(!empty($_SESSION['flash_success'])){
      echo "<div class='alert success'>".htmlspecialchars($_SESSION['flash_success'])."</div>";
      unset($_SESSION['flash_success']);
    }

    if(file_exists($pageFile)) include $pageFile;
    else echo "<div class='notice'>Không tìm thấy trang</div>";
  ?>
</main>

</body>
</html>
