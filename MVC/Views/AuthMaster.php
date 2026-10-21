<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$page = $data["page"] ?? "dangnhap";
$pageFile = "./MVC/Views/Pages/" . $page . ".php";
$payload = $data;
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Xác thực - Quản lý bán hàng</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/auth.css">
</head>
<body class="auth-body">
  <?php if(!empty($_SESSION['flash_error'])): ?>
    <div class="auth-msg error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>
  <?php if(!empty($_SESSION['flash_success'])): ?>
    <div class="auth-msg success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>
  <?php if(!empty($payload["msg"])): ?>
    <div class="auth-msg <?= htmlspecialchars($payload["msgType"] ?? "") ?>">
      <?= htmlspecialchars($payload["msg"]) ?>
    </div>
  <?php endif; ?>

  <?php
  if (file_exists($pageFile)) include_once $pageFile;
  else echo "<div class='auth-msg error'>Không tìm thấy trang</div>";
  ?>
</body>
</html>
