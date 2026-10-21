<?php
$rows=$payload["rows"] ?? null;
$edit=$payload["edit"] ?? null;
$msg=$payload["msg"] ?? null;
$msgType=$payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ KHÁCH HÀNG</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>KhachHang/Search">
    <input class="input" name="keyword" placeholder="Tìm tên, email, SĐT">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>KhachHang/Get_data">↩ Reset</a>
  </form>

  <a class="btn primary" href="<?= BASE_URL ?>KhachHang/Get_data?add=1">➕ Thêm</a>

  <form method="post" action="<?= BASE_URL ?>KhachHang/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
  <form method="post" action="<?= BASE_URL ?>KhachHang/Import" enctype="multipart/form-data">
    <input class="input" type="file" name="file" accept=".csv,text/csv" required>
    <button class="btn">⬆ Nhập</button>
  </form>
</div>

<?php if(isset($_GET['add']) || isset($_GET['edit'])): ?>
<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>KhachHang/Save">
    <div class="form-grid">
      <div class="full"><b><?= $edit ? "SỬA KHÁCH HÀNG" : "THÊM KHÁCH HÀNG" ?></b></div>

      <input type="hidden" name="makh" value="<?= htmlspecialchars($edit['makh'] ?? '') ?>">
      <?php if(!$edit): ?>
        <input class="input" name="new_makh" placeholder="Mã khách hàng" required>
      <?php endif; ?>

      <input class="input full" name="tenkh" placeholder="Tên khách hàng" value="<?= htmlspecialchars($edit['tenkh'] ?? '') ?>" required>
      <input class="input" name="email" placeholder="Email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>">
      <input class="input" name="sdt" placeholder="SĐT" value="<?= htmlspecialchars($edit['sdt'] ?? '') ?>">
      <input class="input full" name="diachi" placeholder="Địa chỉ" value="<?= htmlspecialchars($edit['diachi'] ?? '') ?>">

      <div class="actions full">
        <button class="btn primary">💾 Lưu</button>
        <a class="btn" href="<?= BASE_URL ?>KhachHang/Get_data">Hủy</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<table class="table">
  <thead><tr><th>Mã KH</th><th>Tên</th><th>Email</th><th>SĐT</th><th>Địa chỉ</th><th>Thao tác</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= htmlspecialchars($r['makh']) ?></td>
      <td><?= htmlspecialchars($r['tenkh']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
      <td><?= htmlspecialchars($r['sdt']) ?></td>
      <td><?= htmlspecialchars($r['diachi']) ?></td>
      <td class="actions">
        <a class="btn" href="<?= BASE_URL ?>KhachHang/Edit/<?= urlencode($r['makh']) ?>">✏️ Sửa</a>
        <a class="btn danger" onclick="return confirm('Xóa khách hàng?')"
           href="<?= BASE_URL ?>KhachHang/Delete/<?= urlencode($r['makh']) ?>">🗑 Xóa</a>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="6" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>
