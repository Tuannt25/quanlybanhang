<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$username = $_SESSION['username'] ?? 'Guest';
$role = $_SESSION['role'] ?? '';

$page = $data["page"] ?? "thongke_ngaythang";
$pageFile = "./MVC/Views/Pages/" . $page . ".php";

// payload for pages
$payload = $data;

// active submenu?
$isThongKePage = in_array($page, ["thongke_ngaythang", "thongke_topbanchay"]);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý bán hàng</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/Master.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/pages.css">
</head>

<body>
<div class="layout">
  <aside id="sidebar" class="sidebar">
    <div class="sb-head">
      <div class="sb-brand">
        <img class="sb-logo" src="<?= BASE_URL ?>Public/Images/logo.png">
             
        <span class="sb-title" style="text-align:center;">MENU</span>
      </div>
      <button id="btnToggleSidebar" class="sb-toggle" type="button" title="Mở/Đóng sidebar">☰</button>
    </div>

    <table class="sb-table" cellspacing="0" cellpadding="0">
      <tbody>
        <tr><td>
          <a href="javascript:void(0)" class="sb-item sb-parent" id="btnThongKe">
            <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/dashboard.png" alt="">
            <span class="sb-text">Thống kê</span>
            <span class="sb-caret" id="thongkeCaret"><?= $isThongKePage ? '▴' : '▾' ?></span>
          </a>
        </td></tr>

        <tr class="sb-subrow <?= $isThongKePage ? '' : 'hidden' ?>" id="tkRow1"><td>
          <a class="sb-item sb-sub" href="<?= BASE_URL ?>ThongKe/NgayThang">
            <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/calendar.png" alt="">
            <span class="sb-text">Thống kê ngày/tháng</span>
          </a>
        </td></tr>

        <tr class="sb-subrow <?= $isThongKePage ? '' : 'hidden' ?>" id="tkRow2"><td>
          <a class="sb-item sb-sub" href="<?= BASE_URL ?>ThongKe/TopBanChay">
            <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/top.png" alt="">
            <span class="sb-text">Top bán chạy</span>
          </a>
        </td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>SanPham/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/product.png" alt=""><span class="sb-text">Sản phẩm</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>KhoHang/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/warehouse.png" alt=""><span class="sb-text">Kho hàng</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>DanhMuc/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/category.png" alt=""><span class="sb-text">Danh mục</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>KhachHang/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/customer.png" alt=""><span class="sb-text">Khách hàng</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>QuanLyDonHang/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/orders.png" alt=""><span class="sb-text">Quản lý đơn hàng</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>TaoDonHang/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/create_order.png" alt=""><span class="sb-text">Tạo đơn hàng</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>ChiTietDonHang/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/order_detail.png" alt=""><span class="sb-text">Chi tiết đơn hàng</span>
        </a></td></tr>

        <?php if($role==='ADMIN'): ?>
        <tr><td><a class="sb-item" href="<?= BASE_URL ?>NhanVien/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/staff.png" alt=""><span class="sb-text">Nhân viên</span>
        </a></td></tr>

        <tr><td><a class="sb-item" href="<?= BASE_URL ?>TaiKhoan/Get_data">
          <img class="sb-icon" src="<?= BASE_URL ?>Public/Images/account.png" alt=""><span class="sb-text">Tài khoản</span>
        </a></td></tr>
        <?php endif; ?>

      </tbody>
    </table>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="topbar-center">
        <img class="topbar-logo" src="<?= BASE_URL ?>Public/Images/logo.png" alt="logo">
             
        <span class="topbar-title">Quản lý bán hàng</span>
      </div>

      <div class="userbox">
        <button class="userbtn" id="userBtn" type="button">
          <span class="user-name"><?= htmlspecialchars($username) ?></span>
          <span class="user-caret">▾</span>
        </button>

        <div class="user-dropdown" id="userDropdown">
          <a class="dropdown-item" href="<?= BASE_URL ?>DangNhap/Logout">Thoát</a>
        </div>
      </div>
    </header>

    <section class="content">
      <?php
      if (file_exists($pageFile)) {
        include_once $pageFile;
      } else {
        echo "<div class='notice error'>Không tìm thấy trang: <b>".htmlspecialchars($page)."</b></div>";
      }
      ?>
    </section>
  </main>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  document.getElementById('btnToggleSidebar').addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
  });

  const btnThongKe = document.getElementById('btnThongKe');
  const tkRow1 = document.getElementById('tkRow1');
  const tkRow2 = document.getElementById('tkRow2');
  const thongkeCaret = document.getElementById('thongkeCaret');

  btnThongKe.addEventListener('click', () => {
    tkRow1.classList.toggle('hidden');
    tkRow2.classList.toggle('hidden');
    const opened = !tkRow1.classList.contains('hidden');
    thongkeCaret.textContent = opened ? '▴' : '▾';
  });

  const userBtn = document.getElementById('userBtn');
  const userDropdown = document.getElementById('userDropdown');
  userBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('open');
  });
  document.addEventListener('click', () => userDropdown.classList.remove('open'));
</script>

</body>
</html>
