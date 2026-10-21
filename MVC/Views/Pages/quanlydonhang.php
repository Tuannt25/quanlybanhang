<?php
$rows=$payload["rows"] ?? null;
$msg=$payload["msg"] ?? null;
$msgType=$payload["msgType"] ?? null;
?>
<h2 class="page-title">QUẢN LÝ ĐƠN HÀNG</h2>

<?php if($msg): ?><div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>QuanLyDonHang/Search">
    <input class="input" name="keyword" placeholder="Tìm mã đơn / tên KH / trạng thái">
    <button class="btn primary">🔍 Tìm</button>
    <a class="btn" href="<?= BASE_URL ?>QuanLyDonHang/Get_data">↩ Reset</a>
  </form>

  <form method="post" action="<?= BASE_URL ?>QuanLyDonHang/Export" style="margin-left:auto">
    <button class="btn">⬇ Xuất</button>
  </form>
</div>

<table class="table">
  <thead><tr><th>Mã đơn</th><th>Khách</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Chi tiết</th><th>Thao tác</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><b><?= htmlspecialchars($r['madh']) ?></b></td>
      <td><?= htmlspecialchars($r['tenkh']) ?><div class="muted"><?= htmlspecialchars($r['makh']) ?></div></td>
      <td><?= htmlspecialchars($r['ngaydat']) ?></td>
      <td><?= number_format((float)$r['tongtien']) ?> đ</td>
      <td>
        <form method="post" action="<?= BASE_URL ?>QuanLyDonHang/UpdateStatus" class="actions">
          <input type="hidden" name="madh" value="<?= htmlspecialchars($r['madh']) ?>">
          <select class="select" name="trangthai">
            <?php
              $st = $r['trangthai'] ?? 'Chờ xử lý';
              $opts = ['Chờ xử lý','Hoàn thành','Huỷ'];
              foreach($opts as $o){
                $sel = ($st===$o) ? "selected" : "";
                echo "<option value='".htmlspecialchars($o)."' $sel>".htmlspecialchars($o)."</option>";
              }
            ?>
          </select>
          <button class="btn primary">Lưu</button>
        </form>
      </td>
      <td><a class="btn" href="<?= BASE_URL ?>ChiTietDonHang/ByDon/<?= urlencode($r['madh']) ?>">👁 Xem</a></td>
      <td class="actions">
        <a class="btn danger" onclick="return confirm('Xóa đơn hàng?')"
           href="<?= BASE_URL ?>QuanLyDonHang/Delete/<?= urlencode($r['madh']) ?>">🗑 Xóa</a>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="7" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>

