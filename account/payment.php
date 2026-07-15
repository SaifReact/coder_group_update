<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
$no_share = 1;
$admission_paid = false;
$current_year = (int)date('Y');

// Fetch members
$memberStmt = $pdo->query("SELECT id, name_bn, name_en, member_code FROM members_info ORDER BY id ASC");
$members = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project data (per_share_value for samity project id=1)
$per_share_value = 5000;
$stmt = $pdo->prepare("SELECT per_share_value FROM project WHERE id = 1 LIMIT 1");
$stmt->execute();
if ($row = $stmt->fetch()) {
    $per_share_value = (float)($row['per_share_value'] ?? 5000);
}

// Fetch Uddokta Share project (id=2) per_share_value
$uddokta_per_share_value = 5000;
$stmt = $pdo->prepare("SELECT per_share_value FROM project WHERE id = 2 LIMIT 1");
$stmt->execute();
if ($row = $stmt->fetch()) {
    $uddokta_per_share_value = (float)($row['per_share_value'] ?? 5000);
}

// Fetch all data from utils table and extract fee types
$admissionfee = 0; // Default value
$samityShare = 0;

$stmt_utils = $pdo->prepare("SELECT * FROM utils where status = 'A'");
$stmt_utils->execute();
while ($row_utils = $stmt_utils->fetch()) {
    if (isset($row_utils['fee_type'])) {
        if ($row_utils['fee_type'] === 'admission') {
            $admissionfee = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 1500;
            $admissionId = $row_utils['id'];
        } elseif ($row_utils['fee_type'] === 'samity_share') {
            $samityShare = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 5000;
            $samityShareId = $row_utils['id'];
        } elseif ($row_utils['fee_type'] === 'uddokta_share') {
            $uddoktaShare = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 5000;
            $uddoktaShareId = $row_utils['id'];
        } elseif ($row_utils['fee_type'] === 'project_share') {
            $projectShareId = $row_utils['id'];
        }
    }
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
            <?php if (!empty($_SESSION['error_msg'])): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
              <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>
            <form method="post" action="../process/payments_process.php" enctype="multipart/form-data">
  <div class="row">

    <!-- Payment Type Selection -->
    <div class="col-md-6 mb-3">
      <label for="payment_type" class="form-label">পেমেন্ট ফি বাছাই করুন (Select Payment Fee)</label>
      <select class="form-select" id="payment_type" name="payment_type" required onchange="handlePaymentTypeChange()">
        <option value="">ফি বাছাই করুন (Select Fee)</option>
        <?php
          $months = [
              'admission'      => 'সদস্য এন্ট্রি ফি (Member Entry Fee)',
              'Samity Share'   => 'সমিতি শেয়ার ফি (Samity Share Fee)',
              'Uddokta Share'  => 'উদ্যোক্তা শেয়ার ফি (Uddokta Share Fee)',
              'Project Share'  => 'প্রকল্প শেয়ার ফি (Project Share Fee)'
          ];
          foreach ($months as $key => $val): ?>
            <option value="<?= $key ?>" <?= (!empty($payment_type) && $payment_type == $key) ? 'selected' : '' ?>>
              <?= $val ?>
            </option>
          <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6 mb-3">
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

    <div class="col-12 col-md-6 mb-3">
                  <label for="member_id" class="form-label">সদস্যের নাম (Member Name)</label>
                  <select class="form-select select2" id="member_id" name="member_id" required onchange="handleMemberChange()">
                    <option value="">Select Member ( সদস্য বাছাই করুন )</option>
                    <?php foreach($members as $member): ?>
                      <option value="<?= $member['id'] ?>" data-member-code="<?= htmlspecialchars($member['member_code']) ?>">
                        <?= htmlspecialchars($member['name_bn']) ?> (<?= htmlspecialchars($member['name_en']) ?>) (<?= htmlspecialchars($member['member_code']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <input type="hidden" id="member_code" name="member_code" value="">
                  <input type="hidden" id="memberProjects" name="memberProjects" value="">
                  <input type="hidden" id="sundry_amt" name="sundry_amt" value="">
                  <input type="hidden" id="tran_type" name="tran_type" value="">   
                </div>

    <!-- Year Selection -->
    <div class="col-md-6 mb-3">
      <label for="projectSelect" class="form-label">&nbsp;</label>
      <select class="form-select mt-2" id="projectSelect" name="project_id" style="display:none;" onchange="handleProjectChange()">
      </select>
    </div>

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
      <input type="text" class="form-control" id="bank_trans" name="bank_trans" onblur="checkBankTrans(this.value)">
      <small id="bankTransMsg" class="text-danger" style="display:none;">এই লেনদেন নম্বর আগেই ব্যবহার হয়েছে।</small>
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
  // Global variables
  var admissionFee = <?php echo json_encode($admissionfee); ?>;
  var admissionId = <?php echo json_encode(isset($admissionId) ? $admissionId : null); ?>;
  var samityShareId = <?php echo json_encode(isset($samityShareId) ? $samityShareId : null); ?>;
  var projectShareId = <?php echo json_encode(isset($projectShareId) ? $projectShareId : null); ?>;
  var sundrySamityShare = 0;
  var memberProjects = [];
  var memberProjectsData = []; // Store member's project payment data
  var currentMemberAdmissionPaid = false;
  var currentMemberSamityShareAmt = 0;
  var currentMemberExtraShare = 0;
  var perShareValue = <?php echo json_encode($per_share_value); ?>;
  var uddoktaPerShareValue = <?php echo json_encode($uddokta_per_share_value); ?>;

  function handleMemberChange() {
    var memberSelect = document.getElementById('member_id');
    var selectedOption = memberSelect.options[memberSelect.selectedIndex];
    
    if (!selectedOption.value) {
      return;
    }
    
    var memberId = selectedOption.value;
    var memberCode = selectedOption.getAttribute('data-member-code');
    
    // Store member_code in hidden input for form submission
    document.getElementById('member_code').value = memberCode;
    
    // Check if there are existing project IDs
    var existingProjectIds = document.getElementById('memberProjects').value;
    var url = `../process/get_member_data.php?member_id=${memberId}&member_code=${memberCode}`;
    
    // Add project_id parameter if it exists
    if (existingProjectIds && existingProjectIds !== '0') {
      url += `&project_id=${existingProjectIds}`;
    }
    
    // Fetch member data via AJAX
    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
      })
      .then(text => {
        try {
          var data = JSON.parse(text);
          
          if (data.error) {
            alert('Error: ' + data.error);
            return;
          }
          
          if (data.success) {
            
            // Update global variables with member-specific data
            sundrySamityShare = data.sundry_samity_share;
            memberProjects = data.projects; // All available projects
            memberProjectsData = data.memProject || []; // Member's project payment data
            currentMemberAdmissionPaid = data.admission_paid;
            currentMemberSamityShareAmt = data.samity_share_amt;
            currentMemberExtraShare = data.extra_share || 0;

            // Collect all project_ids from memberProjectsData
            var projectIds = memberProjectsData.map(function(mp) {
              return mp.project_id;
            }).join(',');
            document.getElementById('memberProjects').value = projectIds || '0';
            
            // Reset form when member changes
            document.getElementById('amount').value = '';
            document.getElementById('total_share_value').value = '';
            document.getElementById('admissionPaidMsg').style.display = 'none';
            document.getElementById('projectSelect').innerHTML = '<option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>';
            document.getElementById('projectSelect').value = '';
            
            // Re-trigger payment type change to update with new member data
            var paymentType = document.getElementById('payment_type').value;

            if (paymentType) {
              handlePaymentTypeChange();
            }
          } else {
            alert('Failed to fetch member data.');  
          }
        } catch (e) {
          alert('Error parsing server response.');
          
        }
      })
      .catch(error => {
        alert('Fetch Error: ' + error.message);  
      });
  }

  function handlePaymentTypeChange() {
    var type = document.getElementById('payment_type').value;
    var amountInput = document.getElementById('amount');
    var totalShareDiv = document.getElementById('totalShareDiv');
    var totalShareValue = document.getElementById('total_share_value');
    var projectSelect = document.getElementById('projectSelect');
    var paymentModeDiv = document.getElementById('paymentModeDiv');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var submitButton = document.getElementById('submit');
    var tranTypeInput = document.getElementById('tran_type');

    if (type === 'admission') {
      tranTypeInput.value = admissionId;
      amountInput.value = admissionFee;
      totalShareDiv.style.display = 'none';
      projectSelect.style.display = 'none';
      if (currentMemberAdmissionPaid) {
        admissionPaidMsg.style.display = '';
        admissionPaidMsg.innerText = 'আপনার সদস্য ফি প্রদান করা হয়েছে। (Your Membership Fee has already been paid.)';
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
      tranTypeInput.value = samityShareId;
      totalShareDiv.style.display = '';
      totalShareValue.value = sundrySamityShare;
      projectSelect.style.display = 'none';
      admissionPaidMsg.style.display = 'none';
      amountInput.disabled = false;
      amountInput.value = '';
      if (currentMemberSamityShareAmt > 0 && sundrySamityShare == 0) {
        admissionPaidMsg.style.display = '';
        admissionPaidMsg.innerText = 'আপনার সমিতি শেয়ার ফি প্রদান করা হয়েছে। (Your Samity Share Fee has already been paid.)';
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
    } else if (type === 'Uddokta Share') {
      tranTypeInput.value = 3;
      // Fix project_id=2 via hidden select option
      projectSelect.innerHTML = '<option value="2" selected></option>';
      projectSelect.style.display = 'none';
      totalShareDiv.style.display = '';

      var uddoktaData = memberProjectsData.find(function(mp) { return mp.project_id == 2; });
      var uddoktaPaid    = uddoktaData ? (parseFloat(uddoktaData.paid_amount)   || 0) : 0;
      var uddoktaSundry  = uddoktaData ? (parseFloat(uddoktaData.sundry_amount) || 0) : 0;
      if (uddoktaData) {
        uddoktaSundry = currentMemberExtraShare * uddoktaPerShareValue;
      }

      document.getElementById('sundry_amt').value   = uddoktaSundry;
      document.getElementById('memberProjects').value = uddoktaData ? 2 : '0';
      totalShareValue.value = uddoktaSundry;

      admissionPaidMsg.style.display = 'none';
      admissionPaidMsg.innerText = '';
      amountInput.disabled = false;
      amountInput.value = '';
      submitButton.style.display = '';
      paymentModeDiv.style.display = '';

      if (uddoktaPaid > 0 && uddoktaSundry == 0) {
        admissionPaidMsg.style.display = '';
        admissionPaidMsg.innerText = 'উদ্যোক্তা শেয়ার ফি সম্পূর্ণ পরিশোধ করা হয়েছে।';
        amountInput.disabled = true;
        submitButton.style.display = 'none';
        paymentModeDiv.style.display = 'none';
      }
    } else if (type === 'Project Share') {
      tranTypeInput.value = projectShareId;
      // Populate project select — exclude project_id=2 (Uddokta Share)
      projectSelect.innerHTML = '<option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>';
      memberProjects.forEach(function(p) {
        if (p.project_id == 2) return; // Uddokta Share handled separately
        var projectData = memberProjectsData.find(function(mp) { return mp.project_id == p.project_id; });
        var paidAmount   = projectData ? projectData.paid_amount   : 0;
        var shareAmount  = projectData ? projectData.share_amount  : 0;
        var sundryAmount = projectData ? projectData.sundry_amount : 0;
        projectSelect.innerHTML += '<option value="' + p.project_id + '" data-paid-amount="' + paidAmount + '" data-share-amount="' + shareAmount + '" data-sundry-amount="' + sundryAmount + '">' + p.project_name_bn + '</option>';
      });
      projectSelect.style.display = '';
      totalShareDiv.style.display = '';

      var totalProjectShareValue = currentMemberExtraShare * perShareValue;
      totalShareValue.value = totalProjectShareValue;

      admissionPaidMsg.style.display = 'none';
      admissionPaidMsg.innerText = '';
      amountInput.disabled = false;
      amountInput.value = '';
      submitButton.style.display = '';
    } else {
      tranTypeInput.value = '';
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
    } else if (type === 'Uddokta Share') {
      base = parseFloat(document.getElementById('sundry_amt').value) || 0;
      totalShareValue.value = Math.max(base - val, 0);
    } else if (type === 'Project Share') {
      var selected = projectSelect.options[projectSelect.selectedIndex];
      var selectedProjectId = selected ? parseInt(selected.value) : 0;
      var sundryAmount = selected ? parseFloat(selected.getAttribute('data-sundry-amount')) || 0 : 0;
      if (selectedProjectId > 1 && sundryAmount > 0) {
        base = sundryAmount;
      } else {
        base = currentMemberExtraShare * perShareValue;
      }
      totalShareValue.value = Math.max(base - val, 0);
    }

    var shareTypes = ['Samity Share', 'Uddokta Share', 'Project Share'];
    if (shareTypes.indexOf(type) !== -1 && val > base) {
      admissionPaidMsg.style.display = '';
      admissionPaidMsg.innerText = 'টাকার পরিমান কখন ও মোট শেয়ার মূল্যের বেশি দেয়া যাবে না। (Amount cannot be more than Total Share Value.)';
      amountInput.value = '';
      amountInput.disabled = true;
    } else if (shareTypes.indexOf(type) !== -1) {
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
    var paymentModeDiv = document.getElementById('paymentModeDiv');
    var selected = projectSelect.options[projectSelect.selectedIndex];
    
    // Get selected project_id and check if it has sundry_amount
    var selectedProjectId = selected ? parseInt(selected.value) : 0;
    var sundryAmount = selected ? parseFloat(selected.getAttribute('data-sundry-amount')) || 0 : 0;

    document.getElementById('sundry_amt').value = sundryAmount;
    
    // Update memberProjects value based on selected project
    // Check if this project exists in memberProjectsData
    var projectExists = memberProjectsData.find(mp => mp.project_id == selectedProjectId);
    document.getElementById('memberProjects').value = projectExists ? selectedProjectId : '0';
    
    // If project_id > 1 and has sundry_amount, use it; otherwise use extra_share calculation
    var totalProjectShareValue;
    if (selectedProjectId > 1 && sundryAmount > 0) {
      totalProjectShareValue = sundryAmount;
    } else {
      totalProjectShareValue = currentMemberExtraShare * perShareValue;
    }
    totalShareValue.value = totalProjectShareValue;
    
    // Check if this project already has payment
    var paid = selected ? parseFloat(selected.getAttribute('data-paid-amount')) || 0 : 0;
    
    if (paid > 0 && sundryAmount == 0) {
      admissionPaidMsg.style.display = '';
      admissionPaidMsg.innerText = 'আপনার প্রকল্প শেয়ার ফি প্রদান করা হয়েছে। (Your Project Share Fee has already been paid.)';
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

  function checkBankTrans(val) {
    var msg    = document.getElementById('bankTransMsg');
    var submit = document.getElementById('submit');
    val = val.trim();
    if (!val) { msg.style.display = 'none'; return; }
    fetch('../process/check_bank_trans.php?bank_trans=' + encodeURIComponent(val))
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.duplicate) {
          msg.style.display = '';
          if (submit) submit.disabled = true;
        } else {
          msg.style.display = 'none';
          if (submit) submit.disabled = false;
        }
      });
  }

  // Initial call for page load
  document.addEventListener('DOMContentLoaded', function() {
    handlePayModeChange();
  });
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
