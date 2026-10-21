<?php
$rows=$payload["rows"] ?? null;
$edit=$payload["edit"] ?? null;
$msg=$payload["msg"] ?? null;
$msgType=$payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ NHÂN VIÊN (ADMIN)</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>NhanVien/Search">
    <input class="input" name="keyword" placeholder="Tìm họ tên / email / sđt / chức vụ">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>NhanVien/Get_data">↩ Reset</a>
  </form>

  <a class="btn primary" href="<?= BASE_URL ?>NhanVien/Get_data?add=1">➕ Thêm</a>

  <form method="post" action="<?= BASE_URL ?>NhanVien/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
  <form method="post" action="<?= BASE_URL ?>NhanVien/Import" enctype="multipart/form-data">
    <input class="input" type="file" name="file" accept=".csv,text/csv" required>
    <button class="btn">⬆ Nhập</button>
  </form>
</div>

<?php if(isset($_GET['add']) || isset($_GET['edit'])): ?>
<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>NhanVien/Save">
    <div class="form-grid">
      <div class="full"><b><?= $edit ? "SỬA NHÂN VIÊN" : "THÊM NHÂN VIÊN" ?></b></div>

      <input type="hidden" name="manv" value="<?= htmlspecialchars($edit['manv'] ?? '') ?>">

      <input class="input full" name="hoten" placeholder="Họ tên" value="<?= htmlspecialchars($edit['hoten'] ?? '') ?>" required>
      <input class="input" name="email" placeholder="Email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>">
      <input class="input" name="sdt" placeholder="SĐT" value="<?= htmlspecialchars($edit['sdt'] ?? '') ?>">
      <input class="input full" name="diachi" placeholder="Địa chỉ" value="<?= htmlspecialchars($edit['diachi'] ?? '') ?>">
      <input class="input full" name="chucvu" placeholder="Chức vụ" value="<?= htmlspecialchars($edit['chucvu'] ?? '') ?>">

      <?php if(!$edit): ?>
        <div class="full"><b>TẠO TÀI KHOẢN</b></div>
        <input class="input" name="username" placeholder="Username" required>
        <input class="input" type="password" name="password" placeholder="Password" required>
        <select class="select" name="role">
          <option value="NHANVIEN">Nhân viên</option>
          <option value="ADMIN">Admin</option>
        </select>
      <?php endif; ?>

      <div class="actions full">
        <button class="btn primary">💾 Lưu</button>
        <a class="btn" href="<?= BASE_URL ?>NhanVien/Get_data">Hủy</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<table class="table">
  <thead><tr><th>Mã</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Chức vụ</th><th>Username</th><th>Role</th><th>Thao tác</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= (int)$r['manv'] ?></td>
      <td><?= htmlspecialchars($r['hoten']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
      <td><?= htmlspecialchars($r['sdt']) ?></td>
      <td><?= htmlspecialchars($r['chucvu']) ?></td>
      <td><?= htmlspecialchars($r['username'] ?? '') ?></td>
      <td><span class="badge"><?= htmlspecialchars($r['role'] ?? '') ?></span></td>
      <td class="actions">
        <a class="btn" href="<?= BASE_URL ?>NhanVien/Edit/<?= (int)$r['manv'] ?>">✏️</a>
        <a class="btn danger" onclick="return confirm('Xóa nhân viên?')"
           href="<?= BASE_URL ?>NhanVien/Delete/<?= (int)$r['manv'] ?>">🗑</a>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="8" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>
