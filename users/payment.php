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

$stmt1 = $pdo->prepare("SELECT * FROM member_share a JOIN member_project b ON a.member_id = b.member_id WHERE a.member_id = ? LIMIT 1");
$stmt1->execute([$member_id]);
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
    $share_amount = isset($row1['share_amount']) ? (float)$row1['share_amount'] : 0;
    $paid_amount = isset($row1['paid_amount']) ? (float)$row1['paid_amount'] : 0;
    $sundry_share = isset($row1['sundry_share']) ? (float)$row1['sundry_share'] : 0;
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
            <form method="post" action="../process/payment_process.php" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 mb-3">
                <label for="payment_type" class="form-label">Payments (পেমেন্ট)</label>
                <select class="form-select" id="payment_type" name="payment_type" required>
                  <option value="">Select (বাছাই করুন)</option>
                  <?php
                  if ($status === 'P') {
                      $months = [
                          'admission' => 'Admission Fee (ভর্তি ফি)',
                          'Samity Share'     => 'Samity Share Fee (শেয়ার ফি)',
                          'Project Share'     => 'Project Share Fee (প্রকল্প শেয়ার ফি)'
                      ];
                  } else {
                      $months = [
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

              <div class="col-md-6 mb-3">
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

                <div class="col-md-6 mb-3">
                  <label for="amount" class="form-label">Amount (টাকার পরিমাণ)</label>
                  <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                  
                  <div id="admissionPaidMsg" class="form-text text-danger" style="display:none;"></div>
                </div>
                
                <!-- Total Share Value (readonly) -->
                <div class="col-md-6 mb-3" id="totalShareDiv" style="display:none;">
                  <label for="total_share_value" class="form-label">Total Share Value (মোট শেয়ার মূল্য)</label>
                  <input type="text" class="form-control" id="total_share_value" name="total_share_value" readonly>
                  <div id="admissionInfo" class="form-text text-info" style="display:none;">
                    প্রতি শেয়ার মূল্য <?php echo json_encode($per_share_value); ?> টাকা এবং আপনার মোট শেয়ার সংখ্যা: <span id="shareCount"></span>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="payment_mode" class="form-label">Payment Mode (পেমেন্ট মোড)</label><br/>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="pay_mode" id="AD" value="AD" required>
                      <label class="form-check-label" for="AD">Adjustment</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="pay_mode" id="BP" value="BP" required>
                      <label class="form-check-label" for="BP">Bank Pay</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3" id="bankTransDiv" style="display:none;">
                  <label for="bank_trans" class="form-label">Bank Transaction (ব্যাংক লেনদেন নং)</label>
                  <input type="text" class="form-control" id="bank_trans" name="bank_trans">
                </div>
                <div class="col-md-6 mb-3" id="paymentDateDiv" style="display:none;">
                  <label for="payment_date" class="form-label">Bank Deposit Date (ব্যাংকে জমার তারিখ)</label>
                  <input type="date" class="form-control" id="payment_date" name="payment_date">
                </div>
                <div class="col-md-4 mb-3" id="paymentSlipDiv" style="display:none;">
                  <label for="payment_slip" class="form-label">Payment Slip</label>
                  <input type="file" class="form-control" id="payment_slip" name="payment_slip" accept="image/*" onchange="previewPaymentSlip(event)">
                </div>
                <div class="col-md-3 mb-3">
                  <img id="paymentSlipPreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <label for="remarks" class="form-label">Remarks (মন্তব্য)</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
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
    var paymentMode = document.getElementById('payment_date');
    var admissionInfo = document.getElementById('admissionInfo');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var bankTrans = document.getElementById('bank_trans');
    var paymentDate = document.getElementById('payment_date');
    var paymentSlip = document.getElementById('payment_slip');
    var submitButton = document.getElementById('submit');
    var shareCount = document.getElementById('shareCount');
    var payModeAD = document.getElementById('AD');
    var payModeBP = document.getElementById('BP');
    var bankTransDiv = document.getElementById('bankTransDiv');
    var paymentDateDiv = document.getElementById('paymentDateDiv');
    var paymentSlipDiv = document.getElementById('paymentSlipDiv');

    var noShare = <?php echo json_encode($no_share); ?>;
    var samityShare = <?php echo json_encode($samity_share); ?>;
    var samityShareAmount = <?php echo json_encode($samity_share_amt); ?>;
    var extraShare = <?php echo json_encode($extra_share); ?>;
    var admissionPaid = <?php echo json_encode($admission_paid); ?>;
    var lateAssign = <?php echo json_encode($late_assign); ?>;
    var lateFee = <?php echo json_encode($late_fee); ?>;
    var sundrySamityShare = <?php echo json_encode($sundry_samity_share); ?>;
    var installAdvance = <?php echo json_encode($install_advance); ?>;
    var projectShare = <?php echo json_encode($project_share); ?>;
    var shareAmount = <?php echo json_encode($share_amount); ?>;
    var paidAmount = <?php echo json_encode($paid_amount); ?>;
    var sundryShare = <?php echo json_encode($sundry_share); ?>;
    var admissionPaid = <?php echo json_encode($admission_paid); ?>;
    var existingPayments = <?php echo json_encode($payments); ?>; // "month-year" format
    var perShareValue = <?php echo json_encode($per_share_value); ?>;
    var admissionfee = <?php echo json_encode($admissionfee); ?>;
    var monthly = <?php echo json_encode($monthly); ?>;
 
  
  // Get current month (0-11) and convert to month name
  var currentDate = new Date();
  var currentMonth = currentDate.getMonth(); // 0 = January, 11 = December
  var monthNames = ['january', 'february', 'march', 'april', 'may', 'june', 
                    'july', 'august', 'september', 'october', 'november', 'december'];
  var currentMonthName = monthNames[currentMonth];
  
  // Function to toggle bank fields based on payment mode
  function toggleBankFields() {
    if(payModeBP.checked) {
      bankTransDiv.style.display = 'block';
      paymentDateDiv.style.display = 'block';
      paymentSlipDiv.style.display = 'block';
      bankTrans.required = true;
      paymentDate.required = true;
      paymentSlip.required = true;
    } else if(payModeAD.checked) {
      bankTransDiv.style.display = 'none';
      paymentDateDiv.style.display = 'none';
      paymentSlipDiv.style.display = 'none';
      bankTrans.required = false;
      paymentDate.required = false;
      paymentSlip.required = false;
    }
  }
  
  // Add event listeners to payment mode radio buttons
  payModeAD.addEventListener('change', toggleBankFields);
  payModeBP.addEventListener('change', toggleBankFields);

  function updateForm() {
    var type = paymentType.value;
    var year = paymentYear.value;
    
    // Get share div
    var totalShareDiv = document.getElementById('totalShareDiv');

    // First check if payment exists
    var key = type + '-' + year;
    
    // For Samity Share payment, block if samity_share_amt > 0 and sundry_samity_share == 0
    if(type === 'Samity Share' && samityShareAmount > 0 && sundrySamityShare == 0) {
        amountInput.value = '';
        amountInput.disabled = true;
        bankTrans.disabled = true;
        paymentDate.disabled = true;
        paymentSlip.disabled = true;
        admissionInfo.style.display = 'none';
        totalShareDiv.style.display = 'none';
        admissionPaidMsg.textContent = "সমিতি শেয়ার ফি ইতিমধ্যেই প্রদান করা হয়েছে";
        admissionPaidMsg.style.display = 'block';
        submitButton.style.display = 'none';
        return;
    } else if(type === 'Project Share' && existingPayments.includes(key) && sundryProjectShare <= 0) {
      // Project Share payment already completed (no balance remaining)
      amountInput.value = '';
      amountInput.disabled = true;
      bankTrans.disabled = true;
      paymentDate.disabled = true;
      paymentSlip.disabled = true;
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
      admissionPaidMsg.textContent = "প্রকল্প শেয়ার ফি ইতিমধ্যেই প্রদান করা হয়েছে";
      admissionPaidMsg.style.display = 'block';
      submitButton.style.display = 'none';
      return;
    } else if(type !== 'Samity Share' && type !== 'Project Share' && (existingPayments.includes(key) || (type === 'admission' && admissionPaid))) {
      // For non-share payments, block if already paid
      amountInput.value = '';
      amountInput.disabled = true;
      bankTrans.disabled = true;
      paymentDate.disabled = true;
      paymentSlip.disabled = true;
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
      admissionPaidMsg.textContent = type === 'admission' ? 
        "ভর্তি ফি ইতিমধ্যেই প্রদান করা হয়েছে" : 
        "এই মাসের পেমেন্ট ইতিমধ্যেই প্রদান করা হয়েছে";
      admissionPaidMsg.style.display = 'block';
      submitButton.style.display = 'none';
      return;
    }

    // Enable form fields for new payment
    amountInput.disabled = false;
    bankTrans.disabled = false;
    paymentDate.disabled = false;
    paymentSlip.disabled = false;
    admissionPaidMsg.style.display = 'none';
    submitButton.style.display = 'inline-block';

    // Set amount based on payment type
    if(type === 'admission') {
      amountInput.value = (admissionfee).toFixed(2);
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    } else if(type === 'Samity Share') {
      // Samity Share = (no_share - extra_share) * per_share_value
      var samityShareCount = no_share - extra_share;
      var totalShareValue = samityShareCount * perShareValue;
      console.log('Samity Share Count:', samityShareCount, totalShareValue);
      amountInput.value = ''; // User will enter amount manually
      shareCount.textContent = samityShareCount;
      admissionInfo.style.display = 'block';
      
      // Show total share value field
      totalShareDiv.style.display = 'block';
      
      // Set initial value - show sundry_share if exists, otherwise show calculated total
      if(sundry_samity_share > 0) {
        // Second or subsequent transaction - show sundry_share from database
        document.getElementById('total_share_value').value = sundry_samity_share.toFixed(2);
      } else {
        // First transaction - show (no_share - extra_share) * per_share_value
        document.getElementById('total_share_value').value = totalShareValue.toFixed(2);
      }
      
      // Calculate remaining on amount change
      updateTotalShareValue();
    } else if(type === 'Project Share') {
      // Project Share = extra_share * per_share_value
      var projectShareCount = projectShare;
      var totalProjectShareValue = projectShareCount * perShareValue;
      amountInput.value = ''; // User will enter amount manually
      shareCount.textContent = projectShareCount;
      admissionInfo.style.display = 'block';
      
      // Show total share value field
      totalShareDiv.style.display = 'block';
      
      // Set initial value - show sundry_project_share if exists, otherwise show calculated total
      if(sundryProjectShare > 0) {
        // Second or subsequent transaction - show sundry_project_share from database
        document.getElementById('total_share_value').value = sundryProjectShare.toFixed(2);
      } else {
        // First transaction - show projectShareCount * per_share_value
        document.getElementById('total_share_value').value = totalProjectShareValue.toFixed(2);
      }
      
      // Calculate remaining on amount change
      updateTotalShareValue();
    } else if(type !== '') { // Monthly payments (january-december)
      var paymentAmount = monthly;
      
      // Check if late fee should be applied based on late_assign value and current month
      var selectedMonthIndex = monthNames.indexOf(type);
      if(lateAssign === 'A' && selectedMonthIndex !== -1 && selectedMonthIndex < currentMonth) {
        // Add late fee if late_assign is 'A' AND selected month is before current month
        paymentAmount = monthly + late;
      } else {
        // No late fee
        paymentAmount = monthly;
      }
      
      amountInput.value = paymentAmount.toFixed(2);
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    } else {
      amountInput.value = '';
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    }
  }
  
  function updateTotalShareValue() {
    var type = paymentType.value;
    if(type === 'Samity Share') {
      var amountPaid = parseFloat(amountInput.value) || 0;
      var initialTotal = 0;
      
      // Get initial total based on whether sundry_share exists
      if(sundryShare > 0) {
        // Second or subsequent transaction - use sundry_share
        initialTotal = sundryShare;
      } else {
        // First transaction - use (no_share - extra_share) * per_share_value
        var samityShareCount = noShare - extraShare;
        initialTotal = samityShareCount * perShareValue;
      }
      
      var remainingTotal = initialTotal - amountPaid;
      document.getElementById('total_share_value').value = remainingTotal.toFixed(2);
    } else if(type === 'Project Share') {
      var amountPaid = parseFloat(amountInput.value) || 0;
      var initialTotal = 0;
      
      // Get initial total based on whether sundry_project_share exists
      if(sundryProjectShare > 0) {
        // Second or subsequent transaction - use sundry_project_share
        initialTotal = sundryProjectShare;
      } else {
        // First transaction - use extra_share * per_share_value
        initialTotal = extraShare * perShareValue;
      }
      
      var remainingTotal = initialTotal - amountPaid;
      document.getElementById('total_share_value').value = remainingTotal.toFixed(2);
    }
  }

  paymentType.addEventListener('change', updateForm);
  paymentYear.addEventListener('change', updateForm);
  amountInput.addEventListener('input', updateTotalShareValue);

  // Initial load
  updateForm();
});

function previewPaymentSlip(event) {
  var img = document.getElementById('paymentSlipPreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
