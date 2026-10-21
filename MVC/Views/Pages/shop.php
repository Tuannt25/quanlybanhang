<?php
$rows = $payload['rows'] ?? [];
$danhmuc = $payload['danhmuc'] ?? null;
$kw = $payload['kw'] ?? '';
$madm = $payload['madm'] ?? '';
?>
<div class="p-toolbar">
    <form method="get" action="<?= BASE_URL ?>Shop/Index" style="display:flex; gap:12px; flex-wrap:wrap; width:100%">
        <input type="text" name="kw" class="p-select" style="flex:1; min-width:280px" placeholder="Tìm theo tên..."
            value="<?= htmlspecialchars($kw) ?>">
        <select name="madm" class="p-select" style="min-width:280px">
            <option value="">-- Tất cả danh mục --</option>
            <?php if ($danhmuc):
                while ($dm = mysqli_fetch_assoc($danhmuc)): ?>
                    <option value="<?= htmlspecialchars($dm['madm']) ?>" <?= ($madm === $dm['madm']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dm['tendm']) ?>
                    </option>
                <?php endwhile; endif; ?>
        </select>
        <button type="submit" class="u-btn ghost">Lọc</button>
    </form>
</div>

<div class="p-grid">
    <?php foreach ($rows as $sp): ?>
        <?php
        $qty = (int) ($sp['soluong'] ?? 0);
        $outStock = ($qty <= 0);
        ?>
        <div class="p-card <?= $outStock ? 'out-stock' : '' ?>">

            <?php if ($outStock): ?>
                <div class="p-badge-out">HẾT HÀNG</div>
            <?php endif; ?>

            <div class="p-img">
                <?php if (!empty($sp['hinhanh'])): ?>
                    <img src="<?= BASE_URL ?>Public/Picture/<?= htmlspecialchars($sp['hinhanh']) ?>" alt="">
                <?php else: ?>
                    <span style="font-weight:900;color:#c2410c">No Image</span>
                <?php endif; ?>
            </div>

            <div class="p-name"><?= htmlspecialchars($sp['tensp']) ?></div>

            <div class="p-meta">
                <span>Kho: <?= $qty ?></span>
                <span class="p-price"><?= number_format((float) $sp['gia']) ?> đ</span>
            </div>

            <div class="p-actions">
                <?php if (!$outStock): ?>
                    <a class="primary" href="<?= BASE_URL ?>Shop/Add/<?= urlencode($sp['masp']) ?>">Thêm giỏ</a>
                <?php else: ?>
                    <button class="primary disabled" disabled>Hết hàng</button>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>Cart/Index">Xem giỏ</a>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<style>
    /* ✅ Card hết hàng: xám + mờ */
    .p-card.out-stock {
        opacity: 0.6;
        filter: grayscale(100%);
        position: relative;
    }

    /* Badge hết hàng */
    .p-badge-out {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #444;
        color: #fff;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        z-index: 10;
    }

    /* nút disabled */
    .p-actions .disabled {
        background: #9ca3af !important;
        cursor: not-allowed !important;
        border: none !important;
    }
</style>
