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
    <script>
      // PHP variables to JS
      var admissionFee = <?php echo json_encode($admissionfee); ?>;
      var sundrySamityShare = <?php echo json_encode($sundry_samity_share); ?>;
      var memberProjects = <?php
        $stmtProj = $pdo->prepare("SELECT a.id, a.project_id, b.project_name_bn, b.project_name_en, a.paid_amount, a.share_amount, a.sundry_amount FROM member_project a, project b WHERE a.project_id = b.id AND a.member_id = ?");
        $stmtProj->execute([$member_id]);
        $projectsArr = [];
        while($proj = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
          $projectsArr[] = $proj;
        }
        echo json_encode($projectsArr);
      ?>;
    </script>

    <!-- Payment Type Selection -->
    <div class="col-md-5 mb-3">
      <label for="payment_type" class="form-label">পেমেন্ট ফি বাছাই করুন (Select Payment Fee)</label>
      <select class="form-select" id="payment_type" name="payment_type" required onchange="handlePaymentTypeChange()">
        <option value="">ফি বাছাই করুন (Select Fee)</option>
        <?php
          if ($status === 'P') {
            $months = [
                'admission' => 'সদস্য এন্ট্রি ফি (Member Entry Fee)',
                'Samity Share' => 'সমিতি শেয়ার ফি (Samity Share Fee)',
                'Project Share' => 'প্রকল্প শেয়ার ফি (Project Share Fee)'
            ];
          } else {
            $months = [
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
          }
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

    <!-- Project Select (For Project Share) -->
    <div class="col-md-5 mb-3">
      <label for="projectSelect" class="form-label">&nbsp;</label>
      <select class="form-select mt-2" id="projectSelect" name="project_id" style="display:none;" onchange="handleProjectChange()">
      </select>
    </div>

    <!-- Year Selection -->
    

    <!-- Amount Input -->
    <div class="col-md-6 mb-3">
      <label for="amount" class="form-label">টাকার পরিমাণ (Amount)</label>
      <input type="number" step="0.01" class="form-control" id="amount" name="amount" required oninput="handleAmountInput()">
      <div id="admissionPaidMsg" class="form-text text-danger" style="display:none;"></div>
    </div>

    <!-- Total Share Value -->
    <div class="col-md-6 mb-3" id="totalShareDiv" style="display:none;">
      <label for="total_share_value" class="form-label">মোট শেয়ার মূল্য (Total Share Value)</label>
      <input type="text" class="form-control" id="total_share_value" name="total_share_value" readonly>
      <div id="admissionInfo" class="form-text text-info" style="display:none;">
        প্রতি শেয়ার মূল্য <?php echo json_encode($per_share_value); ?> টাকা এবং আপনার মোট শেয়ার সংখ্যা: <span id="shareCount"></span>
      </div>
    </div>

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
    </div>

    <!-- Bank Transaction Details -->
    <div class="col-md-6 mb-3" id="bankTransDiv" style="display:none;">
      <label for="bank_trans" class="form-label">ব্যাংক লেনদেন নং (Bank Transaction)</label>
      <input type="text" class="form-control" id="bank_trans" name="bank_trans">
    </div>

    <!-- Payment Date -->
    <div class="col-md-6 mb-3" id="paymentDateDiv" style="display:none;">
      <label for="payment_date" class="form-label">ব্যাংকে জমার তারিখ (Bank Deposit Date)</label>
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
  function handlePaymentTypeChange() {
    var type = document.getElementById('payment_type').value;
    var amountInput = document.getElementById('amount');
    var totalShareDiv = document.getElementById('totalShareDiv');
    var totalShareValue = document.getElementById('total_share_value');
    var projectSelect = document.getElementById('projectSelect');

    var paymentModeDiv = document.getElementById('paymentModeDiv');

    var admissionInfo = document.getElementById('admissionInfo');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var submitButton = document.getElementById('submit');

    var admissionfee = <?php echo json_encode($admissionfee); ?>;
    var admission_paid = <?php echo json_encode($admission_paid ? 'yes' : 'no'); ?>;
    
    var samityShareAmount = <?php echo json_encode($samity_share_amt); ?>;
    var sundrySamityShare = <?php echo json_encode($sundry_samity_share); ?>;
    

                if (type === 'admission') {
                  amountInput.value = admissionFee;
                  totalShareDiv.style.display = 'none';
                  projectSelect.style.display = 'none';
                  if (admission_paid === 'yes') {
                    admissionPaidMsg.style.display = '';
                    admissionPaidMsg.innerText = 'আপনার সদস্য ফি প্রদান করা হয়েছে। (Your Membership Fee has already been paid.)';
                    amountInput.value = '';
                    amountInput.disabled = true;
                    submitButton.style.display = 'none';
                    paymentModeDiv.style.display = 'none';
                  } else {
                    admissionPaidMsg.style.display = 'none';
                    admissionPaidMsg.innerText = '';
                    amountInput.disabled = false;
                    submitButton.style.display = '';
                    paymentModeDiv.style.display = '';
                  }
                } else if (type === 'Samity Share') {
                  totalShareDiv.style.display = '';
                  totalShareValue.value = sundrySamityShare;
                  projectSelect.style.display = 'none';
                  admissionPaidMsg.style.display = 'none';
                  amountInput.disabled = false;
                  amountInput.value = '';
                  if (samityShareAmount > 0 && sundrySamityShare == 0) {
                    admissionPaidMsg.style.display = '';
                    admissionPaidMsg.innerText = 'আপনার সমিতি শেয়ার ফি প্রদান করা হয়েছে। (Your Samity Share Fee has already been paid.)';
                    amountInput.value = '';
                    amountInput.disabled = true;
                    submitButton.style.display = 'none';
                    paymentModeDiv.style.display = 'none';
                  } else {
                    admissionPaidMsg.style.display = 'none';
                    admissionPaidMsg.innerText = '';
                    submitButton.style.display = '';
                    paymentModeDiv.style.display = '';
                  }
                } else if (type === 'Project Share') {
                  // Populate project select
                  projectSelect.innerHTML = '<option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>';
                  memberProjects.forEach(function(p) {
                    projectSelect.innerHTML += `<option value="${p.project_id}" data-share-amount="${p.share_amount}" data-paid-amount="${p.paid_amount}" data-sundry-amount="${p.sundry_amount}">${p.project_name_bn}</option>`;
                  });
                  projectSelect.style.display = '';
                  totalShareDiv.style.display = '';
                  // No message or disabling until a project is selected
                  totalShareValue.value = '';
                  admissionPaidMsg.style.display = 'none';
                  admissionPaidMsg.innerText = '';
                  amountInput.disabled = false;
                  submitButton.style.display = '';
                } else {
                  totalShareDiv.style.display = 'none';
                  projectSelect.style.display = 'none';
                  totalShareValue.value = '';
                }
              }

              function handleAmountInput() {
                var type = document.getElementById('payment_type').value;
                var amountInput = document.getElementById('amount');
                var totalShareValue = document.getElementById('total_share_value');
                var projectSelect = document.getElementById('projectSelect');
                var admissionPaidMsg = document.getElementById('admissionPaidMsg');
                var val = parseFloat(amountInput.value) || 0;
                var base = 0;
                if (type === 'Samity Share') {
                  base = parseFloat(sundrySamityShare) || 0;
                  totalShareValue.value = Math.max(base - val, 0);
                } else if (type === 'Project Share') {
                  var selected = projectSelect.options[projectSelect.selectedIndex];
                  base = selected ? parseFloat(selected.getAttribute('data-sundry-amount')) || 0 : 0;
                  totalShareValue.value = Math.max(base - val, 0);
                }
                // Prevent amount > total share value
                if ((type === 'Samity Share' || type === 'Project Share') && val > base) {
                  admissionPaidMsg.style.display = '';
                  admissionPaidMsg.innerText = 'টাকার পরিমান কখন ও মোট শেয়ার মূল্যের বেশি দেয়া যাবে না। (Amount cannot be more than Total Share Value.)';
                  amountInput.value = '';
                  amountInput.disabled = true;
                } else if ((type === 'Samity Share' || type === 'Project Share')) {
                  admissionPaidMsg.style.display = 'none';
                  admissionPaidMsg.innerText = '';
                  amountInput.disabled = false;
                }
              }

              function handleProjectChange() {
                var projectSelect = document.getElementById('projectSelect');
                var totalShareValue = document.getElementById('total_share_value');
                var amountInput = document.getElementById('amount');
                var admissionPaidMsg = document.getElementById('admissionPaidMsg');
                var submitButton = document.getElementById('submit');
                var selected = projectSelect.options[projectSelect.selectedIndex];
                var base = selected ? parseFloat(selected.getAttribute('data-share-amount')) || 0 : 0;
                var paid = selected ? parseFloat(selected.getAttribute('data-paid-amount')) || 0 : 0;
                var sundry = selected ? parseFloat(selected.getAttribute('data-sundry-amount')) || 0 : 0;
                totalShareValue.value = sundry;
                if (paid > 0 && sundry == 0) {
                  admissionPaidMsg.style.display = '';
                  admissionPaidMsg.innerText = 'আপনার প্রকল্প শেয়ার ফি প্রদান করা হয়েছে। (Your Project Share Fee has already been paid.)';
                  amountInput.value = '';
                  amountInput.disabled = true;
                  submitButton.style.display = 'none';
                  paymentModeDiv.style.display = 'none';
                } else {
                  admissionPaidMsg.style.display = 'none';
                  admissionPaidMsg.innerText = '';
                  amountInput.disabled = false;
                  submitButton.style.display = '';
                  paymentModeDiv.style.display = '';
                }
                // Recalculate if amount already entered
                if (amountInput.value) {
                  handleAmountInput();
                }
              }

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
              });
              </script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
