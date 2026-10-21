<?php
$items = $payload['items'] ?? [];
$tong = $payload['tong'] ?? 0;
?>
<h2 style="margin:0 0 12px">Giỏ hàng</h2>

<?php if(empty($items)): ?>
  <div class="notice">Giỏ hàng đang trống. <a href="<?= BASE_URL ?>Shop/Index" style="color:#c2410c;font-weight:900">Mua sắm ngay</a></div>
<?php else: ?>
  <form method="post" action="<?= BASE_URL ?>Cart/Update">
    <table class="table">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Đơn giá</th>
          <th style="width:120px">Số lượng</th>
          <th>Thành tiền</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $it): $sp=$it['sp']; ?>
          <tr>
            <td><b><?= htmlspecialchars($sp['tensp']) ?></b></td>
            <td><?= number_format((float)$sp['gia']) ?> đ</td>
            <td>
              <input type="number" min="1" name="qty[<?= htmlspecialchars($sp['masp']) ?>]" value="<?= (int)$it['qty'] ?>"
                     style="width:90px; padding:8px; border-radius:12px; border:1px solid var(--line)">
            </td>
            <td><b><?= number_format((float)$it['thanhtien']) ?> đ</b></td>
            <td><a href="<?= BASE_URL ?>Cart/Remove/<?= urlencode($sp['masp']) ?>" onclick="return confirm('Xóa?')">🗑</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="display:flex; gap:10px; justify-content:space-between; align-items:center; margin-top:12px; flex-wrap:wrap">
      <div style="font-weight:900">Tổng: <span style="color:#c2410c"><?= number_format((float)$tong) ?> đ</span></div>
      <div style="display:flex; gap:10px; flex-wrap:wrap">
        <button class="u-btn" type="submit">Cập nhật</button>
        <a class="u-btn" href="<?= BASE_URL ?>Cart/Clear" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa giỏ</a>
        <a class="u-btn ghost" href="<?= BASE_URL ?>Cart/Checkout" onclick="return confirm('Xác nhận đặt hàng?')">Đặt hàng</a>
      </div>
    </div>
  </form>
<?php endif; ?>
