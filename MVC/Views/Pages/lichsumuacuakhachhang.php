<?php
$rows=$payload["rows"] ?? null;
?>
<h2 class="page-title">LỊCH SỬ MUA HÀNG</h2>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>LichSuMua/Search">
    <input class="input" name="keyword" placeholder="Tìm mã đơn / tên KH / trạng thái">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>LichSuMua/Get_data">↩ Reset</a>
  </form>

  <a class="btn" style="margin-left:auto" href="<?= BASE_URL ?>LichSuMua/Export">⬇ Xuất</a>
</div>

<table class="table">
  <thead><tr><th>Mã đơn</th><th>Khách</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Chi tiết</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= htmlspecialchars($r['madh']) ?></td>
      <td><?= htmlspecialchars($r['tenkh']) ?><div class="muted"><?= htmlspecialchars($r['makh']) ?></div></td>
      <td><?= htmlspecialchars($r['ngaydat']) ?></td>
      <td><?= number_format((float)$r['tongtien']) ?> đ</td>
      <td><span class="badge"><?= htmlspecialchars($r['trangthai']) ?></span></td>
      <td><a class="btn" href="<?= BASE_URL ?>ChiTietDonHang/ByDon/<?= urlencode($r['madh']) ?>">👁 Xem</a></td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="6" style="text-align:center">Không có đơn hàng</td></tr>
  <?php endif; ?>
  </tbody>
</table>

