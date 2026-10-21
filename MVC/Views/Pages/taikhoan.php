<?php
$rows=$payload["rows"] ?? null;
$edit=$payload["edit"] ?? null;
$nvs =$payload["nvs"] ?? null;
$khs =$payload["khs"] ?? null;
$msg=$payload["msg"] ?? null;
$msgType=$payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ TÀI KHOẢN (ADMIN)</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>TaiKhoan/Search">
    <input class="input" name="keyword" placeholder="Tìm username / email / role">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>TaiKhoan/Get_data">↩ Reset</a>
  </form>

  <form method="post" action="<?= BASE_URL ?>TaiKhoan/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
</div>

<?php if($edit): ?>
<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>TaiKhoan/Save">
    <div class="form-grid">
      <div class="full"><b>SỬA TÀI KHOẢN</b></div>

      <input type="hidden" name="matk" value="<?= htmlspecialchars($edit['matk'] ?? '') ?>">

      <div class="badge full">Username: <b><?= htmlspecialchars($edit['username']) ?></b></div>

      <input class="input full" name="email" placeholder="Email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>">

      <select class="select" name="role">
        <?php $roleSel = $edit['role'] ?? 'NHANVIEN'; ?>
        <option value="NHANVIEN" <?= $roleSel==='NHANVIEN'?'selected':'' ?>>NHANVIEN</option>
        <option value="ADMIN" <?= $roleSel==='ADMIN'?'selected':'' ?>>ADMIN</option>
        <option value="KHACHHANG" <?= $roleSel==='KHACHHANG'?'selected':'' ?>>KHACHHANG</option>
      </select>

      <div class="actions full">
        <button class="btn primary">💾 Lưu</button>
        <a class="btn" href="<?= BASE_URL ?>TaiKhoan/Get_data">Hủy</a>
      </div>
    </div>
  </form>

  <hr style="border:none;border-top:1px solid #f0f0f0;margin:14px 0">
  <form method="post" action="<?= BASE_URL ?>TaiKhoan/ResetPass/<?= (int)$edit['matk'] ?>" class="actions">
    <input class="input" type="password" name="newpass" placeholder="Mật khẩu mới (mặc định 123456)">
    <button class="btn">🔁 Reset mật khẩu</button>
  </form>
</div>
<?php endif; ?>

<table class="table">
  <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Locked</th><th>NV</th><th>KH</th><th>Thao tác</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= (int)$r['matk'] ?></td>
      <td><?= htmlspecialchars($r['username']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
<td><span class="badge"><?= htmlspecialchars($r['role']) ?></span></td>
      <td><?= ((int)$r['is_locked']===1) ? "<span class='badge' style='color:#b42318'>LOCK</span>" : "<span class='badge' style='color:#0f7a2b'>OK</span>" ?></td>
      <td><?= htmlspecialchars($r['ten_nv'] ?? '') ?></td>
      <td><?= htmlspecialchars($r['ten_kh'] ?? '') ?></td>
      <td class="actions">
        <a class="btn" href="<?= BASE_URL ?>TaiKhoan/Edit/<?= (int)$r['matk'] ?>">✏️</a>
        <?php if((int)$r['is_locked']===1): ?>
          <a class="btn" href="<?= BASE_URL ?>TaiKhoan/Unlock/<?= (int)$r['matk'] ?>">🔓</a>
        <?php else: ?>
          <a class="btn" href="<?= BASE_URL ?>TaiKhoan/Lock/<?= (int)$r['matk'] ?>">🔒</a>
        <?php endif; ?>
        <a class="btn danger" onclick="return confirm('Xóa tài khoản?')"
           href="<?= BASE_URL ?>TaiKhoan/Delete/<?= (int)$r['matk'] ?>">🗑</a>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="8" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>
