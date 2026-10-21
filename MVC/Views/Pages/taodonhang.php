<?php
$khs = $payload['khs'] ?? null;
$sps = $payload['sps'] ?? null;
$cartRows = $payload['cartRows'] ?? [];
$tong = (float)($payload['tong'] ?? 0);
$msg = $payload['msg'] ?? null;
$msgType = $payload['msgType'] ?? 'error';
?>
<h2 class="page-title">TẠO ĐƠN HÀNG</h2>

<?php if ($msg): ?>
  <div class="notice <?= htmlspecialchars($msgType) ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>TaoDonHang/Add" class="form-grid">
    <select class="select full" name="masp" required>
      <option value="">-- Chọn sản phẩm --</option>
      <?php if ($sps): while ($sp = mysqli_fetch_assoc($sps)): ?>
        <option value="<?= htmlspecialchars($sp['masp']) ?>" <?= (int)$sp['soluong'] <= 0 ? 'disabled' : '' ?>>
          <?= htmlspecialchars($sp['tensp']) ?> (còn <?= (int)$sp['soluong'] ?>)
        </option>
      <?php endwhile; endif; ?>
    </select>
    <input class="input" type="number" name="qty" min="1" value="1" required>
    <button class="btn primary" type="submit">➕ Thêm vào đơn</button>
  </form>
</div>

<table class="table">
  <thead><tr><th>Mã SP</th><th>Tên sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th></th></tr></thead>
  <tbody>
  <?php if ($cartRows): foreach ($cartRows as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['masp']) ?></td>
      <td><?= htmlspecialchars($row['tensp']) ?></td>
      <td><?= number_format((float)$row['gia']) ?> đ</td>
      <td>
        <form method="post" action="<?= BASE_URL ?>TaoDonHang/Update" class="actions">
          <input type="hidden" name="masp" value="<?= htmlspecialchars($row['masp']) ?>">
          <input class="input" style="width:90px" type="number" name="qty" min="0" max="<?= (int)$row['ton'] ?>" value="<?= (int)$row['qty'] ?>">
          <button class="btn" type="submit">Cập nhật</button>
        </form>
      </td>
      <td><?= number_format((float)$row['thanhtien']) ?> đ</td>
      <td><a class="btn danger" href="<?= BASE_URL ?>TaoDonHang/Remove/<?= urlencode($row['masp']) ?>">Xóa</a></td>
    </tr>
  <?php endforeach; else: ?>
    <tr><td colspan="6" style="text-align:center">Chưa có sản phẩm trong đơn</td></tr>
  <?php endif; ?>
  </tbody>
</table>

<div class="form-card">
  <form method="post" action="<?= BASE_URL ?>TaoDonHang/Create" class="form-grid">
    <select class="select full" name="makh" required>
      <option value="">-- Chọn khách hàng --</option>
      <?php if ($khs): while ($kh = mysqli_fetch_assoc($khs)): ?>
        <option value="<?= htmlspecialchars($kh['makh']) ?>"><?= htmlspecialchars($kh['tenkh']) ?> (<?= htmlspecialchars($kh['makh']) ?>)</option>
      <?php endwhile; endif; ?>
    </select>
    <div class="full"><b>Tổng tiền: <?= number_format($tong) ?> đ</b></div>
    <div class="actions full">
      <button class="btn primary" type="submit" <?= !$cartRows ? 'disabled' : '' ?>>💾 Tạo đơn</button>
      <a class="btn" href="<?= BASE_URL ?>TaoDonHang/Clear">Xóa giỏ</a>
    </div>
  </form>
</div>
