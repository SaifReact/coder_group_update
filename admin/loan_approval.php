<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../process/loan_schedule_process.php';

$method = $_SERVER['REQUEST_METHOD'];

function sms_send($mobile, $message) {
    $sms_api_url = "http://bulksmsbd.net/api/smsapi";
    $api_key = "B5NrU3gcYDTzS4AdGGIo";
    $sender_id = "8809648903446";

    $data = [
        'api_key' => $api_key,
        'type' => 'text',
        'number' => $mobile,
        'senderid' => $sender_id,
        'message' => $message,
    ];

    $url = $sms_api_url . '?' . http_build_query($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if ($response === false) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return $response;
}

// Fetch loan approval records
$stmt = $pdo->query("SELECT a.*, b.name_en, b.name_bn, b.mobile, c.product_name FROM loan_application a LEFT JOIN members_info b ON a.member_id = b.id LEFT JOIN loan_info c ON a.product_code = c.product_code WHERE a.status = 'P' ORDER BY a.id DESC");
$loanApprovals = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($method === 'POST' && isset($_POST['loan_id'], $_POST['status'])) {
    $loan_id = (int)$_POST['loan_id'];
    $status = in_array($_POST['status'], ['A', 'R']) ? $_POST['status'] : 'A';
    $user_id = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE loan_application SET status = ?, updated_by = ?, updated_at = ? WHERE id = ?");
    $stmt->execute([$status, $user_id, $now, $loan_id]);

    if ($stmt->rowCount()) {
        if ($status === 'A') {
            $_SESSION['success_msg'] = '✅ Loan approval accepted successfully.';
            
            // Fetch loan details for schedule generation
            $stmtLoan = $pdo->prepare("SELECT a.*, c.installment_measurement_method FROM loan_application a LEFT JOIN loan_info c ON a.product_code = c.product_code WHERE a.id = ? LIMIT 1");
            $stmtLoan->execute([$loan_id]);
            $loanData = $stmtLoan->fetch(PDO::FETCH_ASSOC);
            
            // If product uses SCDF method, generate schedule
            if ($loanData && ($loanData['installment_measurement_method'] ?? null) === 'SCDF') {
                create_loan_schedule_table();
                
                // Delete existing schedule
                $stmtDel = $pdo->prepare("DELETE FROM loan_schedule WHERE loan_id = ?");
                $stmtDel->execute([$loan_id]);
                
                // Generate new schedule
                $result = generate_loan_schedule(
                    $loan_id,
                    $loanData['loan_amount'],
                    $loanData['duration'],
                    $loanData['service_charge'],
                    $loanData['verification_charge'],
                    $loanData['member_id'],
                    $loanData['member_code'],
                    $loanData['product_code'],
                    $user_id
                );
                
                if ($result) {
                    $_SESSION['success_msg'] .= ' ✓ Loan schedule generated.';
                }
            }
        } else {
            $_SESSION['success_msg'] = '❌ Loan approval rejected.';
        }

        // Send SMS if available
        $stmt = $pdo->prepare("SELECT b.mobile, a.member_code FROM loan_application a LEFT JOIN members_info b ON a.member_id = b.id WHERE a.id = ? LIMIT 1");
        $stmt->execute([$loan_id]);
        $loanData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($loanData['mobile'])) {
            $sms_response = sms_send($loanData['mobile'], $_SESSION['success_msg']);
            if ($sms_response === false) {
                $_SESSION['success_msg'] .= ' SMS পাঠানো যায়নি।';
            }
        }
    } else {
        $_SESSION['error_msg'] = 'Failed to update loan approval status.';
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>

<?php include_once __DIR__ . '/../includes/open.php'; ?>
<?php include_once __DIR__ . '/../includes/side_bar.php'; ?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Loan Approval <span class="text-secondary">(ঋণ অনুমোদন)</span></h3>
                <hr class="mb-4" />

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success mt-3"><?php echo $_SESSION['success_msg']; ?></div>
                    <?php unset($_SESSION['success_msg']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger mt-3"><?php echo $_SESSION['error_msg']; ?></div>
                    <?php unset($_SESSION['error_msg']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Member Code</th>
                                <th>Member Name</th>
                                <th>Product Code</th>
                                <th>Amount</th>
                                <th>Duration</th>
                                <th>Purpose</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loanApprovals as $loan): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($loan['id']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['member_code']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['name_bn'] ?: $loan['name_en']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format((float)$loan['loan_amount'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($loan['duration']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['loan_purpose']); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form method="post" class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['id']); ?>">
                                                    <select name="status" class="form-select form-select-sm">
                                                        <option value="A" <?php echo $loan['status'] === 'A' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="R" <?php echo $loan['status'] === 'R' ? 'selected' : ''; ?>>Rejected</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                </div>
                                            </form>
                                            <button type="button" class="btn btn-info btn-sm view-loan-btn" data-loan-id="<?php echo htmlspecialchars($loan['id']); ?>" data-member-id="<?php echo htmlspecialchars($loan['member_id']); ?>" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
                            </div>
                            </div>
<!-- Modal for viewing loan details -->
<div class="modal fade" id="viewLoanModal" tabindex="-1" aria-labelledby="viewLoanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:80vw;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewLoanModalLabel">Loan Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewLoanModalBody">
        <!-- Details will be loaded here by JS -->
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-loan-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var loanId = this.getAttribute('data-loan-id');
            var memberId = this.getAttribute('data-member-id');
            var modalBody = document.getElementById('viewLoanModalBody');
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
            var modal = new bootstrap.Modal(document.getElementById('viewLoanModal'));
            modal.show();
            // Fetch details via AJAX
            fetch('loan_details.php?loan_id=' + encodeURIComponent(loanId) + '&member_id=' + encodeURIComponent(memberId))
                .then(resp => resp.text())
                .then(html => { modalBody.innerHTML = html; })
                .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Could not load details.</div>'; });
        });
    });
});
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
