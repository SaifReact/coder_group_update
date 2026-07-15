<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'] ?? null;
$member_code = $_SESSION['member_code'] ?? '';
$member = [];
$shareInfo = [];
$monthly_deposit = 0.0;
$project_amount = 0.0;
$total_deposits = 0.0;
$sixty_percent = 0.0;
$products = [];
$grantors = [];

// Fetch loan products from loan_info table
try {
    $stmt = $pdo->query("SELECT id, product_name, savings_percentage FROM loan_info ORDER BY product_name ASC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

// Fetch grantor list from members_info
try {
    $stmt = $pdo->query("SELECT 
        a.id AS member_id,
        CONCAT(a.name_en, ' (', a.name_bn, ')') AS name
    FROM members_info a, user_login b
    WHERE a.id = b.member_id
      AND b.status in ('A', 'P')
    ORDER BY a.id ASC");
    $grantors = $stmt->fetchAll();
} catch (PDOException $e) {
    $grantors = [];
}

// Fetch loan term definitions from loan_charge table
$loanTerms = [];
try {
    $stmt = $pdo->query("SELECT id, loan_info_id, product_name, loan_term, service_charge_rate, late_service_charge_rate, expired_service_charge_rate, verification_charge, effective_date FROM loan_charge ORDER BY loan_term ASC");
    $loanTerms = $stmt->fetchAll();
} catch (PDOException $e) {
    $loanTerms = [];
}

if ($member_id) {
    try {
        $stmt = $pdo->prepare("SELECT name_en AS name, nid, mobile AS phone, member_code FROM members_info WHERE id = ? LIMIT 1");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch() ?: [];
    } catch (PDOException $e) {
        $member = [];
    }

    try {
        $stmt = $pdo->prepare("SELECT no_share, samity_share, samity_share_amt FROM member_share WHERE member_id = ? LIMIT 1");
        $stmt->execute([$member_id]);
        $shareInfo = $stmt->fetch() ?: [];
    } catch (PDOException $e) {
        $shareInfo = [];
    }

    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(paid_amount), 0) AS project_amount FROM member_project WHERE member_id = ? AND member_code = ? AND project_id > 1 AND status = 'A'");
        $stmt->execute([$member_id, $member_code]);
        $pm = $stmt->fetch();
        $project_amount = $pm['project_amount'] ?? 0.0;
    } catch (PDOException $e) {
        $project_amount = 0.0;
    }

    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS monthly_deposit FROM member_payments WHERE member_id = ? AND member_code = ? AND status = 'A' AND payment_method = 'Monthly'");
        $stmt->execute([$member_id, $member_code]);
        $md = $stmt->fetch();
        $monthly_deposit = $md['monthly_deposit'] ?? 0.0;
    } catch (PDOException $e) {
        $monthly_deposit = 0.0;
    }

    $samity_share_amount = isset($shareInfo['samity_share_amt']) ? (float)$shareInfo['samity_share_amt'] : 0.0;
    $total_deposits = $monthly_deposit + $project_amount + $samity_share_amount;
    $savings_percentage = 0.0;
    if (isset($products[0]['savings_percentage'])) {
        $savings_percentage = (float)$products[0]['savings_percentage'];
    }
    $sixty_percent = $total_deposits * ($savings_percentage / 100.0);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? $member_id;
    $product_id = $_POST['product_id'] ?? null;
    $product_code = $_POST['product_code'] ?? '';
    $loan_amount = $_POST['loan_amount'] ?? null;
    $loan_purpose = $_POST['loan_purpose'] ?? null;
    $loan_term = $_POST['loan_term'] ?? null;
    $service_charge_amount = $_POST['service_charge_amount'] ?? 0;
    $verification_charge = $_POST['verification_charge'] ?? 0;
    $late_charge = $_POST['late_charge'] ?? 0;
    $expired_charge = $_POST['expired_charge'] ?? 0;
    $disbursement_amount = $_POST['disbursement_amount'] ?? 0;
    $grantor_id = $_POST['grantor_id'] ?? null;
    $grantor_describes = $_POST['grantor_describes'] ?? '';
    $member_name = $member['name'] ?? ($_POST['member_name'] ?? '');
    $member_code = $_SESSION['member_code'] ?? '';
    $created_by = $_SESSION['user_id'] ?? null;

    $service_charge_amount = floatval(str_replace([',', ' Tk'], '', $service_charge_amount));
    $verification_charge = floatval(str_replace([',', ' Tk'], '', $verification_charge));
    $late_charge = floatval(str_replace([',', ' Tk'], '', $late_charge));
    $expired_charge = floatval(str_replace([',', ' Tk'], '', $expired_charge));
    $disbursement_amount = floatval(str_replace([',', ' Tk'], '', $disbursement_amount));
    $loan_amount = floatval(str_replace([',', ' Tk'], '', $loan_amount));

    try {
        $statusStmt = $pdo->prepare("SELECT status FROM loan_application WHERE member_id = ? AND member_code = ? AND status IN ('I', 'A') ORDER BY id DESC LIMIT 1");
        $statusStmt->execute([$member_id, $member_code]);
        $existingStatus = $statusStmt->fetchColumn();

        if ($existingStatus === 'I') {
            $error = 'আপনার ঋণটি অনুমোদনের জন্য অপেক্ষমান আছে।';
        } elseif ($existingStatus === 'A') {
            $error = 'আপনার ঋণটি এখন ও সচল আছে।';
        } elseif ($existingStatus === 'P') {
            $error = 'আপনার ঋণটি অনুমোদনের জন্য প্রক্রিয়াধীন আছে।';
        } else {
            $insertSql = "INSERT INTO loan_application (member_id, member_code, member_name, product_code, loan_amount, duration, service_charge, verification_charge, late_charge, expired_charge, disbursement_amount, loan_purpose, grantor_id, grantor_describes, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertSql);
            $now = date('Y-m-d H:i:s');
            if ($stmt->execute([
                $member_id,
                $member_code,
                $member_name,
                $product_code,
                $loan_amount,
                $loan_term,
                $service_charge_amount,
                $verification_charge,
                $late_charge,
                $expired_charge,
                $disbursement_amount,
                $loan_purpose,
                $grantor_id,
                $grantor_describes,
                'I',
                $created_by,
                $now
            ])) {
                $success = 'ঋণের জন্য আবেদনপত্র প্রস্তাব করা হইলো।';
            } else {
                $error = 'ঋণের আবেদনপত্র জমা দিতে ব্যর্থ হয়েছেন। অনুগ্রহ করে আবার চেষ্টা করুন।';
            }
        }
    } catch (PDOException $e) {
        $error = 'ঋণের আবেদন জমা দিতে ব্যর্থ হয়েছে: ' . $e->getMessage();
    }
}

// Fetch member info by Member ID (AJAX)
if (isset($_GET['fetch_member']) && isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];
    try {
        $stmt = $pdo->prepare("SELECT name_en AS name, nid, mobile AS phone FROM members_info WHERE id = ? LIMIT 1");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        echo json_encode($member ?: []);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}

// Fetch product info (AJAX)
if (isset($_GET['fetch_product']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    try {
        $stmt = $pdo->prepare("SELECT product_name, product_code, max_loan_amount, min_loan_amount, loan_term, savings_percentage, share_percentage, installment_frequency, service_charge_calculation_method, installment_measurement_method FROM loan_info WHERE id = ? LIMIT 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        echo json_encode($product ?: []);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}
?>
<?php include_once __DIR__ . '/../includes/open.php'; ?>
<?php include_once __DIR__ . '/../includes/side_bar.php'; ?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-12 col-md-12">
                        <h3 class="mb-3 text-primary fw-bold">Loan Application <span class="text-secondary">( ঋণের আবেদন ) </span></h3>
                    </div>
                </div>
                <hr class="mb-4" />
                <?php if(isset($success)) echo '<div class="alert alert-success mt-3">'.$success.'</div>'; ?>
                <?php if(isset($error)) echo '<div class="alert alert-danger mt-3">'.$error.'</div>'; ?>
                <form id="loanForm" method="POST">
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label">সদস্য নং</label>
                            <input type="text" class="form-control" id="member_id" name="member_id" value="<?= htmlspecialchars($member_id) ?>" readonly required>
                        </div>

                        <div class="col-12 col-md-9">
                            <div class="row g-2" id="memberInfoRow">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">সদস্যের নাম</label>
                                    <input type="text" class="form-control" id="member_name" name="member_name" value="<?= htmlspecialchars($member['name'] ?? '') ?>" readonly required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">জাতীয় পরিচয় পত্র</label>
                                    <input type="text" class="form-control" id="member_nid" name="member_nid" value="<?= htmlspecialchars($member['nid'] ?? '') ?>" readonly required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">মোবাইল নং</label>
                                    <input type="text" class="form-control" id="member_phone" name="member_phone" value="<?= htmlspecialchars($member['phone'] ?? '') ?>" readonly required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">মাসিক জমা</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(number_format((float)$monthly_deposit, 2)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">প্রকল্প শেয়ার টাকার পরিমান</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(number_format((float)$project_amount, 2)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">সমিতি শেয়ার টাকার পরিমান</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(number_format((float)($shareInfo['samity_share_amt'] ?? 0), 2)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">সর্বমোট টাকার পরিমান</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(number_format((float)$total_deposits, 2)) ?>" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">প্রোডাক্ট নির্বাচন করুন</label>
                            <select class="form-control" id="product_id" name="product_id" required>
                                <option value="">- নির্বাচন করুন -</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-8" id="productInfo"></div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">প্রোডাক্ট কোড</label>
                            <input type="text" class="form-control" id="product_code_field" name="product_code" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">ঋণের হার %</label>
                            <input type="text" class="form-control" id="savings_percentage_field" name="savings_percentage_field" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">সর্বনিম্ন ঋণ পরিমান</label>
                            <input type="text" class="form-control" id="min_loan_amount_field" name="min_loan_amount_field" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">সর্বোচ্চ ঋণ পরিমান</label>
                            <input type="text" class="form-control" id="max_loan_amount_field" name="max_loan_amount_field" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">কিস্তির ফ্রিকোয়েন্সি</label>  
                            <input type="text" class="form-control" id="installment_frequency_field" name="installment_frequency_field" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">সার্ভিস চার্জ হিসাবের পদ্ধতি</label>    
                            <input type="text" class="form-control" id="service_charge_calculation_method_field" name="service_charge_calculation_method_field" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">কিস্তির পরিমাপের পদ্ধতি</label>
                            <input type="text" class="form-control" id="installment_measurement_method_field" name="installment_measurement_method_field" readonly>
                            <input type="hidden" id="installment_measurement_method_code" name="installment_measurement_method_code" value="">
                        </div>

                        <div class="col-12">
                            <label class="form-label">ঋণের টাকার পরিমান</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_80" value="<?= htmlspecialchars(number_format((float)$sixty_percent, 2, '.', '')) ?>" required checked>
                                            <label class="form-check-label" for="loan_amount_80"><?= htmlspecialchars(number_format((float)$sixty_percent, 2)) ?> Tk (60% of Total)</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_10000" value="10000">
                                            <label class="form-check-label" for="loan_amount_10000">10,000 Tk</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_20000" value="20000">
                                            <label class="form-check-label" for="loan_amount_20000">20,000 Tk</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_30000" value="30000">
                                            <label class="form-check-label" for="loan_amount_30000">30,000 Tk</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_40000" value="40000">
                                            <label class="form-check-label" for="loan_amount_40000">40,000 Tk</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="loan_amount" id="loan_amount_50000" value="50000">
                                            <label class="form-check-label" for="loan_amount_50000">50,000 Tk</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-4">
                            <label class="form-label">ঋণের মেয়াদ (মাস)</label>
                            <select class="form-control" id="loan_term" name="loan_term" required>
                                <option value="">- নির্বাচন করুন -</option>
                            </select>
                        </div>
                       
                        <div class="col-12">
                            <div class="row g-2" id="loanTermDetailFields">
                                <div class="col-12 col-md-2">
                                    <label class="form-label">মেয়াদ (মাস)</label>
                                    <input type="text" class="form-control" id="loan_term_months" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">সার্ভিস চার্জ %</label>
                                    <input type="text" class="form-control" id="loan_term_service_charge" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">বিলম্ব চার্জ %</label>
                                    <input type="text" class="form-control" id="loan_term_late_service_charge" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">মেয়াদোত্তির্ণ চার্জ %</label>
                                    <input type="text" class="form-control" id="loan_term_expired_service_charge" readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">ভেরিফিকেশন টাকার পরিমান</label>
                                    <input type="text" class="form-control" id="loan_term_verification_charge" name="verification_charge" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row g-2" id="loanTermDetailFields">
                                <div class="col-12 col-md-2">
                                    <label class="form-label">নির্বাচিত ঋণের টাকার পরিমান</label>
                                    <input type="text" class="form-control" id="selected_loan_amount" name="selected_loan_amount" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">সার্ভিস চার্জের পরিমান</label>
                                    <input type="text" class="form-control" id="service_charge_amount" name="service_charge_amount" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">বিলম্ব চার্জের পরিমান</label>
                                    <input type="text" class="form-control" id="late_service_amount" name="late_charge" readonly>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">মেয়াদোত্তির্ণ চার্জের পরিমান</label>
                                    <input type="text" class="form-control" id="expired_service_amount" name="expired_charge" readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">বিতরনকৃত টাকার পরিমান</label>
                                    <input type="text" class="form-control" id="remaining_amount" name="disbursement_amount" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm mt-3">
                                <div class="card-header py-2 bg-light"><strong>আসল/মূলধন পরিমানের তালিকা</strong></div>
                                <div class="card-body p-2" id="principal_schedule"></div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label">ঋণের উদ্দেশ্য</label>
                            <input type="text" class="form-control" id="loan_purpose" name="loan_purpose" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label">অনুদানকারীর তথ্য</label>
                            <select class="form-control" id="grantor_id" name="grantor_id" required>
                                <option value="">- নির্বাচন করুন -</option>
                                <option value="others">Others (অন্যান্য)</option>
                                <?php foreach($grantors as $grantor): ?>
                                    <option value="<?= $grantor['member_id'] ?>"><?= $grantor['name'] ?></option>
                                <?php endforeach; ?>               
                            </select>
                        </div>

                        <div class="col-12 col-md-4" id="grantor_others_field" style="display: none;">
                            <label class="form-label">অন্যান্য তথ্য (বিবরণ)</label>
                            <textarea class="form-control" id="grantor_others_text" name="grantor_describes" rows="3" placeholder="এখানে অনুদানকারীর বিবরণ লিখুন..."></textarea>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <?php 
                            $current_month = (int)date('m');
                            $required_deposit = ($current_month - 1) * 2000;
                            $can_apply = $monthly_deposit >= $required_deposit;
                        ?>
                        <?php if($can_apply): ?>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">Apply for Loan</button>
                        <?php else: ?>
                            <p class="text-danger mt-3">
                                <small style="font-size: 1rem; font-weight: bold;">আপনার মাসিক জমা <?= number_format($required_deposit, 0) ?> টাকা এর সমান বা বেশি হওয়া আবশ্যক। বর্তমান জমা: <?= number_format($monthly_deposit, 2) ?> টাকা</small>
                            </p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>


<script>

var loanTerms = <?= json_encode($loanTerms, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

document.addEventListener('DOMContentLoaded', function() {
    var memberInput = document.getElementById('member_id');
    var productInput = document.getElementById('product_id');
    var loanTermSelect = document.getElementById('loan_term');
    var selectedLoanAmountField = document.getElementById('selected_loan_amount');
    var serviceChargeAmountField = document.getElementById('service_charge_amount');
    var remainingAmountField = document.getElementById('remaining_amount');
    var lateServiceAmountField = document.getElementById('late_service_amount');
    var expiredServiceAmountField = document.getElementById('expired_service_amount');
    var principalScheduleContainer = document.getElementById('principal_schedule');
    var sessionMemberId = memberInput ? memberInput.value : '';

    function renderLoanTermDetails(option) {
        var monthsField = document.getElementById('loan_term_months');
        var serviceChargeField = document.getElementById('loan_term_service_charge');
        var lateServiceField = document.getElementById('loan_term_late_service_charge');
        var expiredServiceField = document.getElementById('loan_term_expired_service_charge');
        var verificationField = document.getElementById('loan_term_verification_charge');
        var effectiveDateField = document.getElementById('loan_term_effective_date');

        if (!option || !option.value) {
            if (monthsField) monthsField.value = '';
            if (serviceChargeField) serviceChargeField.value = '';
            if (lateServiceField) lateServiceField.value = '';
            if (expiredServiceField) expiredServiceField.value = '';
            if (verificationField) verificationField.value = '';
            if (effectiveDateField) effectiveDateField.value = '';
            return;
        }

        if (monthsField) monthsField.value = option.value || '';
        if (serviceChargeField) serviceChargeField.value = option.dataset.serviceCharge || '';
        if (lateServiceField) lateServiceField.value = option.dataset.lateServiceCharge || '';
        if (expiredServiceField) expiredServiceField.value = option.dataset.expiredServiceCharge || '';
        if (verificationField) verificationField.value = option.dataset.verificationCharge || '';
        if (effectiveDateField) effectiveDateField.value = option.dataset.effectiveDate || '';
        updateCalculatedAmounts();
    }

    function populateLoanTerms(productId) {
        if (!loanTermSelect) {
            return;
        }
        var options = '<option value="">Select Loan Term</option>';
        loanTerms.forEach(function(term) {
            if (!productId || String(term.loan_info_id) === String(productId)) {
                options += '<option value="' + encodeURIComponent(term.loan_term) + '"' +
                    ' data-product-name="' + (term.product_name ? term.product_name.replace(/"/g, '&quot;') : '') + '"' +
                    ' data-service-charge="' + (term.service_charge_rate ?? '') + '"' +
                    ' data-late-service-charge="' + (term.late_service_charge_rate ?? '') + '"' +
                    ' data-expired-service-charge="' + (term.expired_service_charge_rate ?? '') + '"' +
                    ' data-verification-charge="' + (term.verification_charge ?? '') + '"' +
                    ' data-effective-date="' + (term.effective_date ?? '') + '">' +
                    (term.loan_term ? term.loan_term + ' months' : 'Unknown term') +
                    (term.product_name ? ' - ' + term.product_name : '') +
                    '</option>';
            }
        });
        loanTermSelect.innerHTML = options;
        renderLoanTermDetails(loanTermSelect.options[loanTermSelect.selectedIndex]);
    }

    function parseNumber(value) {
        var parsed = parseFloat(String(value).replace(/,/g, '').replace(/[^0-9.-]/g, ''));
        return isNaN(parsed) ? 0 : parsed;
    }

    function formatCurrency(value) {
        return Number(value).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    function mapInstallmentFrequency(code) {
        var frequencyMap = {
            'M': 'Monthly',
            'Q': 'Quarterly',
            'H': 'Half-Yearly',
            'Y': 'Yearly'
        };
        return frequencyMap[code] || code;
    }

    function mapServiceChargeMethod(code) {
        var methodMap = {
            'F': 'Flat',
            'R': 'Reducing'
        };
        return methodMap[code] || code;
    }

    function mapInstallmentMeasurement(code) {
        var measurementMap = {
            'SCDF': 'Service Charge Deduction First',
            'SCAWL': 'Service Charge Adding With Loan'
        };
        return measurementMap[code] || code;
    }

    function getPaymentDates(months) {
        var dates = [];
        var today = new Date();
        var currentDate = new Date(today.getFullYear(), today.getMonth(), 10);
        
        // If today is after 10th of this month, start from next month's 10th
        if (today.getDate() > 10) {
            currentDate.setMonth(currentDate.getMonth() + 1);
        }
        
        for (var i = 0; i < months; i++) {
            var payDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + i, 10);
            dates.push(payDate);
        }
        return dates;
    }

    function formatPaymentDate(date) {
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return day + '-' + month + '-' + year;
    }

    function updatePrincipalSchedule(selectedAmount, months) {
        if (!principalScheduleContainer) {
            return;
        }
        if (!selectedAmount || !months || months <= 0) {
            principalScheduleContainer.innerHTML = '<div class="text-muted">আসল/মূলধন পরিমানের তালিকা দেখতে ঋণের পরিমাণ ও মেয়াদ নির্বাচন করুন।</div>';
            return;
        }
        var base = Math.floor(selectedAmount / months);
        var total = 0;
        var rows = '';
        var paymentDates = getPaymentDates(months);
        
        for (var i = 1; i <= months; i++) {
            var amount = i === months ? selectedAmount - total : base;
            total += amount;
            var payDate = paymentDates[i - 1];
            rows += '<tr><td class="text-center">' + i + '</td><td class="text-center">' + formatPaymentDate(payDate) + '</td><td class="text-end">' + formatCurrency(amount) + ' Tk</td></tr>';
        }
        var footer = '<tfoot><tr class="table-secondary"><th colspan="2" class="text-end">সর্বমোট</th><th class="text-end">' + formatCurrency(total) + ' Tk</th></tr></tfoot>';
        principalScheduleContainer.innerHTML = '<div class="table-responsive"><table class="table table-bordered mb-0"><thead><tr><th class="text-center">কিস্তি</th><th class="text-center">কিস্তির তারিখ</th><th class="text-end">আসল/মূলধন পরিমান</th></tr></thead><tbody>' + rows + '</tbody>' + footer + '</table></div>';
    }

    function updateServiceChargeWithPrincipalSchedule(selectedAmount, months, serviceChargeTotal, verificationAmount) {
        if (!principalScheduleContainer) {
            return;
        }
        if (!selectedAmount || !months || months <= 0) {
            principalScheduleContainer.innerHTML = '<div class="text-muted">আসল/মূলধন পরিমানের তালিকা দেখতে ঋণের পরিমাণ ও মেয়াদ নির্বাচন করুন।</div>';
            return;
        }

        var principalBase = Math.floor(selectedAmount / months);
        var totalPrincipal = 0;
        var serviceBase = Math.floor(serviceChargeTotal / months);
        var totalService = 0;
        var verificationBase = Math.floor(verificationAmount / months);
        var totalVerification = 0;
        var totalInstallment = 0;
        var rows = '';
        var paymentDates = getPaymentDates(months);

        for (var i = 1; i <= months; i++) {
            var principal = i === months ? selectedAmount - totalPrincipal : principalBase;
            totalPrincipal += principal;

            var serviceCharge = i === months ? serviceChargeTotal - totalService : serviceBase;
            totalService += serviceCharge;

            var verification = i === months ? verificationAmount - totalVerification : verificationBase;
            totalVerification += verification;

            var installment = principal + serviceCharge + verification;
            totalInstallment += installment;

            var payDate = paymentDates[i - 1];
            rows += '<tr>' +
                '<td class="text-center">' + i + '</td>' +
                '<td class="text-center">' + formatPaymentDate(payDate) + '</td>' +
                '<td class="text-end">' + formatCurrency(principal) + ' Tk</td>' +
                '<td class="text-end">' + formatCurrency(serviceCharge) + ' Tk</td>' +
                '<td class="text-end">' + formatCurrency(verification) + ' Tk</td>' +
                '<td class="text-end">' + formatCurrency(installment) + ' Tk</td>' +
                '</tr>';
        }
        var footer = '<tfoot><tr class="table-secondary"><th colspan="2" class="text-end">সর্বমোট</th><th class="text-end">' + formatCurrency(totalPrincipal) + ' Tk</th><th class="text-end">' + formatCurrency(totalService) + ' Tk</th><th class="text-end">' + formatCurrency(totalVerification) + ' Tk</th><th class="text-end">' + formatCurrency(totalInstallment) + ' Tk</th></tr></tfoot>';

        principalScheduleContainer.innerHTML = '<div class="table-responsive"><table class="table table-bordered mb-0"><thead><tr><th class="text-center">কিস্তি</th><th class="text-center">কিস্তির তারিখ</th><th class="text-end">মূলধন</th><th class="text-end">সার্ভিস চার্জ</th><th class="text-end">ভেরিফিকেশন</th><th class="text-end">মোট কিস্তি</th></tr></thead><tbody>' + rows + '</tbody>' + footer + '</table></div>';
    }

    function updateCalculatedAmounts() {
        var checked = document.querySelector('input[name="loan_amount"]:checked');
        var selectedAmount = checked ? parseNumber(checked.value) : 0;
        var serviceRate = parseNumber((document.getElementById('loan_term_service_charge') || {}).value);
        var lateRate = parseNumber((document.getElementById('loan_term_late_service_charge') || {}).value);
        var expiredRate = parseNumber((document.getElementById('loan_term_expired_service_charge') || {}).value);
        var verificationAmount = parseNumber((document.getElementById('loan_term_verification_charge') || {}).value);

        var serviceChargeCalculated = Math.round(selectedAmount * serviceRate / 100);
        var remainingCalculated = Math.round(selectedAmount - (serviceChargeCalculated + verificationAmount));
        var lateServiceCalculated = Math.round(remainingCalculated * lateRate / 100);
        var expiredServiceCalculated = Math.round(remainingCalculated * expiredRate / 100);

        if (serviceChargeAmountField) {
            serviceChargeAmountField.value = serviceChargeCalculated ? formatCurrency(serviceChargeCalculated) + ' Tk' : '0 Tk';
        }
        if (remainingAmountField) {
            remainingAmountField.value = formatCurrency(remainingCalculated) + ' Tk';
        }
        if (lateServiceAmountField) {
            lateServiceAmountField.value = lateServiceCalculated ? formatCurrency(lateServiceCalculated) + ' Tk' : '0 Tk';
        }
        if (expiredServiceAmountField) {
            expiredServiceAmountField.value = expiredServiceCalculated ? formatCurrency(expiredServiceCalculated) + ' Tk' : '0 Tk';
        }
        var termMonths = parseNumber(loanTermSelect ? loanTermSelect.value : 0);
        var measurementCode = (document.getElementById('installment_measurement_method_code') || {}).value || '';
        if (measurementCode === 'SCDF') {
            updatePrincipalSchedule(selectedAmount, termMonths);
        } else {
            updateServiceChargeWithPrincipalSchedule(selectedAmount, termMonths, serviceChargeCalculated, verificationAmount);
        }
    }

    function updateSelectedLoanAmount() {
        if (!selectedLoanAmountField) {
            return;
        }
        var checked = document.querySelector('input[name="loan_amount"]:checked');
        selectedLoanAmountField.value = checked ? formatCurrency(parseNumber(checked.value)) + ' Tk' : '';
        updateCalculatedAmounts();
    }

    if (sessionMemberId) {
        fetch('loan_application.php?fetch_member=1&member_id=' + sessionMemberId)
        .then(response => response.json())
        .then(data => {
            var nameField = document.getElementById('member_name');
            var nidField = document.getElementById('member_nid');
            var phoneField = document.getElementById('member_phone');
            if (data && data.name) {
                if (nameField) nameField.value = data.name || '';
                if (nidField) nidField.value = data.nid || '';
                if (phoneField) phoneField.value = data.phone || '';
            } else {
                if (nameField) nameField.value = 'Member not found.';
                if (nidField) nidField.value = '';
                if (phoneField) phoneField.value = '';
            }
        });
    }

    var loanAmountRadios = document.querySelectorAll('input[name="loan_amount"]');
    loanAmountRadios.forEach(function(radio) {
        radio.addEventListener('change', updateSelectedLoanAmount);
    });

    if (productInput) {
        productInput.addEventListener('change', function() {
            var productId = this.value;
            populateLoanTerms(productId);
            document.getElementById('productInfo').innerHTML = '';
            document.getElementById('product_code_field').value = '';
            document.getElementById('min_loan_amount_field').value = '';
            document.getElementById('max_loan_amount_field').value = '';
            document.getElementById('installment_frequency_field').value = '';
            document.getElementById('service_charge_calculation_method_field').value = '';
            document.getElementById('installment_measurement_method_field').value = '';
            
            if (productId) {
                fetch('loan_application.php?fetch_product=1&product_id=' + productId)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.product_name) {
                            document.getElementById('product_code_field').value = data.product_code || '';
                            document.getElementById('savings_percentage_field').value = (data.savings_percentage !== undefined && data.savings_percentage !== null) ? data.savings_percentage + ' %' : '';
                            document.getElementById('min_loan_amount_field').value = (data.min_loan_amount ? parseFloat(data.min_loan_amount).toLocaleString() : '');
                            document.getElementById('max_loan_amount_field').value = (data.max_loan_amount ? parseFloat(data.max_loan_amount).toLocaleString() : '');
                            document.getElementById('installment_frequency_field').value = mapInstallmentFrequency(data.installment_frequency) || '';
                            document.getElementById('service_charge_calculation_method_field').value = mapServiceChargeMethod(data.service_charge_calculation_method) || '';
                            document.getElementById('installment_measurement_method_field').value = mapInstallmentMeasurement(data.installment_measurement_method) || '';
                            document.getElementById('installment_measurement_method_code').value = data.installment_measurement_method || '';
                            updatePercentLoanAmount(parseNumber(data.savings_percentage));
                        } 
                    });
            }
        });
    }

    if (loanTermSelect) {
        loanTermSelect.addEventListener('change', function() {
            renderLoanTermDetails(this.options[this.selectedIndex]);
        });
        populateLoanTerms(productInput ? productInput.value : '');
    }
    // Disable any loan amount radio option whose numeric value is greater than 50,000
    function disableLargeLoanOptions(maxValue) {
        maxValue = Number(maxValue) || 50000;
        var radios = document.querySelectorAll('input[name="loan_amount"]');
        var firstEnabled = null;
        radios.forEach(function(r) {
            var raw = r.value ? String(r.value).replace(/,/g, '') : '';
            var val = parseFloat(raw);
            if (isNaN(val)) val = 0;
            if (val > maxValue) {
                r.disabled = true;
                var parent = r.closest('.form-check');
                if (parent) parent.classList.add('text-muted');
                if (r.checked) r.checked = false;
            } else {
                r.disabled = false;
                var parent = r.closest('.form-check');
                if (parent) parent.classList.remove('text-muted');
                if (!firstEnabled) firstEnabled = r;
            }
        });
        var anyChecked = Array.from(radios).some(function(r){ return r.checked && !r.disabled; });
        if (!anyChecked && firstEnabled) {
            firstEnabled.checked = true;
            if (typeof updateSelectedLoanAmount === 'function') {
                updateSelectedLoanAmount();
            }
        }
    }

    // Run on load
    disableLargeLoanOptions(50000);
    updateSelectedLoanAmount();

    // Handle grantor Others option
    var grantorSelect = document.getElementById('grantor_id');
    var grantorOthersField = document.getElementById('grantor_others_field');
    var grantorOthersText = document.getElementById('grantor_others_text');

    if (grantorSelect) {
        grantorSelect.addEventListener('change', function() {
            if (this.value === 'others') {
                grantorOthersField.style.display = 'block';
                if (grantorOthersText) grantorOthersText.focus();
            } else {
                grantorOthersField.style.display = 'none';
                if (grantorOthersText) grantorOthersText.value = '';
            }
        });
    }

    // Auto-hide success/error alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert.alert-success, .alert.alert-danger').forEach(function(element) {
            element.style.transition = 'opacity 0.4s ease';
            element.style.opacity = '0';
            setTimeout(function() {
                if (element.parentNode) {
                    element.parentNode.removeChild(element);
                }
            }, 400);
        });
    }, 5000);
});
</script>
