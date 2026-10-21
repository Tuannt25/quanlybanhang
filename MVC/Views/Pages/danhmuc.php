<?php
$rows = $payload["rows"] ?? null;
$edit = $payload["edit"] ?? null;
$msg  = $payload["msg"] ?? null;
$msgType = $payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ DANH MỤC</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>DanhMuc/Search">
    <input class="input" name="keyword" placeholder="Tìm mã / tên danh mục">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>DanhMuc/Get_data">↩ Reset</a>
  </form>

  <a class="btn primary" href="<?= BASE_URL ?>DanhMuc/Get_data?add=1">➕ Thêm</a>

  <form method="post" action="<?= BASE_URL ?>DanhMuc/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
  <form method="post" action="<?= BASE_URL ?>DanhMuc/Import" enctype="multipart/form-data">
    <input class="input" type="file" name="file" accept=".csv,text/csv" required>
    <button class="btn">⬆ Nhập</button>
  </form>
</div>

<?php if(isset($_GET['add']) || isset($_GET['edit'])): ?>
<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>DanhMuc/Save">
    <div class="form-grid">
      <div class="full"><b><?= $edit ? "SỬA DANH MỤC" : "THÊM DANH MỤC" ?></b></div>

      <input type="hidden" name="madm" value="<?= htmlspecialchars($edit['madm'] ?? '') ?>">
      <?php if(!$edit): ?>
        <input class="input" name="new_madm" placeholder="Mã danh mục" required>
      <?php endif; ?>
      <input class="input full" name="tendm" placeholder="Tên danh mục" value="<?= htmlspecialchars($edit['tendm'] ?? '') ?>" required>

      <div class="actions full">
        <button class="btn primary">💾 Lưu</button>
        <a class="btn" href="<?= BASE_URL ?>DanhMuc/Get_data">Hủy</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<table class="table">
  <thead><tr><th>Mã DM</th><th>Tên DM</th><th>Thao tác</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= htmlspecialchars($r['madm']) ?></td>
      <td><?= htmlspecialchars($r['tendm']) ?></td>
      <td class="actions">
        <a class="btn" href="<?= BASE_URL ?>DanhMuc/Edit/<?= urlencode($r['madm']) ?>">✏️ Sửa</a>
        <a class="btn danger" onclick="return confirm('Xóa danh mục?')"
           href="<?= BASE_URL ?>DanhMuc/Delete/<?= urlencode($r['madm']) ?>">🗑 Xóa</a>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="3" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>
