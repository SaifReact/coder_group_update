<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$no_share = 1;
$admission_paid = false;

// Fetch admission info
$stmt = $pdo->prepare("SELECT no_share, admission_fee FROM member_share WHERE member_id = ? LIMIT 1");
$stmt->execute([$member_id]);
if ($row = $stmt->fetch()) {
    $no_share = (float)$row['no_share'];
    $admission_paid = !is_null($row['admission_fee']);
}

// Fetch already paid monthly payments
$payments = [];
$stmt2 = $pdo->prepare("SELECT payment_method, payment_year FROM member_payments WHERE member_id = ?");
$stmt2->execute([$member_id]);
while($row2 = $stmt2->fetch()) {
    // Use "type-year" key for JS lookup
    $payments[] = $row2['payment_method'] . '-' . $row2['payment_year'];
}

include_once __DIR__ . '/../includes/open.php';
?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
      <div class="container">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Make a Payment <span class="text-secondary">(পেমেন্ট করুন)</span></h3>
            <hr class="mb-4" />
            <form method="post" action="../process/payment_process.php">
              <div class="row">
                <div class="col-md-6 mb-3">
                <label for="payment_type" class="form-label">Payments (পেমেন্ট)</label>
                <select class="form-select" id="payment_type" name="payment_type" required>
                  <option value="">Select (বাছাই করুন)</option>
                  <?php
                  $months = [
                    'admission' => 'Admission-Share Fee (ভর্তি-শেয়ার ফি)',
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
                    'december'  => 'December (ডিসেম্বর)',
                  ];
                  foreach ($months as $key => $val): ?>
                    <option value="<?= $key ?>" <?= (!empty($payment_type) && $payment_type == $key) ? 'selected' : '' ?>>
                      <?= $val ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="payment_year" class="form-label">Year (বছর)</label>
                <select class="form-select" id="payment_year" name="payment_year" required>
                  <?php for ($y = 2025; $y <= 2027; $y++): ?>
                    <option value="<?= $y ?>" <?= (!empty($payment_year) && $payment_year == $y) ? 'selected' : '' ?>>
                      <?= $y ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>

                <div class="col-md-4 mb-3">
                  <label for="amount" class="form-label">Amount (টাকার পরিমাণ)</label>
                  <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                  <div id="admissionInfo" class="form-text text-info" style="display:none;">
                    প্রতি শেয়ার মূল্য ৫০০০ টাকা এবং আপনার মোট শেয়ার সংখ্যা: <span id="shareCount"></span>
                  </div>
                  <div id="admissionPaidMsg" class="form-text text-danger" style="display:none;"></div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="bank_trans" class="form-label">Bank Transaction (ব্যাংক লেনদেন নং)</label>
                  <input type="text" class="form-control" id="bank_trans" name="bank_trans" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="payment_date" class="form-label">Bank Deposit Date (ব্যাংকে জমার তারিখ)</label>
                  <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                </div>
                <div class="col-12 mt-4 text-end">
                  <button type="submit" id="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Submit Payment (পেমেন্ট সংরক্ষণ করুন)</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<!-- Hero End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  var paymentType = document.getElementById('payment_type');
  var paymentYear = document.getElementById('payment_year');
  var amountInput = document.getElementById('amount');
  var admissionInfo = document.getElementById('admissionInfo');
  var admissionPaidMsg = document.getElementById('admissionPaidMsg');
  var bankTrans = document.getElementById('bank_trans');
  var paymentDate = document.getElementById('payment_date');
  var submitButton = document.getElementById('submit');
  var shareCount = document.getElementById('shareCount');

  var noShare = <?php echo json_encode($no_share); ?>;
  var admissionPaid = <?php echo json_encode($admission_paid); ?>;
  var existingPayments = <?php echo json_encode($payments); ?>; // "month-year" format

  function updateForm() {
    var type = paymentType.value;
    var year = paymentYear.value;

    if(type === 'admission') {
      if(admissionPaid) {
        amountInput.value = '';
        amountInput.disabled = true;
        bankTrans.disabled = true;
        paymentDate.disabled = true;
        admissionInfo.style.display = 'none';
        admissionPaidMsg.textContent = "ভর্তি ফি ইতিমধ্যেই প্রদান করা হয়েছে"; 
        admissionPaidMsg.style.display = 'block';
        submitButton.style.display = 'none';
      } else {
        amountInput.value = (noShare * 5000).toFixed(2);
        amountInput.disabled = false;
        bankTrans.disabled = false;
        paymentDate.disabled = false;
        shareCount.textContent = noShare;
        admissionInfo.style.display = 'block';
        admissionPaidMsg.style.display = 'none';
        submitButton.style.display = 'inline-block';
      }
    } else {
      // Combine month + year key
      var key = type + '-' + year;
      if(existingPayments.includes(key)) {
        // Already paid for this month & year
        amountInput.value = '';
        amountInput.disabled = true;
        bankTrans.disabled = true;
        paymentDate.disabled = true;
        admissionInfo.style.display = 'none';
        admissionPaidMsg.textContent = "এই মাসের পেমেন্ট ইতিমধ্যেই প্রদান করা হয়েছে";
        admissionPaidMsg.style.display = 'block';
        submitButton.style.display = 'none';
      } else {
        amountInput.value = (noShare * 2500).toFixed(2);
        amountInput.disabled = false;
        bankTrans.disabled = false;
        paymentDate.disabled = false;
        admissionInfo.style.display = 'none';
        admissionPaidMsg.style.display = 'none';
        submitButton.style.display = 'inline-block';
      }
    }
  }

  paymentType.addEventListener('change', updateForm);
  paymentYear.addEventListener('change', updateForm);

  // Initial load
  updateForm();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../includes/toast.php'; ?>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
