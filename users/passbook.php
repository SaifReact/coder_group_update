<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

/* --------- English to Bangla Number Converter --------- */
function englishToBanglaNumber($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

$member_id = $_SESSION['member_id'];

// Get selected year from POST or default to current year
$selected_year = isset($_POST['payment_year']) ? (int)$_POST['payment_year'] : (int)date('Y');

/* --------- Fetch Member Info --------- */
$stmt = $pdo->prepare("SELECT name_bn, member_code FROM members_info WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

/* --------- Fetch All Payment Records --------- */
$stmt = $pdo->prepare("
    SELECT a.payment_method, a.payment_year, a.trans_no, 
           a.for_fees,
           DATE_FORMAT(
               CASE 
                   WHEN a.bank_pay_date = '0000-00-00' OR a.bank_pay_date IS NULL 
                   THEN a.created_at 
                   ELSE a.bank_pay_date 
               END, 
               '%d-%m-%Y'
           ) AS payment_date,
           a.amount,
           a.serial_no,
           CASE 
               WHEN a.amount > (SELECT fee FROM utils WHERE fee_type = 'monthly' LIMIT 1) 
               THEN a.amount - (SELECT fee FROM utils WHERE fee_type = 'monthly' LIMIT 1)
               ELSE 0 
           END AS late_fee
    FROM member_payments a
    WHERE a.member_id = ? AND a.status = 'A' AND a.payment_year = ?
    ORDER BY 
        CASE 
            WHEN a.payment_method = 'admission' THEN 1
            WHEN a.payment_method = 'Samity Share' THEN 2
            WHEN a.payment_method = 'Project Share' THEN 3
            WHEN a.payment_method = 'Monthly' THEN 4
            ELSE 99
        END,
        CASE WHEN a.payment_method = 'Monthly' THEN 
            FIELD(a.for_fees, 'january','february','march','april','may','june','july','august','september','october','november','december')
        ELSE 0 END,
        a.serial_no
");
$stmt->execute([$member_id, $selected_year]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!-- ===== STYLE ===== -->
<style>
:root {
    --bs-corporate-blue: #002D59;
    --bs-corporate-orange: #F8971D;
}

/* Passbook Styles */
.passbook-page {
    min-height: 600px;
}
.passbook-page table {
    font-size: 0.85rem;
}
.passbook-page table th {
    background-color: #f8f9fa;
    font-weight: 600;
    padding: 8px 4px;
}
.passbook-page table td {
    padding: 8px 4px;
    vertical-align: middle;
}
.passbook-page table tbody tr {
    height: 40px;
}
</style>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
         <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <div class="row">
                <div class="col-12 col-md-6">
                    <form method="POST" class="mb-0">
                        <label for="payment_year" class="form-label fw-bold">বছর নির্বাচন করুন:</label>
                        <select name="payment_year" id="payment_year" class="form-select" onchange="this.form.submit()">
                            <?php
                            $current_year = (int)date('Y');
                            for ($i = 0; $i < 2; $i++) {
                                $year = $current_year - $i;
                                $selected = ($year == $selected_year) ? 'selected' : '';
                                echo '<option value="' . $year . '" ' . $selected . '>' . englishToBanglaNumber($year) . '</option>';
                            }
                            ?>
                          </select>
                        </form>
                      </div>
                      <div class="col-12 col-md-6 text-end align-self-end">
                        <button type="button" class="btn btn-danger" onclick="downloadPDF()">
                          <i class="fas fa-file-pdf me-2"></i>PDF ডাউনলোড করুন
                        </button>
                      </div>
                    </div>
                    
                    <div id="passbook-content">
                      <h6 class="text-center fw-bold my-3">পাশবুক বিবরণী - <?= englishToBanglaNumber($selected_year) ?></h6>
                      <p class="text-center mb-2"><strong>সদস্য নাম:</strong> <?= htmlspecialchars($member['name_bn']) ?> | <strong>সদস্য কোড:</strong> <?= englishToBanglaNumber(htmlspecialchars($member['member_code'])) ?></p>
                    <table class="table table-bordered table-sm">
                      <thead>
                        <tr>
                          <th>মাস</th>
                          <th style="text-align: center;">তারিখ</th>
                          <th style="text-align: center;">ট্রান্সেকশন নং</th>
                          <th style="text-align: right;">টাকা/কিস্তির পরিমান</th>
                          <th style="text-align: right;">জরিমানা</th>
                          <th style="text-align: right;">সর্বমোট</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // Define all months in order
                        $all_months = [
                            'admission' => 'সদস্য এন্ট্রি ফি',
                            'Samity Share' => 'সমিতি শেয়ার ফি',
                            'Project Share' => 'প্রকল্প শেয়ার ফি',
                            'january' => 'জানুয়ারি',
                            'february' => 'ফেব্রুয়ারি',
                            'march' => 'মার্চ',
                            'april' => 'এপ্রিল',
                            'may' => 'মে',
                            'june' => 'জুন',
                            'july' => 'জুলাই',
                            'august' => 'আগস্ট',
                            'september' => 'সেপ্টেম্বর',
                            'october' => 'অক্টোবর',
                            'november' => 'নভেম্বর',
                            'december' => 'ডিসেম্বর'
                        ];
                        
                        // Calculate totals
                        $total_installment = 0;
                        $total_late_fee = 0;
                        $total_amount = 0;
                        
                        foreach ($payments as $payment) {
                            // For admission, Samity Share, and Project Share, show full amount as installment (no late fee)
                            if ($payment['payment_method'] == 'admission' || $payment['payment_method'] == 'Samity Share' || $payment['payment_method'] == 'Project Share') {
                                $installment = $payment['amount'];
                                $late_fee = 0;
                            } else {
                                // For monthly payments, calculate late fee
                                $installment = $payment['amount'] - $payment['late_fee'];
                                $late_fee = $payment['late_fee'];
                            }
                            
                            $total_installment += $installment;
                            $total_late_fee += $late_fee;
                            $total_amount += $payment['amount'];
                        }
                        
                        // Display all months with data if available
                        $share_transaction_count = 0;
                        $share_total = 0;
                        
                        foreach ($all_months as $method => $month_name) {
                            // For share payments, display all transactions
                            if ($method === 'Samity Share' || $method === 'Project Share') {
                                $share_payments = array_filter($payments, function($p) use ($method) {
                                    return $p['payment_method'] === $method;
                                });
                                if (count($share_payments) > 0) {
                                    foreach ($share_payments as $payment) {
                                        $share_transaction_count++;
                                        $installment = $payment['amount'];
                                        $late_fee = 0;
                                        $share_total += $installment;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($month_name) ?> (<?= englishToBanglaNumber($share_transaction_count) ?>)</td>
                                            <td style="text-align: center;"><?= englishToBanglaNumber(htmlspecialchars($payment['payment_date'])) ?></td>
                                            <td style="text-align: center;"><?= englishToBanglaNumber(htmlspecialchars($payment['trans_no'])) ?></td>
                                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($installment, 2)) ?></td>
                                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($late_fee, 2)) ?></td>
                                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($payment['amount'], 2)) ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    // No share payment found
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($month_name) ?></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                    </tr>
                                    <?php
                                }
                                continue; // Skip to next method
                            }

                            // For monthly payments, match payment_method = 'Monthly' and for_fees = month (case-insensitive)
                            $found = false;
                            foreach ($payments as $payment) {
                                if (
                                    ($method == 'admission' && $payment['payment_method'] === 'admission') ||
                                    ($method != 'admission' && $payment['payment_method'] === 'Monthly' && strtolower($payment['for_fees']) === strtolower($method))
                                ) {
                                    $found = true;
                                    if ($method == 'admission') {
                                        $installment = $payment['amount'];
                                        $late_fee = 0;
                                    } else {
                                        $installment = $payment['amount'] - $payment['late_fee'];
                                        $late_fee = $payment['late_fee'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($month_name) ?></td>
                                        <td style="text-align: center;"><?= englishToBanglaNumber(htmlspecialchars($payment['payment_date'])) ?></td>
                                        <td style="text-align: center;"><?= englishToBanglaNumber(htmlspecialchars($payment['trans_no'])) ?></td>
                                        <td style="text-align: right;"><?= englishToBanglaNumber(number_format($installment, 2)) ?></td>
                                        <td style="text-align: right;"><?= englishToBanglaNumber(number_format($late_fee, 2)) ?></td>
                                        <td style="text-align: right;"><?= englishToBanglaNumber(number_format($payment['amount'], 2)) ?></td>
                                    </tr>
                                    <?php
                                    break;
                                }
                            }
                            // If no payment found, show empty row
                            if (!$found) {
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($month_name) ?></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <!-- Total Row -->
                        <tr class="fw-bold" style="background-color: #f0f0f0;">
                            <td colspan="3" style="text-align: right;">সর্বমোট:</td>
                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($total_installment, 2)) ?></td>
                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($total_late_fee, 2)) ?></td>
                            <td style="text-align: right;"><?= englishToBanglaNumber(number_format($total_amount, 2)) ?></td>
                        </tr>
                      </tbody>
                    </table>
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
                    </div><!-- End passbook-content -->
                </div>
            </div>
        </div>
    </main>
  </div>
</div>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('passbook-content');
    const memberCode = '<?= htmlspecialchars($member['member_code']) ?>';
    const year = '<?= $selected_year ?>';
    
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
        filename: `Passbook_${memberCode}_${year}.pdf`,
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
