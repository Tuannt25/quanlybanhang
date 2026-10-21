<?php
$rows = $payload['rows'] ?? null;
?>
<h2 style="margin:0 0 12px">Đơn hàng của tôi</h2>

<table class="table">
  <thead>
    <tr>
      <th>Mã đơn</th>
      <th>Ngày đặt</th>
      <th>Tổng tiền</th>
      <th>Trạng thái</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows && mysqli_num_rows($rows) > 0):
      while ($r = mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><b><?= htmlspecialchars($r['madh']) ?></b></td>
          <td><?= htmlspecialchars($r['ngaydat']) ?></td>
          <td><?= number_format((float) $r['tongtien']) ?> đ</td>
          <td><?= htmlspecialchars($r['trangthai']) ?></td>
          <td class="actions">
            <a class="btn" href="<?= BASE_URL ?>Orders/Detail/<?= urlencode($r['madh']) ?>">👁 Xem</a>
            <a class="btn" href="<?= BASE_URL ?>Orders/Invoice/<?= urlencode($r['madh']) ?>">🖨 In</a>

            <?php if (($r['trangthai'] ?? '') === 'Chờ xử lý'): ?>
              <a class="btn danger"
                onclick="return confirm('Bạn có chắc muốn hủy đơn <?= htmlspecialchars($r['madh']) ?> không?')"
                href="<?= BASE_URL ?>Orders/Huy/<?= urlencode($r['madh']) ?>">
                ❌ Hủy
              </a>
            <?php endif; ?>
          </td>

        </tr>
      <?php endwhile; else: ?>
      <tr>
        <td colspan="5" style="text-align:center;color:var(--muted)">Chưa có đơn hàng</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
