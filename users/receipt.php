<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

function englishToBanglaNumber($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

$selected_year = isset($_POST['payment_year']) ? (int)$_POST['payment_year'] : (int)date('Y');

$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';

/* --------- Number to Words Function --------- */
function numberToWords($num) {
    $ones = [
        0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four",
        5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine",
        10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen",
        14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen",
        18 => "Eighteen", 19 => "Nineteen"
    ];

    $tens = [
        2 => "Twenty", 3 => "Thirty", 4 => "Forty",
        5 => "Fifty", 6 => "Sixty", 7 => "Seventy",
        8 => "Eighty", 9 => "Ninety"
    ];

    $levels = ["", "Thousand", "Million", "Billion"];

    if ($num == 0) return "Zero";

    $words = "";
    $level = 0;

    while ($num > 0) {
        $chunk = $num % 1000;
        if ($chunk > 0) {
            $chunkWords = "";
            $hundreds = floor($chunk / 100);
            $remainder = $chunk % 100;

            if ($hundreds > 0) {
                $chunkWords .= $ones[$hundreds] . " Hundred ";
            }

            if ($remainder > 0) {
                if ($remainder < 20) {
                    $chunkWords .= $ones[$remainder] . " ";
                } else {
                    $chunkWords .= $tens[floor($remainder / 10)] . " ";
                    if ($remainder % 10 > 0) {
                        $chunkWords .= $ones[$remainder % 10] . " ";
                    }
                }
            }

            $words = $chunkWords . $levels[$level] . " " . $words;
        }

        $num = floor($num / 1000);
        $level++;
    }

    return trim($words);
}

/* --------- Setup Info from Session --------- */
$logo     = $_SESSION['setup']['logo'] ?? '';
$siteName = $_SESSION['setup']['site_name_bn'] ?? '';
$slogan_bn = $_SESSION['setup']['slogan_bn'] ?? '';
$slogan_en = $_SESSION['setup']['slogan_en'] ?? ''; 
$slogan   = $slogan_bn . ($slogan_en ? ' ( ' . $slogan_en . ' )' : '');
$reg_no   = $_SESSION['setup']['registration_no'] ?? '';
$address  = $_SESSION['setup']['address'] ?? '';
$phone1   = $_SESSION['setup']['phone1'] ?? '';
$phone2   = $_SESSION['setup']['phone2'] ?? '';
$phone    = $phone1 . ($phone2 ? ', ' . $phone2 : '');
$email    = $_SESSION['setup']['email'] ?? '';

$member_id = $_SESSION['member_id'];

/* --------- Fetch Member Info --------- */
$stmt = $pdo->prepare("SELECT name_bn, member_code FROM members_info WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

/* --------- Fetch Receipt if POST --------- */
$receipt = null;
$payment_type = '';
$payment_year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_type'], $_POST['payment_year'])) {
    $payment_type = $_POST['payment_type'];
    $payment_year = $_POST['payment_year'];

    if (in_array($payment_type, ['Samity Share', 'Project Share'])) {
        $stmt = $pdo->prepare("
            SELECT b.no_share, 
                   SUM(a.amount) AS total_amount,
                   a.payment_method, a.payment_year,
                   MAX(a.trans_no) AS trans_no,
                   MAX(DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i')) AS created_at,
                   MAX(a.bank_trans_no) AS bank_trans_no,
                   MAX(a.bank_pay_date) AS bank_pay_date,
                   MAX(a.pay_mode) AS pay_mode,
                   a.for_fees
            FROM member_payments a
            JOIN member_share b ON a.member_id = b.member_id AND a.member_code = b.member_code
            WHERE a.member_id = ? AND a.payment_method = ? AND a.payment_year = ? AND a.status = 'A'
            GROUP BY b.no_share, a.payment_method, a.payment_year
            LIMIT 1
        ");
        $stmt->execute([$member_id, $payment_type, $payment_year]);
    } else {
        // Handle monthly payments by mapping month names to payment_method 'Monthly' and for_fees as the month
        $months = ['january','february','march','april','may','june','july','august','september','october','november','december'];
        if (in_array(strtolower($payment_type), $months)) {
            $stmt = $pdo->prepare("
                SELECT b.no_share, a.trans_no, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i') AS created_at,  
                       a.payment_method, a.payment_year, a.bank_trans_no, a.bank_pay_date, a.pay_mode, a.for_fees,
                       a.amount AS total_amount
                FROM member_payments a
                JOIN member_share b ON a.member_id = b.member_id AND a.member_code = b.member_code
                WHERE a.member_id = ? AND a.payment_method = 'Monthly' AND a.for_fees = ? AND a.payment_year = ? AND a.status = 'A'
                ORDER BY a.id DESC
                LIMIT 1
            ");
            $stmt->execute([$member_id, ucfirst(strtolower($payment_type)), $payment_year]);
        } else {
            $stmt = $pdo->prepare("
                SELECT b.no_share, a.trans_no, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i') AS created_at,  
                       a.payment_method, a.payment_year, a.bank_trans_no, a.bank_pay_date, a.pay_mode, a.for_fees,
                       a.amount AS total_amount
                FROM member_payments a
                JOIN member_share b ON a.member_id = b.member_id AND a.member_code = b.member_code
                WHERE a.member_id = ? AND a.payment_method = ? AND a.payment_year = ? AND a.status = 'A'
                ORDER BY a.id DESC
                LIMIT 1
            ");
            $stmt->execute([$member_id, $payment_type, $payment_year]);
        }
    }
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get Cashier signature
    $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
    $stmt->execute(['cashier']);
    $cashier_sign = $stmt->fetch(PDO::FETCH_ASSOC);
    $cashier_signature = $cashier_sign ? $cashier_sign['banner_image'] : '';

    if ($receipt) {
        $receipt['total_amount_words'] = numberToWords((int)$receipt['total_amount']) . ' Taka Only';
    }
}
?>

<!-- ===== STYLE ===== -->
<style>
:root {
    --bs-corporate-blue: #002D59;
    --bs-corporate-orange: #F8971D;
}
.receipt-container { width: 800px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden; }
.dotted-input-solid { border: none; border-bottom: 1px solid #000; background: transparent; outline: none; padding: 0 5px; flex-grow: 1; }
.header-bg { position: relative; }
.header-bg::before { content:''; position:absolute; top:0; left:0; width:35%; height:100%;  }
.header-content { position:relative; z-index:2; }
.logo-box-custom { width:100px; height:100px; margin-right:10px; }
.footer-bg { border-top: 2px solid var(--bs-corporate-blue); background: #f1f1f1; position:relative; z-index:10; }

.receipt-institute {
    text-align: right;
    font-size: 14px;
    color: #333;
}

.receipt-body::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 250px;
    height: 250px;
    transform: translate(-50%, -50%);
    background: url('<?php echo BASE_URL; ?>assets/img/logo.png') no-repeat center center;
    background-size: 250px 250px;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,0.15));
    opacity: 0.15;
    pointer-events: none;
    z-index: 0;
}

.button-center {
  display: flex;
  align-items: center;
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
            <h3 class="mb-3 text-primary fw-bold">Payment Receipt <span class="text-secondary">(পেমেন্ট রসিদ)</span></h3>
            <hr class="mb-4" />

            <!-- ===== Receipt Form ===== -->
            <form method="post" class="mb-4 row g-3">
              <div class="col-12 col-md-4 mb-3">
                <label for="payment_type" class="form-label">Payments (পেমেন্ট)</label>
                <select class="form-select" id="payment_type" name="payment_type" required>
                  <option value="">Select (বাছাই করুন)</option>
                  <?php
                  if ($status === 'P') {
                      $months = [
                          'admission' => 'সদস্য এন্ট্রি ফি',
                          'Samity Share' => 'সমিতি শেয়ার ফি',
                          'Project Share' => 'প্রকল্প শেয়ার ফি',
                          'january'   => 'January (জানুয়ারি)',
                          'february'  => 'February (ফেব্রুয়ারি)',
                          'march'     => 'March (মার্চ)',
                          'april'     => 'April (এপ্রিল)',
                          'may'       => 'May (মে)',
                          'june'      => 'June (জুন)',
                          'july'      => 'July (জুলাই)',
                          'august'    => 'August (আগস্ট)',
                          'september' => 'September (সেপ্টেম্বর)',
                          'october'   => 'October (অক্টোবর)',
                          'november'  => 'November (নভেম্বর)',
                          'december'  => 'December (ডিসেম্বর)'
                      ];
                  } 
                  foreach ($months as $key => $val): ?>
                    <option value="<?= $key ?>" <?= (!empty($payment_type) && $payment_type == $key) ? 'selected' : '' ?>>
                      <?= $val ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12 col-md-2 mb-3">
                <label for="payment_year" class="form-label">Year (বছর)</label>
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
              </div>

              <div class="col-12 col-md-3 mb-3 button-center">
                <button type="submit" class="btn btn-primary w-75">Generate Receipt (রসিদ তৈরি করুন)</button>
              </div>

              <div class="col-12 col-md-3 mb-3 button-center">
                <button type="button" class="btn btn-danger" onclick="downloadPDF()">
                    <i class="fas fa-file-pdf me-2"></i>PDF ডাউনলোড করুন
                </button>
              </div>
            </form>

            <!-- ===== Show Receipt or Alert ===== -->
            <?php if ($receipt): ?>
              <div class="container receipt-container bg-white mt-5 p-0" id ="receipt-content">
    <div class="row m-0 p-0">
        <div class="col-md-12 p-0">
            <div class="header-bg py-3 px-4 d-flex align-items-center">
                <!-- Logo and Slogan -->
                <div class="col-md-6 d-flex align-items-center">
                    <div class="header-content align-items-center">
                        <div class="logo-box-custom align-items-center">
                            <div class="p-2" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="<?php echo BASE_URL; ?>assets/img/<?= htmlspecialchars($logo) ?>" alt="Logo" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Company Details -->
                <div class="col-md-6 text-end">
                    <div class="receipt-institute">
                        <p class="fw-bold mb-0"><?= $siteName ?? 'Company Name Here' ?></p>
                        <p class="mb-0"><?= $reg_no ?? 'Your Business Address 0000' ?></p>
                        <p class="mb-0 small">📞 <?= $phone ?? '0000-000000' ?> <br/>✉️ <?= $email ?? 'Your Mail Here' ?></p>
                        <p class="mb-0 small"><?= $slogan ?></p>
                    </div>
                </div>
            </div>
            <hr class="m-0 border border-2" style="border-color: var(--bs-corporate-orange) !important;">

            <!-- Receipt Information -->
            <div class="receipt-body">

            
            <div class="row my-3 px-4 small">
                <div class="col-6 d-flex align-items-center">
                    <label class="fw-bold me-2" style="color: var(--bs-corporate-blue);">NO :</label>
                    <span class="text-dark"><?= htmlspecialchars($receipt['trans_no'] ?? 'trans_no') ?></span>
                </div>
                <div class="col-6 d-flex justify-content-end align-items-center">
                    <label class="fw-bold me-2" style="color: var(--bs-corporate-blue);">Date :</label>
                    <span class="text-dark"><?= htmlspecialchars($receipt['created_at'] ?? 'created_at') ?></span>
                </div>
            </div>

            <!-- Receipt Details -->
            <div class="px-4 py-3">
                <div class="mb-3 d-flex align-items-center">
                    <label class="me-2 text-dark">Received with thanks from</label>
                    <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                        <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($member['name_bn'] ?? 'Member Name') ?></h5>
                        <span class="text-muted"><small>Member Code: <?= htmlspecialchars($member['member_code'] ?? 'M-0000') ?></small></span> (<span class="text-muted"><small>Share: <?= htmlspecialchars($receipt['no_share'] ?? '0') ?></small></span>)
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">For Purpose</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['for_fees'] ?? 'For Purpose') ?></h5>
                        </div>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">Year</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['payment_year'] ?? 'Payment Year') ?></h5>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">Bank Trans No</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['bank_trans_no'] ?? 'bank_trans_no') ?></h5>
                        </div>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">Bank Pay Date</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['bank_pay_date'] ?? '') ?></h5>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">Amount of Taka</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['total_amount'] ?? 'total_amount') ?></h5>
                        </div>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <label class="me-2 text-dark">By Paid</label>
                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1">
                            <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['pay_mode'] ?? '') ?></h5>
                        </div>
                    </div>
                </div>

                <div class="mb-2 d-flex align-items-center">
                    <label class="me-2 text-dark">In words: </label>
                    <div class="flex-grow-1">
                        <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['total_amount_words'] ?? 'total_amount_words') ?></h5>
                    </div>
                </div>
              </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="row m-0 p-0">
            <div class="col-12 p-0">
                <div class="footer-bg py-3 px-4 d-flex justify-content-between align-items-end">
                    <!-- Amount -->
                    <div class="d-flex align-items-center me-3" style="z-index: 12;">
                        <p class="fw-bold mb-0 small">Amount=</p>
                        <div class="bg-white border border-primary ms-2" style="width: 100px; height: 25px;">
                            <?= htmlspecialchars($receipt['total_amount'] ?? 'total_amount') ?>
                        </div>
                    </div>

                    <!-- Print Date -->
                    <div class="d-flex align-items-end me-3" style="width: 35%; z-index: 12;">
                        <label class="fw-bold me-2">Print Date :</label>
                        <span class="text-dark"><?= date('d-m-Y H:i') ?></span>
                    </div>

                    <!-- Signature -->
                    <div class="signature-box">
                            <?php if (!empty($cashier_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($cashier_signature) ?>" alt="Cashier Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">কোষাধ্যক্ষ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- In case of POST request -->
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning">
        If you have made the payment, please wait for approval and then print the receipt. (যদি আপনার পেমেন্ট হয়ে যায়, তাহলে অনুমোদনের জন্য অপেক্ষা করুন এবং তারপর রসিদটি প্রিন্ট করুন।)
    </div>
<?php endif; ?>


          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('receipt-content');
    const memberCode = '<?= htmlspecialchars($member['member_code']) ?>';
    const year = '<?= $selected_year ?>';
    
    // Create a wrapper for proper sizing
    const wrapper = document.createElement('div');
    wrapper.style.width = '300mm'; // A4 width
    wrapper.style.padding = '40mm';
    wrapper.style.boxSizing = 'border-box';
    wrapper.style.backgroundColor = 'white';
    wrapper.innerHTML = element.innerHTML;
    
    // Temporarily add to body
    document.body.appendChild(wrapper);
    
    const opt = {
        margin: 0,
        filename: `Receipt_${memberCode}_${year}.pdf`,
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
            orientation: 'landscape'
        }
    };
    
    html2pdf().set(opt).from(wrapper).save().then(() => {
        // Remove temporary wrapper
        document.body.removeChild(wrapper);
    });
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
