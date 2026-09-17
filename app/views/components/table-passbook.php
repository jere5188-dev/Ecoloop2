<?php
/**
 * Component: table-passbook
 *
 * The Waste Passbook ledger (requirements R1.2–R1.7).
 *
 * Columns: Kategori Sampah · Volume (KG) · Proporsi · EcoPoints Diperoleh ·
 * Status Verifikasi. Every <td> carries `data-label` so the mobile stylesheet
 * can transform each row into a self-labelling card without any JS.
 *
 * Props:
 *   rows       array<int, array<string, mixed>>  ledger rows (required)
 *   totals     array<string, mixed>              aggregate row values
 *   showFooter bool                              render the totals row (default true)
 *   filterable bool                              tag rows with data-category (default true)
 */

$rows = (array) ($props['rows'] ?? []);
$totals = (array) ($props['totals'] ?? []);
$showFooter = (bool) ($props['showFooter'] ?? true);
$filterable = (bool) ($props['filterable'] ?? true);
?>
<div class="table-wrap">
    <table class="data-table data-table--passbook" data-passbook-table>
        <caption class="sr-only">
            Rincian volume sampah tersortir per kategori, proporsi, EcoPoints yang diperoleh dan status verifikasi.
        </caption>
        <thead>
            <tr>
                <th scope="col">Kategori Sampah</th>
                <th scope="col">Volume (KG)</th>
                <th scope="col">Proporsi</th>
                <th scope="col">EcoPoints Diperoleh</th>
                <th scope="col">Status Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row):
                $tone = (string) ($row['tone'] ?? 'forest');
                $statusKey = (string) ($row['status_key'] ?? 'verified');
                $pct = clamp_pct((float) ($row['proportion'] ?? 0));
                ?>
                <tr<?= $filterable ? ' data-category="' . e((string) ($row['key'] ?? '')) . '"' : '' ?>>
                    <td data-label="Kategori">
                        <div class="cell-material">
                            <span class="tile tile--sm tile--<?= e($tone) ?>">
                                <?php component('icon', ['name' => (string) ($row['icon'] ?? 'recycle'), 'size' => 'md']); ?>
                            </span>
                            <span class="u-grow">
                                <span class="cell-material__label"><?= e((string) ($row['label'] ?? '')) ?></span>
                                <span class="cell-material__sub"><?= e((string) ($row['sub'] ?? '')) ?></span>
                            </span>
                        </div>
                    </td>
                    <td data-label="Volume" class="is-numeric"><?= e(fmt_kg((float) ($row['kg'] ?? 0))) ?></td>
                    <td data-label="Proporsi">
                        <span class="bar-inline">
                            <span class="bar-inline__track">
                                <span class="bar-inline__fill" style="width: <?= e(number_format($pct, 2, '.', '')) ?>%"></span>
                            </span>
                            <span class="bar-inline__value"><?= e(fmt_pct($pct)) ?></span>
                        </span>
                    </td>
                    <td data-label="EcoPoints" class="is-numeric">
                        <?php $pts = (int) ($row['points'] ?? 0); ?>
                        <span class="<?= $pts > 0 ? 't-forest' : 't-muted' ?>">
                            <?= $pts > 0 ? '+' . e(number_format($pts)) . ' Pts' : '—' ?>
                        </span>
                    </td>
                    <td data-label="Status">
                        <span class="pill pill--<?= e(status_tone($statusKey)) ?>">
                            <?php component('icon', ['name' => $statusKey === 'verified' ? 'check-circle' : ($statusKey === 'pending' ? 'clock' : 'alert')]); ?>
                            <?= e((string) ($row['status'] ?? '')) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if ($showFooter && $totals): ?>
            <tfoot data-passbook-total>
                <tr>
                    <td data-label="Total">Total keseluruhan</td>
                    <td data-label="Volume" class="is-numeric" data-total-kg><?= e(fmt_kg((float) ($totals['kg'] ?? 0))) ?></td>
                    <td data-label="Proporsi" class="is-numeric" data-total-pct>100.0%</td>
                    <td data-label="EcoPoints" class="is-numeric t-forest" data-total-points>+<?= e(number_format((int) ($totals['points'] ?? 0))) ?> Pts</td>
                    <td data-label="Status">
                        <span class="t-small t-muted"><?= e((int) ($totals['deposits'] ?? 0)) ?> transaksi</span>
                    </td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>
</div>
