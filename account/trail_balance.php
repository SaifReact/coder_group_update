<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

function bn_number($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9','.', ','];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','.', ','];
    return str_replace($en, $bn, $number);
}

$from_date = trim($_GET['from_date'] ?? '');
$to_date   = trim($_GET['to_date']   ?? '');
$download  = isset($_GET['download']) && $_GET['download'] === 'csv';

$typeMap = [
    '1' => 'সম্পদ (Assets)',
    '2' => 'দায় (Liabilities)',
    '3' => 'আয় (Income)',
    '4' => 'ব্যয় (Expenses)',
    '5' => 'মূলধন (Capital)',
];

// Build query: date-filtered from voucher_payments, or full from gl_summary
if ($from_date !== '' && $to_date !== '') {
    $stmt = $pdo->prepare("
        SELECT g.glac_code, g.glac_name, g.glac_type,
               SUM(CASE WHEN v.drcr_code = 'D' THEN v.tran_amount ELSE 0 END) AS debit_amount,
               SUM(CASE WHEN v.drcr_code = 'C' THEN v.tran_amount ELSE 0 END) AS credit_amount
        FROM voucher_payments v
        JOIN glac_mst g ON v.glac_id = g.id
        WHERE v.tran_date BETWEEN ? AND ?
          AND v.status = 'A'
        GROUP BY g.id, g.glac_code, g.glac_name, g.glac_type
        ORDER BY g.glac_type ASC, g.glac_code ASC
    ");
    $stmt->execute([$from_date, $to_date]);
} else {
    $stmt = $pdo->query("
        SELECT g.glac_code, g.glac_name, g.glac_type, s.debit_amount, s.credit_amount
        FROM gl_summary s
        JOIN glac_mst g ON s.glac_id = g.id
        ORDER BY g.glac_type ASC, g.glac_code ASC
    ");
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by type
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['glac_type'] ?? '0'][] = $row;
}

// ── CSV download ──────────────────────────────────────────────────────────────
if ($download) {
    $label = ($from_date && $to_date) ? "{$from_date}_to_{$to_date}" : 'all';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="trial_balance_' . $label . '.csv"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // UTF-8 BOM for Excel
    fputs($out, "\xEF\xBB\xBF");

    $period = ($from_date && $to_date) ? "$from_date to $to_date" : 'All Data';
    fputcsv($out, ['Trial Balance', $period]);
    fputcsv($out, []);
    fputcsv($out, ['Type', 'GL Code', 'GL Name', 'Debit Amount', 'Credit Amount']);

    $grand_debit  = 0;
    $grand_credit = 0;

    foreach ($typeMap as $type => $title) {
        if (empty($grouped[$type])) continue;
        fputcsv($out, [$title, '', '', '', '']);
        $sub_d = 0; $sub_c = 0;
        foreach ($grouped[$type] as $r) {
            fputcsv($out, [
                '',
                $r['glac_code'],
                $r['glac_name'],
                number_format($r['debit_amount'],  2, '.', ''),
                number_format($r['credit_amount'], 2, '.', ''),
            ]);
            $sub_d += $r['debit_amount'];
            $sub_c += $r['credit_amount'];
        }
        fputcsv($out, ['', '', 'Subtotal', number_format($sub_d, 2, '.', ''), number_format($sub_c, 2, '.', '')]);
        fputcsv($out, []);
        $grand_debit  += $sub_d;
        $grand_credit += $sub_c;
    }

    fputcsv($out, ['Grand Total', '', '', number_format($grand_debit, 2, '.', ''), number_format($grand_credit, 2, '.', '')]);
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
                    <h3 class="text-primary fw-bold mb-0"> ট্রায়াল ব্যালেন্স <span class="text-secondary">(Trail Balance)</span></h3>
                </div>
                <hr class="mb-3" />

                <!-- Filter form -->
                <form method="get" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-1">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-1">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Run
                        </button>
                        <a href="trail_balance.php" class="btn btn-outline-secondary flex-fill">Reset</a>
                        <a href="trail_balance.php?download=csv<?= $from_date ? '&from_date='.urlencode($from_date).'&to_date='.urlencode($to_date) : '' ?>"
                           class="btn btn-success flex-fill">
                            <i class="bi bi-file-earmark-excel"></i> CSV
                        </a>
                    </div>
                </form>

                <?php if ($from_date && $to_date): ?>
                    <p class="text-muted small mb-3">Showing data from <strong><?= htmlspecialchars($from_date) ?></strong> to <strong><?= htmlspecialchars($to_date) ?></strong></p>
                <?php else: ?>
                    <p class="text-muted small mb-3">Showing all data (no date filter)</p>
                <?php endif; ?>

                <?php
                $grand_debit  = 0;
                $grand_credit = 0;
                foreach ($typeMap as $type => $title):
                    if (empty($grouped[$type])) continue;
                    $type_debit  = 0;
                    $type_credit = 0;
                ?>
                <div class="table-responsive mb-4">
                    <h4 class="bg-primary text-white p-2 rounded"><?= $title ?></h4>
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>GL Code</th>
                                <th>GL Name</th>
                                <th class="text-end">Debit Amount</th>
                                <th class="text-end">Credit Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($grouped[$type] as $row):
                            $type_debit  += $row['debit_amount'];
                            $type_credit += $row['credit_amount'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['glac_code']) ?></td>
                                <td><?= htmlspecialchars($row['glac_name']) ?></td>
                                <td class="text-end">৳ <?= bn_number(number_format($row['debit_amount'],  2)) ?></td>
                                <td class="text-end">৳ <?= bn_number(number_format($row['credit_amount'], 2)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold table-light">
                                <td colspan="2" class="text-end">Subtotal</td>
                                <td class="text-end">৳ <?= bn_number(number_format($type_debit,  2)) ?></td>
                                <td class="text-end">৳ <?= bn_number(number_format($type_credit, 2)) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
                    $grand_debit  += $type_debit;
                    $grand_credit += $type_credit;
                endforeach; ?>

                <div class="alert alert-success fw-bold">
                    Grand Total:
                    <span class="float-end">
                        Debit: ৳ <?= bn_number(number_format($grand_debit,  2)) ?>
                        &nbsp;|&nbsp;
                        Credit: ৳ <?= bn_number(number_format($grand_credit, 2)) ?>
                    </span>
                </div>

            </div>
        </div>
    </div>
</main>
</div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
