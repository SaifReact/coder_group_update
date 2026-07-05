<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

function bn($n) {
    return str_replace(
        ['0','1','2','3','4','5','6','7','8','9','.'],
        ['০','১','২','৩','৪','৫','৬','৭','৮','৯','.'],
        $n
    );
}
function tk($amount) {
    return '৳ ' . bn(number_format((float)$amount, 2));
}

// ── Member status counts ─────────────────────────────────────────────────────
$counts = $pdo->query("
    SELECT
        SUM(status='A') AS approved,
        SUM(status='P') AS processed,
        SUM(status='I') AS inactive,
        SUM(status='R') AS rejected
    FROM user_login WHERE role='user'
")->fetch(PDO::FETCH_ASSOC);

$paymentMemberCount = (int)$pdo->query("
    SELECT COUNT(DISTINCT member_id) FROM member_payments WHERE status='A'
")->fetchColumn();

// ── Fees from member_share ───────────────────────────────────────────────────
$fees = $pdo->query("
    SELECT
        COALESCE(SUM(idcard_fee),    0) AS idcard_fee,
        COALESCE(SUM(passbook_fee),  0) AS passbook_fee,
        COALESCE(SUM(softuses_fee),  0) AS softuses_fee,
        COALESCE(SUM(sms_fee),       0) AS sms_fee,
        COALESCE(SUM(office_rent),   0) AS office_rent,
        COALESCE(SUM(office_staff),  0) AS office_staff,
        COALESCE(SUM(other_fee),     0) AS other_fee,
        COALESCE(SUM(late_fee),      0) AS late_fee
    FROM member_share
")->fetch(PDO::FETCH_ASSOC);

$admission_fee = array_sum([
    $fees['idcard_fee'], $fees['passbook_fee'], $fees['softuses_fee'],
    $fees['sms_fee'],    $fees['office_rent'],  $fees['office_staff'],
    $fees['other_fee'],
]);

// ── Payments by type from member_payments ────────────────────────────────────
$pay = $pdo->query("
    SELECT
        COALESCE(SUM(CASE WHEN payment_method='Samity Share'   THEN amount ELSE 0 END), 0) AS samity_share,
        COALESCE(SUM(CASE WHEN payment_method='Uddokta Share'  THEN amount ELSE 0 END), 0) AS uddokta_share,
        COALESCE(SUM(CASE WHEN payment_method='Project Share'  THEN amount ELSE 0 END), 0) AS project_share,
        COALESCE(SUM(CASE WHEN payment_method NOT IN
            ('Samity Share','Uddokta Share','Project Share','admission') THEN amount ELSE 0 END), 0) AS monthly
    FROM member_payments WHERE status='A'
")->fetch(PDO::FETCH_ASSOC);

$total_deposits = $admission_fee + $fees['late_fee']
                + $pay['samity_share'] + $pay['uddokta_share']
                + $pay['project_share'] + $pay['monthly'];

// ── Payment done detail rows ─────────────────────────────────────────────────
$detailRows = $pdo->query("
    WITH uddokta_cte AS (
        SELECT mp.member_id, COALESCE(SUM(mp.project_share), 0) AS uddokta_shares
        FROM member_project mp
        JOIN project p ON p.id = mp.project_id
        WHERE p.project_name_en LIKE '%Uddokta%'
           OR p.project_name_bn LIKE '%উদ্যোক্তা%'
        GROUP BY mp.member_id
    ),
    share_data AS (
        SELECT ms.member_id,
               SUM(ms.samity_share)            AS samity_shares,
               SUM(ms.no_share - ms.samity_share) AS extra_shares
        FROM member_share ms GROUP BY ms.member_id
    ),
    pay_data AS (
        SELECT member_id,
               SUM(CASE WHEN payment_method='admission'     THEN amount ELSE 0 END) AS admission,
               SUM(CASE WHEN payment_method='Samity Share'  THEN amount ELSE 0 END) AS samity_share,
               SUM(CASE WHEN payment_method='Uddokta Share' THEN amount ELSE 0 END) AS uddokta_share,
               SUM(CASE WHEN payment_method='Project Share' THEN amount ELSE 0 END) AS project_share,
               SUM(CASE WHEN payment_method NOT IN
                   ('Samity Share','Uddokta Share','Project Share','admission')
                   THEN amount ELSE 0 END)                                          AS monthly
        FROM member_payments WHERE status='A' GROUP BY member_id
    )
    SELECT
        ROW_NUMBER() OVER (ORDER BY a.id) AS sn,
        a.id, a.member_code, a.name_bn,
        COALESCE(s.samity_shares,  0)                                          AS samity_shares,
        COALESCE(u.uddokta_shares, 0)                                          AS uddokta_shares,
        GREATEST(0, COALESCE(s.extra_shares,0) - COALESCE(u.uddokta_shares,0)) AS project_shares,
        COALESCE(p.admission,      0) AS admission,
        COALESCE(p.samity_share,   0) AS samity_share,
        COALESCE(p.uddokta_share,  0) AS uddokta_share,
        COALESCE(p.project_share,  0) AS project_share,
        COALESCE(p.monthly,        0) AS monthly
    FROM members_info a
    LEFT JOIN share_data  s ON s.member_id = a.id
    LEFT JOIN uddokta_cte u ON u.member_id = a.id
    LEFT JOIN pay_data    p ON p.member_id = a.id
    WHERE EXISTS (
        SELECT 1 FROM user_login c
        WHERE c.member_id = a.id AND c.status IN ('A','P')
    )
    ORDER BY a.id
")->fetchAll(PDO::FETCH_ASSOC);

// Column totals for the modal footer
$col_totals = [
    'samity_shares'  => 0, 'uddokta_shares' => 0, 'project_shares' => 0,
    'admission'      => 0, 'samity_share'   => 0,
    'uddokta_share'  => 0, 'project_share'  => 0,
    'monthly'        => 0,
];
foreach ($detailRows as $r) {
    foreach ($col_totals as $k => $_) $col_totals[$k] += (float)$r[$k];
}

// ── Member list helper ───────────────────────────────────────────────────────
function memberRows(PDO $pdo, string $status): string {
    $stmt = $pdo->prepare("
        SELECT a.member_code, a.name_bn, a.name_en, a.mobile
        FROM members_info a
        JOIN user_login b ON b.member_id = a.id
        WHERE b.status = ? AND b.role = 'user'
        ORDER BY b.id DESC
    ");
    $stmt->execute([$status]);
    $html = '';
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $html .= '<tr>'
            . '<td>' . htmlspecialchars($row['member_code']) . '</td>'
            . '<td>' . htmlspecialchars($row['name_bn'])     . '</td>'
            . '<td>' . htmlspecialchars($row['name_en'])     . '</td>'
            . '<td>' . htmlspecialchars($row['mobile'])      . '</td>'
            . '</tr>';
    }
    return $html ?: '<tr><td colspan="4" class="text-center text-muted">কোনো তথ্য নেই</td></tr>';
}
?>

<?php
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div>
        <h3 class="mb-3 text-primary fw-bold">একাউন্টস ড্যাশবোর্ড <span class="text-secondary">(Accounts Dashboard)</span></h3>
        <hr class="mb-4" />

        <!-- ── Member Status Cards ─────────────────────────────────────────── -->
        <?php
        $statusCards = [
            ['id'=>'approvedModal',  'label'=>'অনুমোদিত সদস্য',   'en'=>'Approved Members',    'icon'=>'✅', 'count'=>$counts['approved'],  'gradient'=>'#28a745,#20c997'],
            ['id'=>'processModal',   'label'=>'প্রক্রিয়াধীন সদস্য','en'=>'Processing Members',  'icon'=>'⏳', 'count'=>$counts['processed'], 'gradient'=>'#007bff,#0056b3'],
            ['id'=>'inactiveModal',  'label'=>'নিষ্ক্রিয় সদস্য',   'en'=>'Inactive Members',    'icon'=>'⏸️', 'count'=>$counts['inactive'],  'gradient'=>'#ffc107,#ff9800'],
            ['id'=>'rejectedModal',  'label'=>'প্রত্যাখ্যাত সদস্য', 'en'=>'Rejected Members',    'icon'=>'❌', 'count'=>$counts['rejected'],  'gradient'=>'#dc3545,#c82333'],
        ];
        ?>
        <div class="row g-4 mb-4">
            <?php foreach ($statusCards as $c): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center"
                     style="background:linear-gradient(135deg,<?= $c['gradient'] ?>);<?= $c['count'] > 0 ? 'cursor:pointer;' : '' ?>"
                     <?= $c['count'] > 0 ? 'data-bs-toggle="modal" data-bs-target="#' . $c['id'] . '"' : '' ?>>
                    <div class="card-body text-white">
                        <h6 class="mb-2"><?= $c['icon'] ?> <?= $c['label'] ?></h6>
                        <p class="mb-1 small"><?= $c['en'] ?></p>
                        <div class="display-5 fw-bold"><?= bn($c['count']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="col-md-12">
                <div class="card shadow-sm border-0 text-center"
                     style="background:linear-gradient(135deg,#667eea,#764ba2);<?= $paymentMemberCount > 0 ? 'cursor:pointer;' : '' ?>"
                     <?= $paymentMemberCount > 0 ? 'data-bs-toggle="modal" data-bs-target="#paymentDoneModal"' : '' ?>>
                    <div class="card-body text-white">
                        <h6 class="mb-2">💳 সদস্য পেমেন্ট</h6>
                        <p class="mb-1 small">Members Payment</p>
                        <div class="display-5 fw-bold"><?= bn($paymentMemberCount) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Financial Summary ──────────────────────────────────────────── -->
        <h5 class="mb-3 text-primary fw-bold">আর্থিক সারসংক্ষেপ <span class="text-secondary">(Financial Summary)</span></h5>

        <?php
        $feeCards = [
            ['icon'=>'🪪',  'label'=>'আইডি কার্ড ফি',  'en'=>'ID Card Fee',     'val'=>$fees['idcard_fee'],   'color'=>'text-secondary'],
            ['icon'=>'📖',  'label'=>'পাসবুক ফি',       'en'=>'Passbook Fee',    'val'=>$fees['passbook_fee'], 'color'=>'text-secondary'],
            ['icon'=>'💻',  'label'=>'সফটওয়্যার ফি',  'en'=>'Software Fee',    'val'=>$fees['softuses_fee'], 'color'=>'text-secondary'],
            ['icon'=>'📱',  'label'=>'এসএমএস ফি',       'en'=>'SMS Fee',         'val'=>$fees['sms_fee'],      'color'=>'text-secondary'],
            ['icon'=>'🏢',  'label'=>'অফিস ভাড়া',      'en'=>'Office Rent',     'val'=>$fees['office_rent'],  'color'=>'text-secondary'],
            ['icon'=>'👥',  'label'=>'অফিস কর্মচারী',   'en'=>'Office Staff',    'val'=>$fees['office_staff'], 'color'=>'text-secondary'],
            ['icon'=>'📋',  'label'=>'অন্যান্য ফি',    'en'=>'Other Fee',       'val'=>$fees['other_fee'],    'color'=>'text-secondary'],
            ['icon'=>'⏰',  'label'=>'বিলম্ব ফি',       'en'=>'Late Fee',        'val'=>$fees['late_fee'],     'color'=>'text-warning'],
            ['icon'=>'💳',  'label'=>'ভর্তি ফি',        'en'=>'Admission Fee',   'val'=>$admission_fee,        'color'=>'text-success'],
            ['icon'=>'🏦',  'label'=>'সমিতি শেয়ার',    'en'=>'Samity Share',    'val'=>$pay['samity_share'],  'color'=>'text-info'],
            ['icon'=>'🚀',  'label'=>'উদ্যোক্তা শেয়ার','en'=>'Uddokta Share',  'val'=>$pay['uddokta_share'], 'color'=>'text-danger'],
            ['icon'=>'📊',  'label'=>'প্রকল্প শেয়ার',  'en'=>'Project Share',   'val'=>$pay['project_share'], 'color'=>'text-primary'],
            ['icon'=>'📅',  'label'=>'মাসিক জমা',       'en'=>'Monthly Deposit', 'val'=>$pay['monthly'],       'color'=>'text-success'],
        ];
        ?>
        <div class="row g-4 mb-4">
            <?php foreach ($feeCards as $fc): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="<?= $fc['color'] ?> mb-2"><?= $fc['icon'] ?> <?= $fc['label'] ?></h6>
                        <p class="mb-1 small text-muted"><?= $fc['en'] ?></p>
                        <div class="h5 fw-bold <?= $fc['color'] ?> mb-0"><?= tk($fc['val']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ── Total Deposit ──────────────────────────────────────────────── -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-lg border-0" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <div class="card-body text-white text-center py-4">
                        <h4 class="mb-2">💰 মোট জমা পরিমাণ</h4>
                        <p class="mb-3">Total Deposit Amount</p>
                        <div class="display-4 fw-bold"><?= tk($total_deposits) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</div>

<!-- ── Member Status Modals ────────────────────────────────────────────────── -->
<?php
$modals = [
    ['id'=>'approvedModal', 'title'=>'✅ অনুমোদিত সদস্য তালিকা (Approved Members List)',    'hdr'=>'bg-success',  'thd'=>'table-success',  'status'=>'A'],
    ['id'=>'processModal',  'title'=>'⏳ প্রক্রিয়াধীন সদস্য তালিকা (Processing Members List)','hdr'=>'bg-primary',  'thd'=>'table-primary',  'status'=>'P'],
    ['id'=>'inactiveModal', 'title'=>'⏸️ নিষ্ক্রিয় সদস্য তালিকা (Inactive Members List)',  'hdr'=>'bg-warning',  'thd'=>'table-warning',  'status'=>'I'],
    ['id'=>'rejectedModal', 'title'=>'❌ প্রত্যাখ্যাত সদস্য তালিকা (Rejected Members List)','hdr'=>'bg-danger',   'thd'=>'table-danger',   'status'=>'R'],
];
foreach ($modals as $m): ?>
<div class="modal fade" id="<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header <?= $m['hdr'] ?> text-white">
                <h5 class="modal-title"><?= $m['title'] ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="<?= $m['thd'] ?>">
                            <tr><th>Member Code</th><th>Name (Bangla)</th><th>Name (English)</th><th>Mobile</th></tr>
                        </thead>
                        <tbody><?= memberRows($pdo, $m['status']) ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ── Payment Done Modal ─────────────────────────────────────────────────── -->
<div class="modal fade" id="paymentDoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                <h5 class="modal-title">💳 পেমেন্ট সম্পন্ন সদস্যদের তালিকা (Payment Done Members List)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end gap-2 mb-2">
                    <button class="btn btn-success btn-sm" onclick="downloadCSV()">⬇ Download CSV</button>
                    <button class="btn btn-danger btn-sm" onclick="downloadPDF()">⬇ Download PDF</button>
                </div>
                <div class="table-responsive" id="payment-done-table">
                    <table class="table table-bordered table-hover align-middle">
                        <thead style="background:linear-gradient(135deg,#667eea,#764ba2); color:#fff;">
                            <tr>
                                <th>#</th>
                                <th>সদস্য তথ্য</th>
                                <th class="text-center">সমিতি শেয়ার (সংখ্যা)</th>
                                <th class="text-center">উদ্যোক্তা শেয়ার (সংখ্যা)</th>
                                <th class="text-center">প্রকল্প শেয়ার (সংখ্যা)</th>
                                <th class="text-end">ভর্তি ফি</th>
                                <th class="text-end">সমিতি শেয়ার টাকা</th>
                                <th class="text-end">উদ্যোক্তা শেয়ার টাকা</th>
                                <th class="text-end">প্রকল্প শেয়ার টাকা</th>
                                <th class="text-end">মাসিক জমা</th>
                                <th class="text-end">সর্বমোট</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($detailRows as $r):
                            $row_total = $r['admission'] + $r['samity_share'] + $r['uddokta_share'] + $r['project_share'] + $r['monthly'];
                        ?>
                        <tr>
                            <td><?= bn($r['sn']) ?></td>
                            <td><?= bn($r['id']) ?><br><?= htmlspecialchars($r['member_code']) ?><br><?= htmlspecialchars($r['name_bn']) ?></td>
                            <td class="text-center"><?= bn($r['samity_shares']) ?></td>
                            <td class="text-center"><?= bn($r['uddokta_shares']) ?></td>
                            <td class="text-center"><?= bn($r['project_shares']) ?></td>
                            <td class="text-end"><?= tk($r['admission']) ?></td>
                            <td class="text-end"><?= tk($r['samity_share']) ?></td>
                            <td class="text-end"><?= tk($r['uddokta_share']) ?></td>
                            <td class="text-end"><?= tk($r['project_share']) ?></td>
                            <td class="text-end"><?= tk($r['monthly']) ?></td>
                            <td class="text-end fw-bold"><?= tk($row_total) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-dark fw-bold">
                            <?php
                            $ft = $col_totals;
                            $grand = $ft['admission'] + $ft['samity_share'] + $ft['uddokta_share'] + $ft['project_share'] + $ft['monthly'];
                            ?>
                            <tr>
                                <td colspan="2" class="text-end">সর্বমোট</td>
                                <td class="text-center"><?= bn($ft['samity_shares']) ?></td>
                                <td class="text-center"><?= bn($ft['uddokta_shares']) ?></td>
                                <td class="text-center"><?= bn($ft['project_shares']) ?></td>
                                <td class="text-end"><?= tk($ft['admission']) ?></td>
                                <td class="text-end"><?= tk($ft['samity_share']) ?></td>
                                <td class="text-end"><?= tk($ft['uddokta_share']) ?></td>
                                <td class="text-end"><?= tk($ft['project_share']) ?></td>
                                <td class="text-end"><?= tk($ft['monthly']) ?></td>
                                <td class="text-end"><?= tk($grand) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
var csvData = <?= json_encode(array_merge(
    // header row
    [['#','সদস্য ID','সদস্য কোড','সদস্যের নাম','সমিতি শেয়ার (সংখ্যা)','উদ্যোক্তা শেয়ার (সংখ্যা)','প্রকল্প শেয়ার (সংখ্যা)','ভর্তি ফি','সমিতি শেয়ার টাকা','উদ্যোক্তা শেয়ার টাকা','প্রকল্প শেয়ার টাকা','মাসিক জমা','সর্বমোট']],
    // data rows
    array_map(function($r) {
        $total = $r['admission'] + $r['samity_share'] + $r['uddokta_share'] + $r['project_share'] + $r['monthly'];
        return [
            $r['sn'], $r['id'], $r['member_code'], $r['name_bn'],
            $r['samity_shares'], $r['uddokta_shares'], $r['project_shares'],
            number_format((float)$r['admission'],    2),
            number_format((float)$r['samity_share'],  2),
            number_format((float)$r['uddokta_share'], 2),
            number_format((float)$r['project_share'], 2),
            number_format((float)$r['monthly'],       2),
            number_format($total, 2),
        ];
    }, $detailRows),
    // totals row
    (function() use ($col_totals) {
        $grand = $col_totals['admission'] + $col_totals['samity_share'] + $col_totals['uddokta_share'] + $col_totals['project_share'] + $col_totals['monthly'];
        return [['সর্বমোট','','','',
            $col_totals['samity_shares'], $col_totals['uddokta_shares'], $col_totals['project_shares'],
            number_format((float)$col_totals['admission'],    2),
            number_format((float)$col_totals['samity_share'],  2),
            number_format((float)$col_totals['uddokta_share'], 2),
            number_format((float)$col_totals['project_share'], 2),
            number_format((float)$col_totals['monthly'],       2),
            number_format($grand, 2),
        ]];
    })()
), JSON_UNESCAPED_UNICODE) ?>;

function downloadCSV() {
    var BOM = '﻿'; // UTF-8 BOM for Bengali text in Excel
    var rows = csvData.map(function(row) {
        return row.map(function(cell) {
            var s = String(cell === null || cell === undefined ? '' : cell);
            // wrap in quotes if contains comma, newline or quote
            if (s.indexOf(',') !== -1 || s.indexOf('"') !== -1 || s.indexOf('\n') !== -1) {
                s = '"' + s.replace(/"/g, '""') + '"';
            }
            return s;
        }).join(',');
    }).join('\r\n');

    var blob = new Blob([BOM + rows], { type: 'text/csv;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href     = url;
    a.download = 'payment-done.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function downloadPDF() {
    var element = document.getElementById('payment-done-table');
    var wrapper = document.createElement('div');
    wrapper.style.cssText = 'width:290mm;padding:15mm;box-sizing:border-box;background:#fff;';
    wrapper.innerHTML = element.innerHTML;
    document.body.appendChild(wrapper);
    html2pdf().set({
        margin: 0,
        filename: 'payment-done-table.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).from(wrapper).save().then(function() {
        document.body.removeChild(wrapper);
    });
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
