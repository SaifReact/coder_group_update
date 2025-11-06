<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
$current_year = (int)date('Y');

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

    $stmt = $pdo->prepare("
        SELECT b.no_share, a.trans_no, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i') AS created_at,  
               a.payment_method, a.payment_year, a.bank_trans_no, a.bank_pay_date,
               SUM(a.amount) AS total_amount,
               SUM(CASE WHEN a.for_fees = 'idcard_fee' THEN a.amount ELSE 0 END) AS idcard_fee_amount,
               SUM(CASE WHEN a.for_fees = 'passbook_fee' THEN a.amount ELSE 0 END) AS passbook_fee_amount,
               SUM(CASE WHEN a.for_fees = 'other_fee' THEN a.amount ELSE 0 END) AS other_fee_amount,
               SUM(CASE WHEN a.for_fees = 'softuses_fee' THEN a.amount ELSE 0 END) AS softuses_fee_amount,
               SUM(a.amount) 
                 - SUM(CASE WHEN a.for_fees IN ('idcard_fee','passbook_fee','other_fee','softuses_fee') 
                            THEN a.amount ELSE 0 END) AS net_amount
        FROM member_payments a
        JOIN member_share b ON a.member_id = b.member_id AND a.member_code = b.member_code
        WHERE a.member_id = ? AND a.payment_method = ? AND a.payment_year = ? AND a.status = 'A'
        GROUP BY b.no_share, a.trans_no, a.created_at, a.payment_method, a.payment_year, a.bank_trans_no, a.bank_pay_date
        LIMIT 1
    ");
    $stmt->execute([$member_id, $payment_type, $payment_year]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($receipt) {
        $receipt['total_amount_words'] = numberToWords((int)$receipt['total_amount']) . ' Taka Only';
    }
}

include_once __DIR__ . '/../includes/open.php';
?>

<!-- ===== STYLE ===== -->
<style>
:root {
    --bs-corporate-blue: #002D59;
    --bs-corporate-orange: #F8971D;
}
.receipt-container { width: 865px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden; }
.dotted-input-solid { border: none; border-bottom: 1px solid #000; background: transparent; outline: none; padding: 0 5px; flex-grow: 1; }
.header-bg { background: var(--bs-corporate-orange); position: relative; }
.header-bg::before { content:''; position:absolute; top:0; left:0; width:35%; height:100%; background:var(--bs-corporate-blue); transform:skewX(-20deg); transform-origin: top left; z-index:1; }
.header-content { position:relative; z-index:2; }
.logo-box-custom { width:100px; height:100px; margin-right:10px; }
.footer-bg { background:var(--bs-corporate-orange); position:relative; z-index:10; }
.footer-bg::after { content:''; position:absolute; bottom:0; right:0; width:50px; height:100%; background:var(--bs-corporate-blue); clip-path:polygon(100% 0,100% 100%,0 100%); z-index:11; }
</style>

<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
    <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
      <div class="container-fluid">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Payment Receipt <span class="text-secondary">(পেমেন্ট রসিদ)</span></h3>
            <hr class="mb-4" />

            <!-- ===== Receipt Form ===== -->
            <form method="post" class="mb-4 row g-3">
              <div class="col-md-5 mb-3">
                <label for="payment_type" class="form-label">Payments (পেমেন্ট)</label>
                <select class="form-select" id="payment_type" name="payment_type" required>
                  <option value="">Select (বাছাই করুন)</option>
                  <?php
                  if ($status === 'P') {
                      $months = [
                          'admission' => 'Admission Fee (ভর্তি ফি)'
                      ];
                  } else {
                      $months = [
                          'share'     => 'Share Fee (শেয়ার ফি)',
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

              <div class="col-md-4 mb-3">
                <label for="payment_year" class="form-label">Year (বছর)</label>
                <select class="form-select" id="payment_year" name="payment_year" required>
                  <?php 
                  for ($y = $current_year; $y <= ($current_year + 0); $y++): 
                    $is_selected = (empty($payment_year) && $y == $current_year) || (!empty($payment_year) && $payment_year == $y);
                  ?>
                    <option value="<?= $y ?>" <?= $is_selected ? 'selected' : '' ?>>
                      <?= $y ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate Receipt (রসিদ তৈরি করুন)</button>
              </div>
            </form>

            <!-- ===== Show Receipt or Alert ===== -->
            <?php if ($receipt): ?>
              <div class="container receipt-container bg-white mt-5 p-0"> 
                <div class="row m-0 p-0"> 
                  <div class="col-12 p-0"> 
                    <div class="header-bg py-3 px-4 d-flex align-items-center"> 
                        <div class="col-4 d-flex align-items-center"> 
                            <div class="header-content d-flex align-items-center"> 
                                <div class="logo-box-custom d-flex align-items-center"> 
                                  <div class="p-2 bg-white rounded-circle shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden;"> <img src="/coder_group/assets/img/<?= htmlspecialchars($logo) ?>" alt="Logo" class="img-fluid"> </div> 
                                  </div> 
                                  <div class="text-white"> <p class="mb-0 small" style="color: #FFF;"><?= $slogan ?></p> </div> 
                                  </div> 
                                </div> 
                                  <div class="col-4 text-center"> 
                                      <div class="header-content"> <h1 class="text-white fw-bolder mb-1" style="font-size: 2rem;">MONEY RECEIPT</h1> <p class="mb-0 small" style="color: var(--bs-corporate-blue);"> 📞 <?= $phone ?? '0000-000000' ?> <br/>✉️ <?= $email ?? 'Your Mail Here' ?> </p> </div> 
                                  </div> 
                                    <div class="col-4 text-end"> 
                                      <div class="header-content small"> <p class="fw-bold mb-0" style="color: var(--bs-corporate-blue);"><?= $siteName ?? 'Company Name Here' ?></p> <p class="mb-0"><?= $reg_no ?? 'Your Business Address 0000' ?></p> <p class="mb-0"><?= $address ?? 'Main Street, Unit 000C FEL' ?></p> </div> 
                                    </div> 
                                  </div> 
                                </div> 
                              </div> 
                              <hr class="m-0 border border-4" style="border-color: var(--bs-corporate-orange) !important;"> <div class="row my-3 px-4 small"> <div class="col-6 d-flex align-items-center"> <label class="fw-bold me-2" style="color: var(--bs-corporate-blue);">NO :</label> <span class="text-dark"><?= htmlspecialchars($receipt['trans_no'] ?? 'trans_no') ?></span> </div> <div class="col-6 d-flex justify-content-end align-items-center"> <label class="fw-bold me-2" style="color: var(--bs-corporate-blue);">Date : </label> <span class="text-dark"><?= htmlspecialchars($receipt['created_at'] ?? 'created_at') ?></span> </div> </div> 
                                    <div class="px-4 py-3"> 
                                        <div class="mb-3 d-flex align-items-center"> <label class="me-2 text-dark">Received with thanks from</label> <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($member['name_bn'] ?? 'Member Name') ?></h5> <span class="text-muted"><small>Member Code: <?= htmlspecialchars($member['member_code'] ?? 'M-0000') ?></small></span> ( <span class="text-muted"><small>Share: <?= htmlspecialchars($receipt['no_share'] ?? '0') ?></small></span> ) </div> </div> 
                                        <div class="row g-3 mb-3"> <div class="col-6 d-flex align-items-center"> <label class="me-2 text-dark">For</label> <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['payment_method'] ?? 'Payment Method') ?></h5> </div> </div> 
                                        <div class="col-6 d-flex align-items-center"> <label class="me-2 text-dark">Year</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['payment_year'] ?? 'Payment Year') ?></h5> </div> </div> </div> <div class="row g-3 mb-3"> <div class="col-6 d-flex align-items-center"> <label class="me-2 text-dark">Bank Trans</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['bank_trans_no'] ?? 'bank_trans_no') ?></h5> </div> </div> 
                                        <div class="col-6 d-flex align-items-center"> <label class="me-2 text-dark">Bank Pay Date</label> <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['bank_pay_date'] ?? 'bank_pay_date') ?></h5> </div> </div> </div> 
                                        <div class="row g-3 mb-4"> 
                                          <div><strong>Expense of Fees:</strong></div> 
                                        <div class="col-3 d-flex align-items-center"> <label class="me-2 text-dark">ID Card</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> 
                                          <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['idcard_fee_amount'] ?? 'idcard_fee_amount') ?></h5> 
                                        </div> </div> 
                                        <div class="col-3 d-flex align-items-center"> <label class="me-2 text-dark">Passbook</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['passbook_fee_amount'] ?? 'passbook_fee_amount') ?></h5> </div> </div> 
                                        <div class="col-3 d-flex align-items-center"> <label class="me-2 text-dark">Software</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['softuses_fee_amount'] ?? 'softuses_fee_amount') ?></h5> </div> </div> 
                                        <div class="col-3 d-flex align-items-center"> <label class="me-2 text-dark">Other</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['other_fee_amount'] ?? 'other_fee_amount') ?></h5> </div> 
                                      </div> 
                                        <div class="mb-2 d-flex align-items-center"> <label class="me-2 text-dark">Due of Amount</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['net_amount'] ?? 'net_amount') ?></h5> </div> 
                                      </div> 
                                        <div class="mb-2 d-flex align-items-center"> <label class="me-2 text-dark">In word</label> 
                                        <div class="flex-grow-1 border-bottom border-dark border-1 pb-1"> <h5 class="mb-0 d-inline-block me-3"><?= htmlspecialchars($receipt['total_amount_words'] ?? 'total_amount_words') ?></h5> </div> 
                                      </div> 
                                    </div> 
                                  </div> 
                                  <div class="row m-0 p-0"> <div class="col-12 p-0"> 
                                    <div class="footer-bg py-3 px-4 d-flex justify-content-between align-items-end"> 
                                      <div class="d-flex align-items-center me-3" style="z-index: 12;"> <p class="fw-bold mb-0 small" style="color: var(--bs-corporate-blue);">Amount=</p> 
                                        <div class="bg-white border border-primary ms-2" style="width: 100px; height: 25px;"><?= htmlspecialchars($receipt['total_amount'] ?? 'total_amount') ?></div> </div> 
                                        <div class="d-flex align-items-end me-3" style="width: 35%; z-index: 12;"> <label class="fw-bold me-2" style="color: var(--bs-corporate-blue);"> Print Date : </label> <span class="text-dark"><?= date('d-m-Y H:i') ?></span> </div> 
                                        <div class="d-flex align-items-end" style="width: 35%; z-index: 12;"> <p class="text-white fw-bold mb-0 me-2 small">Signature</p> <input type="text" class="dotted-input-solid text-center" style="width: 100%; border-color: white !important;"> </div> 
                                      </div> 
                                    </div> 
                                  </div> 
                                </div>

            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
              <div class="alert alert-warning">If you are payment done, wait for approval then print the receipt. ( যদি আপনার পেমেন্ট হয়ে যায়, তাহলে অনুমোদনের জন্য অপেক্ষা করুন এবং তারপর রসিদটি প্রিন্ট করুন। )</div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
