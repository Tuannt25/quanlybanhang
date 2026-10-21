<?php
$from = $payload["from"] ?? date("Y-m-01");
$to = $payload["to"] ?? date("Y-m-d");
$selected = $payload["selected"] ?? [];
$tong = $payload["tong"] ?? ["tongdon"=>0,"tongtien"=>0];
$rows = $payload["rows"] ?? null;
$dms = $payload["dms"] ?? null;

$selStr = is_array($selected) ? implode(",", $selected) : "";
?>
<h2 class="page-title">THỐNG KÊ DOANH THU (NGÀY / THÁNG)</h2>

<div class="panel">
  <form method="post" action="<?= BASE_URL ?>ThongKe/NgayThang" class="form-grid">
    <div>
      <label class="lbl">Từ ngày</label>
      <input class="input" type="date" name="from" value="<?= htmlspecialchars($from) ?>">
    </div>
    <div>
      <label class="lbl">Đến ngày</label>
      <input class="input" type="date" name="to" value="<?= htmlspecialchars($to) ?>">
    </div>
    <div class="full">
      <label class="lbl">Danh mục (chọn nhiều)</label>
      <select class="select full" name="madm[]" multiple size="5">
        <?php if($dms): while($dm=mysqli_fetch_assoc($dms)): ?>
          <?php $isSel = in_array($dm['madm'], $selected) ? "selected" : ""; ?>
          <option value="<?= htmlspecialchars($dm['madm']) ?>" <?= $isSel ?>><?= htmlspecialchars($dm['tendm']) ?></option>
        <?php endwhile; endif; ?>
      </select>
      <div class="muted">Giữ Ctrl để chọn nhiều</div>
    </div>
    <div class="actions full">
      <button class="btn primary">📊 Thống kê</button>
      <a class="btn" href="<?= BASE_URL ?>ThongKe/ExportNgayThang?from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&madm=<?= urlencode($selStr) ?>">⬇ Xuất</a>
    </div>
  </form>
</div>

<div class="stats">
  <div class="stat-card">
    <div class="muted">Tổng đơn</div>
    <div class="stat-val"><?= (int)($tong['tongdon'] ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="muted">Tổng tiền</div>
    <div class="stat-val"><?= number_format((float)($tong['tongtien'] ?? 0)) ?> đ</div>
  </div>
</div>

<table class="table">
  <thead><tr><th>Mã SP</th><th>Tên SP</th><th>Danh mục</th><th>Tổng SL</th><th>Doanh thu</th></tr></thead>
  <tbody>
  <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
    <tr>
      <td><?= htmlspecialchars($r['masp']) ?></td>
      <td><?= htmlspecialchars($r['tensp']) ?></td>
      <td><?= htmlspecialchars($r['tendm']) ?></td>
      <td><?= (int)$r['tongsl'] ?></td>
      <td><?= number_format((float)$r['doanhthu']) ?> đ</td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="5" style="text-align:center">Không có dữ liệu</td></tr>
  <?php endif; ?>
  </tbody>
</table>

