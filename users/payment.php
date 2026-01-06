<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
$no_share = 1;
$admission_paid = false;
$current_year = (int)date('Y');

// Fetch project data (per_share_value)
$per_share_value = 0; // Default value
$stmt = $pdo->prepare("SELECT * FROM project LIMIT 1");
$stmt->execute();
if ($row = $stmt->fetch()) {
    $per_share_value = isset($row['per_share_value']) ? (float)$row['per_share_value'] : 5000;
}

// Fetch all data from utils table and extract fee types
$admissionfee = 0; // Default value
$monthly = 0; // Default value
$late = 0; // Default value
$samityShare = 0;

$stmt_utils = $pdo->prepare("SELECT * FROM utils where status = 'A'");
$stmt_utils->execute();
while ($row_utils = $stmt_utils->fetch()) {
    if (isset($row_utils['fee_type'])) {
        if ($row_utils['fee_type'] === 'admission') {
            $admissionfee = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 1500;
        } elseif ($row_utils['fee_type'] === 'monthly') {
            $monthly = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 2000;
        } elseif ($row_utils['fee_type'] === 'late') {
            $late = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 200;
        } elseif ($row_utils['fee_type'] === 'samity_share') {
            $samityShare = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 5000;
    }
  }
}

$stmt1 = $pdo->prepare("SELECT * FROM member_share a JOIN member_project b ON a.member_id = b.member_id WHERE a.member_id = ? AND a.member_code = ? LIMIT 1");
$stmt1->execute([$member_id, $_SESSION['member_code']]);
if ($row1 = $stmt1->fetch()) {
    $no_share = (float)$row1['no_share'];
    $samity_share = (float)$row1['samity_share'];
    $samity_share_amt = (float)$row1['samity_share_amt'];
    $extra_share = isset($row1['extra_share']) ? (float)$row1['extra_share'] : 0;
    $admission_paid = isset($row1['admission_fee']) && (float)$row1['admission_fee'] > 0;
    $late_assign = isset($row1['late_assign']) ? $row1['late_assign'] : '';
    $late_fee = isset($row1['late_fee']) ? (float)$row1['late_fee'] : 0;
    $sundry_samity_share = isset($row1['sundry_samity_share']) ? (float)$row1['sundry_samity_share'] : 0;
    $install_advance = isset($row1['install_advance']) ? (float)$row1['install_advance'] : 0;
    $project_share = isset($row1['project_share']) ? (float)$row1['project_share'] : 0;
    $project_share_amount = isset($row1['share_amount']) ? (float)$row1['share_amount'] : 0;
    $paid_amount = isset($row1['paid_amount']) ? (float)$row1['paid_amount'] : 0;
    $sundry_amount = isset($row1['sundry_amount']) ? (float)$row1['sundry_amount'] : 0;
}

// Fetch already paid monthly payments
$payments = [];
$stmt2 = $pdo->prepare("SELECT payment_method, payment_year FROM member_payments WHERE member_id = ?");
$stmt2->execute([$member_id]);
while($row2 = $stmt2->fetch()) {
    // Use "type-year" key for JS lookup
    $payments[] = $row2['payment_method'] . '-' . $row2['payment_year'];
}
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Make a Payment <span class="text-secondary">(পেমেন্ট করুন)</span></h3>
            <hr class="mb-4" />
            <form method="post" action="../process/payment_process.php" enctype="multipart/form-data">
  <div class="row">

    <!-- Payment Type Selection -->
    <div class="col-md-5 mb-3">
      <label for="payment_type" class="form-label">পেমেন্ট ফি বাছাই করুন (Select Payment Fee)</label>
      <select class="form-select" id="payment_type" name="payment_type" required onchange="handlePaymentTypeChange()">
        <option value="">ফি বাছাই করুন (Select Fee)</option>
        <?php
          $months = [
            'advance' => 'Advance Deposit (অগ্রিম ডিপোজিট)',
            'january' => 'January (জানুয়ারি)',
            'february' => 'February (ফেব্রুয়ারি)',
            'march' => 'March (মার্চ)',
            'april' => 'April (এপ্রিল)',
            'may' => 'May (মে)',
            'june' => 'June (জুন)',
            'july' => 'July (জুলাই)',
            'august' => 'August (আগস্ট)',
            'september' => 'September (সেপ্টেম্বর)',
            'october' => 'October (অক্টোবর)',
            'november' => 'November (নভেম্বর)',
            'december' => 'December (ডিসেম্বর)'
          ];
          foreach ($months as $key => $val): ?>
            <option value="<?= $key ?>" <?= (!empty($payment_type) && $payment_type == $key) ? 'selected' : '' ?>>
              <?= $val ?>
            </option>
          <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2 mb-3">
      <label for="payment_year" class="form-label">বছর (Year)</label>
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

    <!-- Project Select removed -->

    <!-- Year Selection -->
    

    <!-- Amount Input -->
    <div class="col-md-5 mb-3">
      <label for="deposit_amount" class="form-label">মাসিক ডিপোজিট (Monthly Deposit)</label>
      <input type="text" class="form-control" id="deposit_amount" name="deposit_amount" readonly>
      <div id="admissionPaidMsg" class="form-text text-danger" style="display:none;"></div>
    </div>
    <div class="col-md-6 mb-3">
      <label for="amount" class="form-label">টাকার পরিমাণ (Amount)</label>
      <input type="number" step="0.01" class="form-control" id="amount" name="amount" required oninput="handleAmountInput()">
    </div>

    <!-- Total Share Value removed -->

    <!-- Payment Mode (Adjustment or Bank Pay) -->
    <div class="col-md-6 mb-3">
      <div id="paymentModeDiv">
        <label for="payment_mode" class="form-label">পেমেন্ট মোড (Payment Mode)</label><br/>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="pay_mode" id="AD" value="AD" required onclick="handlePayModeChange()">
          <label class="form-check-label" for="AD">Adjustment</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="pay_mode" id="BP" value="BP" required onclick="handlePayModeChange()">
          <label class="form-check-label" for="BP">Bank Pay</label>
        </div>
      </div>
      <input type="date" class="form-control" id="payment_date" name="payment_date">
    </div>

    <!-- Payment Slip -->
    <div class="col-md-6 mb-3" id="paymentSlipDiv" style="display:none;">
      <label for="payment_slip" class="form-label">পেমেন্ট স্লিপ (Payment Slip)</label>
      <input type="file" class="form-control" id="payment_slip" name="payment_slip" accept="image/*" onchange="previewPaymentSlip(event)">
    </div>

    <div class="col-md-6 mb-3">
      <img id="paymentSlipPreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
    </div>

    <!-- Remarks -->
    <div class="col-md-12 mb-3">
      <label for="remarks" class="form-label">মন্তব্য (Remarks)</label>
      <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
    </div>

    <!-- Submit Button -->
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
  // Only monthly payment logic
  function handlePaymentTypeChange() {
    var type = document.getElementById('payment_type').value;
    var amountInput = document.getElementById('amount');
    var depositAmountInput = document.getElementById('deposit_amount');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var submitButton = document.getElementById('submit');
    var monthlyFee = <?php echo json_encode($monthly); ?>;
    var lateFee = <?php echo json_encode($late); ?>;
    var payments = <?php echo json_encode($payments); ?>;
    var paymentYear = document.getElementById('payment_year').value;
    var months = ['january','february','march','april','may','june','july','august','september','october','november','december'];

    // Check previous month paid
    var selectedMonth = type;
    var selectedIndex = months.indexOf(selectedMonth);
    var prevMonth = months[selectedIndex-1];
    var prevPaid = payments.includes(prevMonth+'-'+paymentYear);
    var alreadyPaid = payments.includes(selectedMonth+'-'+paymentYear);

    if (alreadyPaid) {
      admissionPaidMsg.style.display = '';
      admissionPaidMsg.innerText = 'এই মাসের ফি ইতিমধ্যে প্রদান করা হয়েছে। (Already paid for this month)';
      depositAmountInput.value = '';
      amountInput.value = '';
      amountInput.disabled = true;
      submitButton.style.display = 'none';
      return;
    }

    if (selectedIndex > 0 && !prevPaid) {
      admissionPaidMsg.style.display = '';
      admissionPaidMsg.innerText = 'আগে ' + months[selectedIndex-1] + ' মাসের ফি প্রদান করুন। (Please pay previous month first)';
      depositAmountInput.value = '';
      amountInput.value = '';
      amountInput.disabled = true;
      submitButton.style.display = 'none';
      return;
    }

    // Calculate late fine based on payment date
    var depositAmt = monthlyFee;
    var paymentDateInput = document.getElementById('payment_date');
    var paymentDate = paymentDateInput ? paymentDateInput.value : '';
    var lateApplied = false;
    if (paymentDate) {
      var payDate = new Date(paymentDate);
      var payMonth = payDate.getMonth();
      var payYear = payDate.getFullYear();
      if (payYear == paymentYear && payMonth == selectedIndex) {
        var day = payDate.getDate();
        if (day < 1 || day > 30) {
          depositAmt += lateFee;
          lateApplied = true;
        }
      } else {
        depositAmt += lateFee;
        lateApplied = true;
      }
    }
    depositAmountInput.value = depositAmt;
    amountInput.value = depositAmt;
    amountInput.disabled = false;
    submitButton.style.display = '';
    if (lateApplied) {
      admissionPaidMsg.style.display = '';
      admissionPaidMsg.innerText = 'বিলম্ব ফি যোগ হয়েছে। (Late fine applied)';
    } else {
      admissionPaidMsg.style.display = 'none';
      admissionPaidMsg.innerText = '';
    }
  }

  // Amount input for advance deposit
  function handleAmountInput() {
    var amountInput = document.getElementById('amount');
    var depositAmountInput = document.getElementById('deposit_amount');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var monthlyFee = <?php echo json_encode($monthly); ?>;
    var val = parseFloat(amountInput.value) || 0;
    if (val >= monthlyFee) {
      var monthsAdvance = Math.floor(val / monthlyFee);
      if (monthsAdvance > 1) {
        admissionPaidMsg.style.display = '';
        admissionPaidMsg.innerText = monthsAdvance + ' মাসের অগ্রিম প্রদান হবে। (Advance payment for ' + monthsAdvance + ' months)';
      } else {
        admissionPaidMsg.style.display = 'none';
        admissionPaidMsg.innerText = '';
      }
      depositAmountInput.value = monthlyFee;
    } else {
      admissionPaidMsg.style.display = 'none';
      admissionPaidMsg.innerText = '';
      depositAmountInput.value = val;
    }
  }

  // Project change removed

              function handlePayModeChange() {
                var bp = document.getElementById('BP').checked;
                document.getElementById('bankTransDiv').style.display = bp ? '' : 'none';
                document.getElementById('paymentDateDiv').style.display = bp ? '' : 'none';
                document.getElementById('paymentSlipDiv').style.display = bp ? '' : 'none';
              }

              function previewPaymentSlip(event) {
                var img = document.getElementById('paymentSlipPreview');
                if(event.target.files && event.target.files[0]) {
                  img.src = URL.createObjectURL(event.target.files[0]);
                  img.style.display = 'block';
                } else {
                  img.style.display = 'none';
                }
              }
              // Initial call for page load
              document.addEventListener('DOMContentLoaded', function() {
                handlePaymentTypeChange();
                handlePayModeChange();
                var paymentDateInput = document.getElementById('payment_date');
                if (paymentDateInput) {
                  paymentDateInput.addEventListener('change', function() {
                    handlePaymentTypeChange();
                  });
                }
              });
              </script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
