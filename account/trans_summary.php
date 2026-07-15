<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

function englishToBanglaNumber($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9','.', ','];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','.', ','];
    return str_replace($en, $bn, $number);
}

// Filter inputs
$gl_id     = (int)($_GET['gl_id']     ?? 0);
$from_date = trim($_GET['from_date']  ?? '');
$to_date   = trim($_GET['to_date']    ?? '');
$download  = isset($_GET['download']) && $_GET['download'] === 'csv';

// GL list for dropdown
$glStmt = $pdo->query("SELECT id, glac_code, glac_name FROM glac_mst WHERE parent_child = 'C' ORDER BY glac_code ASC");
$glList = $glStmt->fetchAll(PDO::FETCH_ASSOC);

$filtered = ($gl_id > 0 || ($from_date !== '' && $to_date !== ''));

if ($filtered) {
    // Build filtered query from voucher_payments
    $where   = ["v.status = 'A'"];
    $params  = [];

    if ($gl_id > 0) {
        $where[]  = "v.glac_id = ?";
        $params[] = $gl_id;
    }
    if ($from_date !== '' && $to_date !== '') {
        $where[]  = "v.tran_date BETWEEN ? AND ?";
        $params[] = $from_date;
        $params[] = $to_date;
    }

    $whereSQL = implode(' AND ', $where);

    $stmt = $pdo->prepare("
        SELECT v.tran_date,
               g.id AS glac_id, g.glac_code, g.glac_name,
               SUM(CASE WHEN v.drcr_code = 'D' THEN v.tran_amount ELSE 0 END) AS debit_amount,
               SUM(CASE WHEN v.drcr_code = 'C' THEN v.tran_amount ELSE 0 END) AS credit_amount
        FROM voucher_payments v
        JOIN glac_mst g ON v.glac_id = g.id
        WHERE $whereSQL
        GROUP BY v.tran_date, g.id, g.glac_code, g.glac_name
        ORDER BY v.tran_date ASC, g.glac_code ASC
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Default: full gl_summary
    $stmt = $pdo->query("
        SELECT gs.glac_id, gm.glac_name, gm.glac_code, gs.debit_amount, gs.credit_amount
        FROM gl_summary gs
        LEFT JOIN glac_mst gm ON gm.id = gs.glac_id
        ORDER BY gm.glac_code ASC, gm.glac_name ASC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total_debit  = 0;
$total_credit = 0;
foreach ($rows as $row) {
    $total_debit  += (float)($row['debit_amount']  ?? 0);
    $total_credit += (float)($row['credit_amount'] ?? 0);
}

// ── CSV download ──────────────────────────────────────────────────────────────
if ($download) {
    $parts = [];
    if ($gl_id > 0) {
        foreach ($glList as $g) {
            if ($g['id'] == $gl_id) { $parts[] = $g['glac_code']; break; }
        }
    }
    if ($from_date && $to_date) $parts[] = "{$from_date}_to_{$to_date}";
    $label = $parts ? implode('_', $parts) : 'all';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="trans_summary_' . $label . '.csv"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

    $period = ($from_date && $to_date) ? "$from_date to $to_date" : 'All Data';
    fputcsv($out, ['Transaction Summary', $period]);
    fputcsv($out, []);

    if ($filtered) {
        fputcsv($out, ['Date', 'GL Code', 'GL Name', 'Debit Amount', 'Credit Amount']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['tran_date'],
                $r['glac_code'],
                $r['glac_name'],
                number_format((float)$r['debit_amount'],  2, '.', ''),
                number_format((float)$r['credit_amount'], 2, '.', ''),
            ]);
        }
    } else {
        fputcsv($out, ['GL Code', 'GL Name', 'Debit Amount', 'Credit Amount']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['glac_code'] ?? '',
                $r['glac_name'] ?? 'N/A',
                number_format((float)$r['debit_amount'],  2, '.', ''),
                number_format((float)$r['credit_amount'], 2, '.', ''),
            ]);
        }
    }

    fputcsv($out, []);
    fputcsv($out, ['', 'মোট (Total)', number_format($total_debit, 2, '.', ''), number_format($total_credit, 2, '.', '')]);
    fclose($out);
    exit;
}
// ─────────────────────────────────────────────────────────────────────────────
?>

<?php
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary fw-bold mb-0">লেনদেন সারসংক্ষেপ <span class="text-secondary">(Transaction Summary)</span></h3>
                </div>
                <hr class="mb-3" />

                <!-- Filter form -->
                <form method="get" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">GL Account</label>
                        <select name="gl_id" class="form-select">
                            <option value="0">-- সকল GL --</option>
                            <?php foreach ($glList as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= $gl_id == $g['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['glac_code'] . ' - ' . $g['glac_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Run
                        </button>
                        <a href="trans_summary.php" class="btn btn-outline-secondary flex-fill">Reset</a>
                        <a href="trans_summary.php?download=csv<?=
                            ($gl_id     ? '&gl_id='     . $gl_id     : '') .
                            ($from_date ? '&from_date=' . urlencode($from_date) : '') .
                            ($to_date   ? '&to_date='   . urlencode($to_date)   : '')
                        ?>" class="btn btn-success flex-fill">
                            <i class="bi bi-file-earmark-excel"></i> CSV
                        </a>
                    </div>
                </form>

                <?php if ($filtered): ?>
                    <p class="text-muted small mb-3">
                        <?php if ($gl_id > 0):
                            $selGL = array_values(array_filter($glList, fn($g) => $g['id'] == $gl_id))[0] ?? null;
                        ?>
                            GL: <strong><?= htmlspecialchars($selGL ? $selGL['glac_code'].' - '.$selGL['glac_name'] : '') ?></strong>
                        <?php endif; ?>
                        <?= ($from_date && $to_date) ? '&nbsp;|&nbsp; Date: <strong>'.htmlspecialchars($from_date).'</strong> to <strong>'.htmlspecialchars($to_date).'</strong>' : '' ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted small mb-3">Showing all data (no filter)</p>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <?php if ($filtered): ?>
                                    <th>তারিখ (Date)</th>
                                <?php endif; ?>
                                <th>জেনারেল লেজার (GL)</th>
                                <th class="text-end">ডেবিট (Debit)</th>
                                <th class="text-end">ক্রেডিট (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rows) > 0): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <?php if ($filtered): ?>
                                            <td><?= htmlspecialchars($row['tran_date']) ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <?= htmlspecialchars($row['glac_name'] ?? 'N/A') ?>
                                            <?php if (!empty($row['glac_code'])): ?>
                                                <span class="text-muted">(<?= htmlspecialchars($row['glac_code']) ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">৳ <?= englishToBanglaNumber(number_format((float)($row['debit_amount']  ?? 0), 2)) ?></td>
                                        <td class="text-end">৳ <?= englishToBanglaNumber(number_format((float)($row['credit_amount'] ?? 0), 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="fw-bold table-light">
                                    <td colspan="<?= $filtered ? 2 : 1 ?>" class="text-end">মোট</td>
                                    <td class="text-end">৳ <?= englishToBanglaNumber(number_format($total_debit,  2)) ?></td>
                                    <td class="text-end">৳ <?= englishToBanglaNumber(number_format($total_credit, 2)) ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $filtered ? 4 : 3 ?>" class="text-center text-muted">কোনো তথ্য পাওয়া যায়নি।</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</main>
</div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
