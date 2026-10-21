<?php
$rows=$payload["rows"] ?? null;
$msg=$payload["msg"] ?? null;
$msgType=$payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ KHO HÀNG</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>KhoHang/Search">
    <input class="input" name="keyword" placeholder="Tìm mã / tên sản phẩm">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>KhoHang/Get_data">↩ Reset</a>
  </form>

  <form method="post" action="<?= BASE_URL ?>KhoHang/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
  <form method="post" action="<?= BASE_URL ?>KhoHang/Import" enctype="multipart/form-data">
    <input class="input" type="file" name="file" accept=".csv,text/csv" required>
    <button class="btn">⬆ Nhập</button>
  </form>
</div>

<table class="table">
  <thead>
    <tr><th>Mã</th><th>Tên</th><th>Giá</th><th>Tồn</th><th>Cảnh báo</th><th>Thêm hàng</th></tr>
  </thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= htmlspecialchars($r['masp']) ?></td>
      <td><?= htmlspecialchars($r['tensp']) ?></td>
      <td><?= number_format((float)$r['gia']) ?></td>
      <td><span class="badge"><?= (int)$r['soluong'] ?></span></td>
      <td>
        <?php if((int)$r['soluong']<=0): ?>
          <span class="badge" style="color:#b42318">Hết hàng</span>
        <?php elseif((int)$r['soluong']<=5): ?>
          <span class="badge" style="color:#ff7a18">Sắp hết</span>
        <?php else: ?>
          <span class="badge" style="color:#0f7a2b">OK</span>
        <?php endif; ?>
      </td>
      <td>
        <form method="post" action="<?= BASE_URL ?>KhoHang/UpdateQty" class="actions">
          <input type="hidden" name="masp" value="<?= htmlspecialchars($r['masp']) ?>">
         <input class="input" style="min-width:80px" type="number" name="soluong" value="1" placeholder="Nhập số lượng thêm">
          <button class="btn primary">Lưu</button>
        </form>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="6" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>
