<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

include_once __DIR__ . '/../includes/open.php';

$method = $_SERVER['REQUEST_METHOD'];

// Helper function to send SMS
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

    error_log("SMS Data: " . print_r($data, true));

    $url = $sms_api_url . '?' . http_build_query($data);

    error_log("Generated SMS URL: $url");
    error_log("Sending SMS to: $mobile with message: $message");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    error_log("SMS Response: $response");
    return $response;
}

// Fetch all payments
$stmt = $pdo->query("SELECT a.id, a.member_id, a.payment_method, a.bank_pay_date, a.bank_trans_no, a.trans_no, a.amount, a.status, b.member_code, b.name_en, b.name_bn, b.mobile FROM member_payments a, members_info b WHERE b.id = a.member_id ORDER BY a.id DESC");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['pay_id'], $_POST['status'])) {
    $pay_id = (int)$_POST['pay_id'];
    $status = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    $stmt = $pdo->prepare("UPDATE member_payments SET status = ? WHERE id = ?");
    $stmt->execute([$status, $pay_id]);

    // Set dynamic success message based on status
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ সমিতিতে আপনার পেমেন্ট এর সকল তথ্য সঠিক, অনুমোদন দেয়া হইলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার পেমেন্টটি  অপেক্ষমান অবস্থায় আছে, নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতিতে আপনার পেমেন্ট এর কোনো তথ্য পাওয়া যায়নি,বাতিল করা হইলো !";
    }
    
    if ($payments['mobile']) {
            $sms_response = sms_send($payments['mobile'], $success_msg);
            if ($sms_response === false) {
                $sms_error_msg = '❌ SMS পাঠানো যায়নি।';
            } else {
                $sms_result = json_decode($sms_response, true);
                if (isset($sms_result['error']) && $sms_result['error'] != 0) {
                    $sms_error_msg = '❌ SMS পাঠানো যায়নি: ' . ($sms_result['message'] ?? 'Unknown error');
                } else {
                    $sms_success_msg = '✅ SMS সফলভাবে পাঠানো হয়েছে।';
                    $success_msg .= ' ' . $sms_success_msg;
                }
            }    
        }

        if (isset($sms_error_msg)) {
            $success_msg .= ' ' . $sms_error_msg;
        }

        $_SESSION['success_msg'] = $success_msg;

    header('Location: ../admin/payment_approval.php'); // Redirect to avoid form resubmission
    exit;
}
?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
            <div class="container">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Payment Approval <span class="text-secondary">( পেমেন্ট অনুমোদন )</span></h3> 
                      <hr class="mb-4" />
                      
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Member Info</th>
                                        <th>Member Name</th>
                                        <th>Payment Method</th>
                                        <th>Bank Pay Info</th>
                                        <th>Trans No</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($payment['member_code']) ?><br/> 
                                            <?= htmlspecialchars($payment['name_bn']) ?><br/>
                                            <?= htmlspecialchars($payment['name_en']) ?></td>
                                        <td><?= htmlspecialchars($payment['mobile']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($payment['payment_method'])) ?></td>
                                        <td><?= htmlspecialchars($payment['bank_pay_date']) ?><br/>
                                            <?= htmlspecialchars($payment['bank_trans_no']) ?></td>
                                        <td><?= htmlspecialchars($payment['trans_no']) ?></td>
                                        <td><?= htmlspecialchars($payment['amount']) ?></td>
                                        <td>
                                            <form method="post" class="d-flex flex-column align-items-start gap-2">
                                                <input type="hidden" name="pay_id" value="<?= $payment['id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2" style="min-width:120px;">
                                                    <option value="A" <?= $payment['status'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                    <option value="I" <?= $payment['status'] === 'I' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                    <option value="R" <?= $payment['status'] === 'R' ? 'selected' : '' ?>>❌ Rejected</option>
                                                </select>
                                                <br/>
                                                <button type="submit" class="btn btn-success btn-sm">Update (হালনাগাদ)</button>
                                            </form>
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


<?php include_once __DIR__ . '/../includes/end.php'; ?>


