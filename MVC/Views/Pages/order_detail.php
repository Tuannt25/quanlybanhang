<?php
$rows = $payload['rows'] ?? null;
$madh = $payload['madh'] ?? '';
$order = $payload['order'] ?? null;
$trangthai = $order['trangthai'] ?? 'Chờ xử lý';
?>
<div class="bill-wrap">

  <div class="bill-top">
    <div>
      <h2 class="bill-title">HÓA ĐƠN MUA HÀNG</h2>
      <div class="bill-sub">Mã đơn: <span class="bill-code"><?= htmlspecialchars($madh) ?></span></div>
      <div class="bill-note">Cảm ơn bạn đã mua hàng ❤️</div>
    </div>

    <div class="bill-actions">
      <a class="u-btn ghost" href="<?= BASE_URL ?>Orders/Index">← Quay lại</a>
      <button class="u-btn primary" onclick="window.print()">🖨 In hóa đơn</button>
    </div>
  </div>

  <div class="bill-box">
    <div class="bill-row">
      <div class="bill-col">
        <div class="bill-label">Cửa hàng</div>
        <div class="bill-value">Mini Shop - UTT</div>
      </div>

      <div class="bill-col">
        <div class="bill-label">Ngày in</div>
        <div class="bill-value"><?= date("d/m/Y H:i") ?></div>
      </div>

      <div class="bill-col">
        <div class="bill-label">Trạng thái</div>
        <div class="bill-status"><?= htmlspecialchars($trangthai) ?></div>

      </div>
    </div>

    <div class="bill-table-wrap">
      <table class="bill-table">
        <thead>
          <tr>
            <th style="text-align:left">Sản phẩm</th>
            <th style="width:110px;text-align:center">Số lượng</th>
            <th style="width:140px;text-align:right">Đơn giá</th>
            <th style="width:160px;text-align:right">Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $tong = 0;
          if ($rows && mysqli_num_rows($rows) > 0):
            while ($r = mysqli_fetch_assoc($rows)):
              $sl = (int) $r['soluong'];
              $dg = (float) $r['dongia'];
              $tt = $dg * $sl;
              $tong += $tt;
              ?>
              <tr>
                <td>
                  <div class="pname"><?= htmlspecialchars($r['tensp']) ?></div>
                  <div class="psub">x<?= $sl ?> • <?= number_format($dg) ?> đ</div>
                </td>
                <td style="text-align:center"><?= $sl ?></td>
                <td style="text-align:right"><?= number_format($dg) ?> đ</td>
                <td style="text-align:right;font-weight:900"><?= number_format($tt) ?> đ</td>
              </tr>
            <?php endwhile; else: ?>
            <tr>
              <td colspan="4" class="empty">Không có dữ liệu</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="bill-sum">

      <div class="sum-row total">
        <span>TỔNG THANH TOÁN</span>
        <span><?= number_format($tong) ?> đ</span>
      </div>
    </div>

  </div>
</div>

<style>
  /* =============================
   BILL STYLE (đẹp + in được)
============================= */
  .bill-wrap {
    max-width: 920px;
    margin: 0 auto;
  }

  .bill-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
  }

  .bill-title {
    margin: 0;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: .6px;
  }

  .bill-sub {
    margin-top: 6px;
    color: var(--muted);
    font-weight: 700;
  }

  .bill-code {
    color: #c2410c;
  }

  .bill-note {
    margin-top: 4px;
    color: var(--muted);
    font-size: 14px;
  }

  .bill-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .bill-box {
    background: #fff;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    border: 1px solid rgba(0, 0, 0, .06);
  }

  .bill-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px dashed rgba(0, 0, 0, .15);
    margin-bottom: 14px;
  }

  .bill-col {
    background: rgba(0, 0, 0, .02);
    border-radius: 14px;
    padding: 10px 12px;
  }

  .bill-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .6px;
  }

  .bill-value {
    margin-top: 6px;
    font-weight: 800;
  }

  .bill-status {
    margin-top: 6px;
    display: inline-block;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 900;
    background: rgba(194, 65, 12, .12);
    color: #c2410c;
  }

  .bill-table-wrap {
    overflow: auto;
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, .06);
  }

  .bill-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 720px;
  }

  .bill-table thead th {
    background: rgba(0, 0, 0, .03);
    padding: 12px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #111827;
  }

  .bill-table tbody td {
    padding: 12px;
    border-top: 1px solid rgba(0, 0, 0, .06);
    vertical-align: top;
  }

  .pname {
    font-weight: 900;
  }

  .psub {
    margin-top: 4px;
    color: var(--muted);
    font-size: 12px;
  }

  .empty {
    text-align: center;
    color: var(--muted);
    padding: 20px !important;
  }

  .bill-sum {
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed rgba(0, 0, 0, .15);
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 420px;
    margin-left: auto;
  }

  .sum-row {
    display: flex;
    justify-content: space-between;
    font-weight: 800;
    color: #111827;
  }

  .sum-row.total {
    font-size: 18px;
    font-weight: 900;
    color: #c2410c;
  }

  .bill-footer {
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px dashed rgba(0, 0, 0, .15);
  }

  .bill-thanks {
    color: var(--muted);
    font-weight: 700;
  }

  .bill-sign {
    margin-top: 14px;
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    flex-wrap: wrap;
  }

  .sign-label {
    font-weight: 900;
    margin-bottom: 8px;
  }

  .sign-line {
    width: 220px;
    height: 40px;
    border-bottom: 2px dashed rgba(0, 0, 0, .2);
  }

  @media print {
    @page {
      size: A4;
      margin: 10mm;
    }

    /* Ẩn toàn bộ trang */
    body * {
      visibility: hidden !important;
    }

    /* Chỉ hiện hóa đơn */
    .bill-wrap,
    .bill-wrap * {
      visibility: visible !important;
    }

    .bill-wrap {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      margin: 0 !important;
    }

    /* Ẩn nút */
    .bill-actions,
    .u-btn {
      display: none !important;
    }

    /* Fix bảng không cuộn */
    .bill-table-wrap {
      overflow: visible !important;
      border: none !important;
    }

    .bill-table {
      min-width: 0 !important;
      width: 100% !important;
    }

    body {
      background: #fff !important;
    }

    .bill-box {
      box-shadow: none !important;
    }


  }
</style>