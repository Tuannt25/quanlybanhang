<?php
$rows = $payload["rows"] ?? null;
?>
<h2 class="page-title">TOP 10 SẢN PHẨM BÁN CHẠY</h2>

<div class="toolbar">
  <a class="btn" href="<?= BASE_URL ?>ThongKe/ExportTop" style="margin-left:auto">⬇ Xuất</a>
</div>

<table class="table">
  <thead><tr><th>#</th><th>Mã SP</th><th>Tên SP</th><th>Danh mục</th><th>Tổng SL</th><th>Doanh thu</th></tr></thead>
  <tbody>
  <?php
    $i=0;
    if($rows && mysqli_num_rows($rows)>0):
      while($r=mysqli_fetch_assoc($rows)):
        $i++;
  ?>
    <tr>
      <td><span class="badge"><?= $i ?></span></td>
      <td><?= htmlspecialchars($r['masp']) ?></td>
      <td><?= htmlspecialchars($r['tensp']) ?></td>
      <td><?= htmlspecialchars($r['tendm']) ?></td>
      <td><?= (int)$r['tongsl'] ?></td>
      <td><?= number_format((float)$r['doanhthu']) ?> đ</td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="6" style="text-align:center">Chưa có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>

