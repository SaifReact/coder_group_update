<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

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
        WHERE a.member_id = ? AND a.payment_method = ? AND a.payment_year = ?
        GROUP BY b.no_share, a.trans_no, a.created_at, a.payment_method, a.payment_year, a.bank_trans_no, a.bank_pay_date
        LIMIT 1
    ");
    $stmt->execute([$member_id, $payment_type, $payment_year]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($receipt) {
        $receipt['total_amount_words'] = numberToWords((int)$receipt['total_amount']) . ' Taka Only';
    }
}

// Fetch payment summary for the year
$stmt = $pdo->prepare("SELECT a.payment_year, SUM(CASE WHEN a.for_fees = 'admission' THEN a.amount ELSE 0 END) AS admission, b.no_share, SUM(CASE WHEN a.for_fees = 'share' THEN a.amount ELSE 0 END) AS share FROM member_payments a JOIN member_share b ON a.member_id = b.member_id WHERE a.member_id = ? AND a.payment_year = ? GROUP BY a.payment_year, b.no_share LIMIT 1");
$stmt->execute([$member_id, $payment_year]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

include_once __DIR__ . '/../includes/open.php';
?>


<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
    <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
      <div class="container">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Passbook <span class="text-secondary">(পাসবুক)</span></h3>
            <hr class="mb-4" />

            <!-- ===== Receipt Form ===== -->
            <form method="post" class="mb-4 row g-3">
              <div class="col-md-7">
                <label for="payment_year" class="form-label">Year (বছর)</label>
                <select class="form-select" id="payment_year" name="payment_year" required>
                  <?php for($y=2025;$y<=2027;$y++): ?>
                    <option value="<?= $y ?>" <?= ($payment_year==$y)?'selected':'' ?>><?= $y ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="col-md-5 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate Receipt (রসিদ তৈরি করুন)</button>
              </div>
            </form>

            <!-- ===== Payment Summary Table ===== -->
            <div class="mt-5">
              <h4 class="text-center text-primary fw-bold"><?= htmlspecialchars($siteName) ?></h4>
              <div class="table-responsive">
                <table border="1" cellpadding="5" style="text-align:center;" class="table table-bordered table-striped">
                    <tr>
                        <th rowspan="2">বছর</th>
                        <th rowspan="2">ভর্তি ফি</th>
                        <th rowspan="2">শেয়ার সংখ্যা</th>
                        <th rowspan="2">শেয়ার টাকা পরিমান</th>
                        <th colspan="12">মাসসমূহ</th>
                        <th rowspan="2">মোট</th>
                    </tr>
                    <tr>
                        <th>জানুয়ারী</th><th>ফেব্রুয়ারী</th><th>মার্চ</th><th>এপ্রিল</th><th>মে</th><th>জুন</th>
                        <th>জুলাই</th><th>আগস্ট</th><th>সেপ্টেম্বর</th><th>অক্টোবর</th><th>নভেম্বর</th><th>ডিসেম্বর</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars($summary['payment_year'] ?? '') ?></td>
                        <td><?= htmlspecialchars($summary['admission'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['no_share'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['share'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['jan'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['feb'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['mar'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['apr'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['may'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['jun'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['jul'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['aug'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['sep'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['oct'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['nov'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['dec'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($summary['total'] ?? 0) ?></td>
                    </tr>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
