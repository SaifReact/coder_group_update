<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';


// Helper: English to Bangla digits
function en2bn($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return str_replace($en, $bn, $number);
}

function bnNumberToWords($number) {
    $bn_numbers = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $en_numbers = ['0','1','2','3','4','5','6','7','8','9'];
    $places = ['', 'হাজার', 'লক্ষ', 'কোটি'];
    $number = (int)$number;
        if ($number == 0) return 'শূন্য';
        $words = [
            0 => '', 1 => 'এক', 2 => 'দুই', 3 => 'তিন', 4 => 'চার', 5 => 'পাঁচ', 6 => 'ছয়', 7 => 'সাত', 8 => 'আট', 9 => 'নয়',
            10 => 'দশ', 11 => 'এগারো', 12 => 'বারো', 13 => 'তেরো', 14 => 'চৌদ্দ', 15 => 'পনেরো', 16 => 'ষোল', 17 => 'সতেরো', 18 => 'আঠারো', 19 => 'উনিশ',
            20 => 'বিশ', 21 => 'একুশ', 22 => 'বাইশ', 23 => 'তেইশ', 24 => 'চব্বিশ', 25 => 'পঁচিশ', 26 => 'ছাব্বিশ', 27 => 'সাতাশ', 28 => 'আটাশ', 29 => 'ঊনত্রিশ',
            30 => 'ত্রিশ', 31 => 'একত্রিশ', 32 => 'বত্রিশ', 33 => 'তেত্রিশ', 34 => 'চৌত্রিশ', 35 => 'পঁয়ত্রিশ', 36 => 'ছত্রিশ', 37 => 'সাঁইত্রিশ', 38 => 'আটত্রিশ', 39 => 'ঊনচল্লিশ',
            40 => 'চল্লিশ', 41 => 'একচল্লিশ', 42 => 'বিয়াল্লিশ', 43 => 'তেতাল্লিশ', 44 => 'চুয়াল্লিশ', 45 => 'পঁয়তাল্লিশ', 46 => 'ছেচল্লিশ', 47 => 'সাতচল্লিশ', 48 => 'আটচল্লিশ', 49 => 'ঊনপঞ্চাশ',
            50 => 'পঞ্চাশ', 51 => 'একান্ন', 52 => 'বাহান্ন', 53 => 'তিপ্পান্ন', 54 => 'চুয়ান্ন', 55 => 'পঞ্চান্ন', 56 => 'ছাপ্পান্ন', 57 => 'সাতান্ন', 58 => 'আটান্ন', 59 => 'ঊনষাট',
            60 => 'ষাট', 61 => 'একষট্টি', 62 => 'বাষট্টি', 63 => 'তেষট্টি', 64 => 'চৌষট্টি', 65 => 'পঁয়ষট্টি', 66 => 'ছেষট্টি', 67 => 'সাতষট্টি', 68 => 'আটষট্টি', 69 => 'ঊনসত্তর',
            70 => 'সত্তর', 71 => 'একাত্তর', 72 => 'বাহাত্তর', 73 => 'তিয়াত্তর', 74 => 'চুয়াত্তর', 75 => 'পঁচাত্তর', 76 => 'ছিয়াত্তর', 77 => 'সাতাত্তর', 78 => 'আটাত্তর', 79 => 'ঊনআশি',
            80 => 'আশি', 81 => 'একাশি', 82 => 'বিরাশি', 83 => 'তিরাশি', 84 => 'চুরাশি', 85 => 'পঁচাশি', 86 => 'ছিয়াশি', 87 => 'সাতাশি', 88 => 'আটাশি', 89 => 'ঊননব্বই',
            90 => 'নব্বই', 91 => 'একানব্বই', 92 => 'বিরানব্বই', 93 => 'তিরানব্বই', 94 => 'চুরানব্বই', 95 => 'পঁচানব্বই', 96 => 'ছিয়ানব্বই', 97 => 'সাতানব্বই', 98 => 'আটানব্বই', 99 => 'নিরানব্বই',
            100 => 'একশত', 200 => 'দুইশত', 300 => 'তিনশত', 400 => 'চারশত', 500 => 'পাঁচশত', 600 => 'ছয়শত', 700 => 'সাতশত', 800 => 'আটশত', 900 => 'নয়শত'
        ];
        $parts = [];
        $parts['কোটি'] = (int)($number / 10000000);
        $number = $number % 10000000;
        $parts['লক্ষ'] = (int)($number / 100000);
        $number = $number % 100000;
        $parts['হাজার'] = (int)($number / 1000);
        $number = $number % 1000;
        $parts['শত'] = (int)($number / 100);
        $number = $number % 100;
        $parts[''] = $number;
        $out = [];
        foreach ($parts as $label => $val) {
            if ($val) {
                if ($label == 'শত') {
                    $out[] = ($val == 1 ? 'একশত' : $words[$val*100]) . '';
                } elseif ($label == '') {
                    if ($val > 0) $out[] = $words[$val];
                } else {
                    if ($val > 0) $out[] = $words[$val] . ' ' . $label;
                }
            }
        }
        return implode(' ', $out);
}

// Helper: Format date to Bangla dd/mm/yyyy
function formatBanglaDate($date) {
    if (!$date) return '';
    $d = date('d/m/Y', strtotime($date));
    return en2bn($d);
}

$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$where = '';
$params = [];
if ($from_date && $to_date) {
    $where = "WHERE exp_date BETWEEN :from_date AND :to_date";
    $params['from_date'] = $from_date;
    $params['to_date'] = $to_date;
}

$sql = "SELECT e.*, c.name AS category_name FROM expenses e LEFT JOIN expense_category c ON e.exp_cat = c.id $where ORDER BY e.exp_date DESC, e.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get President signature
$stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
$stmt->execute(['president']);
$president_sign = $stmt->fetch(PDO::FETCH_ASSOC);
$president_signature = $president_sign ? $president_sign['banner_image'] : '';

// Get Secretary signature
$stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
$stmt->execute(['secretary']);
$secretary_sign = $stmt->fetch(PDO::FETCH_ASSOC);
$secretary_signature = $secretary_sign ? $secretary_sign['banner_image'] : '';

// Get Cashier signature
$stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
$stmt->execute(['cashier']);
$cashier_sign = $stmt->fetch(PDO::FETCH_ASSOC);
$cashier_signature = $cashier_sign ? $cashier_sign['banner_image'] : '';

include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Expense Voucher <span class="text-secondary">(ব্যয় ভাউচার)</span></h3>
                <hr class="mb-4" />
                <form method="get" class="row g-3 mb-4">
                    <div class="col-auto">
                        <label for="from_date" class="col-form-label">From Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date); ?>" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <label for="to_date" class="col-form-label">To Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date); ?>" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger" onclick="downloadPDF()">
                          <i class="fas fa-file-pdf me-2"></i>PDF ডাউনলোড করুন
                        </button>
                    </div>
                </form>
                <div class="table-responsive" id="voucher">
                    <div class="d-flex align-items-center justify-content-between mb-2" style="gap: 1rem;">
                        <div style="flex:1;">
                            <img src="../assets/img/logo.png" alt="Logo" style="height:50px;max-width:120px;object-fit:contain;">
                        </div>
                        <div style="flex:2;text-align:center;">
                            <h4 class="fw-bold m-0">Expense Voucher</h4>
                        </div>
                        <div style="flex:1;text-align:right;font-size:1rem;">
                            <div>থেকে: <strong><?= formatBanglaDate($from_date); ?></strong></div>
                            <div>পর্যন্ত: <strong><?= formatBanglaDate($to_date); ?></strong></div>
                        </div>
                    </div>
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            if ($expenses) {
                                $i = 1;
                                foreach ($expenses as $exp) {
                                    $total += $exp['amount'];
                                    echo '<tr>';
                                    echo '<td>' . $i++ . '</td>';
                                    echo '<td>' . formatBanglaDate($exp['exp_date']) . '</td>';
                                    echo '<td>' . htmlspecialchars($exp['category_name'] ?? '') . '</td>';
                                    echo '<td>' . htmlspecialchars($exp['note']) . '</td>';
                                    echo '<td>' . en2bn(number_format($exp['amount'], 2)) . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">No records found.</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th><?= en2bn(number_format($total, 2)); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mt-4 mb-2">
                        <strong>মোট (কথায়):</strong>
                        <span style="font-style:italic; color:#2c3e50;">
                            <?php
                            if ($total > 0) {
                                echo bnNumberToWords($total) . ' মাত্র';
                            } else {
                                echo 'শূন্য টাকা মাত্র';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="signature-section">
                        <div class="signature-box">
                            <?php if (!empty($president_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($president_signature) ?>" alt="President Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সভাপতি</div>
                        </div>
                        <div class="signature-box">
                            <?php if (!empty($cashier_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($cashier_signature) ?>" alt="Cashier Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">কোষাধ্যক্ষ</div>
                        </div>
                        <div class="signature-box">
                            <?php if (!empty($secretary_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($secretary_signature) ?>" alt="Secretary Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সম্পাদক</div>
                        </div>
                      </div>
                    </div>
                </div>
        </div>
    </div>
</main>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('voucher');
    
    // Create a wrapper for proper sizing
    const wrapper = document.createElement('div');
    wrapper.style.width = '210mm'; // A4 width
    wrapper.style.padding = '15mm';
    wrapper.style.boxSizing = 'border-box';
    wrapper.style.backgroundColor = 'white';
    wrapper.innerHTML = element.innerHTML;
    
    // Temporarily add to body
    document.body.appendChild(wrapper);
    
    const opt = {
        margin: 0,
        filename: `Expense_Voucher.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            letterRendering: true,
            allowTaint: true,
            scrollY: -window.scrollY,
            scrollX: 0
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        }
    };
    
    html2pdf().set(opt).from(wrapper).save().then(() => {
        // Remove temporary wrapper
        document.body.removeChild(wrapper);
    });
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
