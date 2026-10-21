<?php
$rows = $payload["rows"] ?? null;//dsach từ DB
$dms = $payload["dms"] ?? null;//danhmuc trong selectbox
$edit = $payload["edit"] ?? null;//dsach đổ lên khi ấn sửa
$msg = $payload["msg"] ?? null;//NDung thông báo
$msgType = $payload["msgType"] ?? null;//kiểu thông báo
?>
<h2 class="page-title">QUẢN LÝ SẢN PHẨM</h2>
<!-- //Nếu có thông báo mới hiện ra thì ngăn chặn việc thực thi mã script lạ từ nội dung thông báo -->
<?php if ($msg): ?>
  <div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>SanPham/Search">
    <input class="input" name="keyword" placeholder="Tìm mã / tên / xuất xứ">
    <select class="select" name="madm">
      <option value="ALL">Tất cả danh mục</option>
      <!-- //vòng lặp duyệt danh mục tạo lựa chọn trong dropdown -->
      <?php if ($dms):
        while ($dm = mysqli_fetch_assoc($dms)): ?>
          <option value="<?= htmlspecialchars($dm['madm']) ?>"><?= htmlspecialchars($dm['tendm']) ?></option>
        <?php endwhile; endif; ?>
    </select>
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>SanPham/Get_data">↩ Reset</a>
  </form>

  <a class="btn primary" href="<?= BASE_URL ?>SanPham/Get_data?add=1">➕Thêm</a>

  <form method="post" action="<?= BASE_URL ?>SanPham/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
  <form method="post" action="<?= BASE_URL ?>SanPham/Import" enctype="multipart/form-data">
    <input class="input" type="file" name="file" accept=".csv,text/csv" required>
    <button class="btn">⬆ Nhập</button>
  </form>
</div>

<?php if (isset($_GET['add']) || isset($_GET['edit'])): ?>
  <div class="form-card">
    <form method="post" action="<?= BASE_URL ?>SanPham/Save" enctype="multipart/form-data">
      <div class="form-grid">
        <div class="full"><b><?= $edit ? "SỬA SẢN PHẨM" : "THÊM SẢN PHẨM" ?></b></div>

        <input type="hidden" name="masp" value="<?= htmlspecialchars($edit['masp'] ?? '') ?>">
        <?php if (!$edit): ?>
          <input class="input" name="new_masp" placeholder="Mã sản phẩm" required>
        <?php endif; ?>
        <!-- //nếu có dữ liệu cũ thì hiện lên ko thì để trống -->
        <input class="input full" name="tensp" placeholder="Tên sản phẩm"
          value="<?= htmlspecialchars($edit['tensp'] ?? '') ?>" required>
        <input class="input" name="gia" type="number" step="0.01" placeholder="Giá"
          value="<?= htmlspecialchars($edit['gia'] ?? '') ?>" required>
        <input class="input" name="soluong" type="number" placeholder="Số lượng"
          value="<?= htmlspecialchars($edit['soluong'] ?? '') ?>" required>
          <input class="input" name ="xuatxu" placeholder="Xuất xứ"
          value="<?=htmlspecialchars($edit['xuatxu']??'')?>"required>
        <select class="select" name="madm" required>
          <?php
          $spm = new SanPhamModel();
          $dms2 = $spm->listDanhMuc();
          while ($dm = mysqli_fetch_assoc($dms2)):
            $sel = (($edit['madm'] ?? '') === $dm['madm']) ? "selected" : "";
            ?>
            <option value="<?= htmlspecialchars($dm['madm']) ?>" <?= $sel ?>><?= htmlspecialchars($dm['tendm']) ?></option>
          <?php endwhile; ?>
        </select>

        <input class="input full" type="file" name="hinhanh"   accept=".jpg,.jpeg,.png,.webp">

        <div class="actions full">
          <button class="btn primary">💾 Lưu</button>
          <a class="btn" href="<?= BASE_URL ?>SanPham/Get_data">Hủy</a>
        </div>
      </div>
    </form>
  </div>
<?php endif; ?>

<table class="table">
  <thead>
    <tr>
      <th>Mã</th>
      <th>Tên</th>
      <th>Giá</th>
      <th>SL</th>
      <th>Danh mục</th>
      <th>Ảnh</th>
      <th>Xuất xứ</th>
      <th>Thao tác</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows && mysqli_num_rows($rows) > 0):
      while ($r = mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><?= htmlspecialchars($r['masp']) ?></td>
          <td><?= htmlspecialchars($r['tensp']) ?></td>
          <td><?= number_format((float) $r['gia']) ?></td>
          <td><?= (int) $r['soluong'] ?></td>
          <td><?= htmlspecialchars($r['tendm']) ?></td>
          <td>
            <?php if (!empty($r['hinhanh'])): ?>
              <img src="<?= BASE_URL ?>Public/Picture/<?= htmlspecialchars($r['hinhanh']) ?>"
                style="width:48px;height:48px;border-radius:14px;object-fit:cover">
            <?php else: ?>
              <span class="badge">No img</span>            
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['xuatxu'] ?? '') ?></td>
          <td class="actions">
            <a class="btn" href="<?= BASE_URL ?>SanPham/Edit/<?= urlencode($r['masp']) ?>">✏️ Sửa</a>
            <a class="btn danger" onclick="return confirm('Xóa sản phẩm?')"
              href="<?= BASE_URL ?>SanPham/Delete/<?= urlencode($r['masp']) ?>">🗑 Xóa</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
      <tr>
        <td colspan="8" style="text-align:center">Không có dữ liệu</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
