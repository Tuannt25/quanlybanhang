<?php
$rows=$payload["rows"] ?? null;
$madh=$payload["madh"] ?? '';
?>
<h2 class="page-title">CHI TIẾT ĐƠN HÀNG</h2>

<div class="toolbar">
  <form method="post" action="<?= BASE_URL ?>ChiTietDonHang/Search">
    <input class="input" name="madh" placeholder="Nhập mã đơn" value="<?= htmlspecialchars($madh) ?>">
    <button class="btn primary">🔍 Xem</button>
  </form>

  <?php if($madh): ?>
    <a class="btn" style="margin-left:auto" href="<?= BASE_URL ?>ChiTietDonHang/Export?madh=<?= urlencode($madh) ?>">⬇ Xuất</a>
  <?php endif; ?>
</div>

<?php if(!$madh): ?>
  <div class="notice">Nhập mã đơn để xem chi tiết.</div>
<?php else: ?>
<table class="table">
  <thead><tr><th>Mã SP</th><th>Tên SP</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
  <tbody>
  <?php
    $sum=0;
    if($rows && mysqli_num_rows($rows)>0):
      while($r=mysqli_fetch_assoc($rows)):
        $tt = (float)$r['dongia'] * (int)$r['soluong'];
        $sum += $tt;
  ?>
    <tr>
      <td><?= htmlspecialchars($r['masp']) ?></td>
      <td><?= htmlspecialchars($r['tensp']) ?></td>
      <td><?= (int)$r['soluong'] ?></td>
      <td><?= number_format((float)$r['dongia']) ?></td>
      <td><?= number_format($tt) ?></td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="5" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>

<div class="sumline">
  <span>Tổng:</span><b><?= number_format($sum) ?> đ</b>
</div>
<?php endif; ?>

