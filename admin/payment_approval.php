<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

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
$stmt = $pdo->query("SELECT a.id, a.member_id, a.member_code, a.payment_method, a.bank_pay_date, a.bank_trans_no, a.trans_no,
a.amount, a.status, c.name_en, c.name_bn, c.mobile, COALESCE(b.id, 0) AS member_project_id
FROM member_payments a
LEFT JOIN (
    SELECT mp.*
    FROM member_project mp
    INNER JOIN (
        SELECT member_id, member_code, MAX(id) AS max_id
        FROM member_project
        GROUP BY member_id, member_code
    ) latest ON mp.member_id = latest.member_id AND mp.member_code = latest.member_code AND mp.id = latest.max_id
) b ON a.member_id = b.member_id AND a.member_code = b.member_code
INNER JOIN members_info c ON a.member_id = c.id AND a.member_code = c.member_code
ORDER BY a.id DESC");

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['member_id'], $_POST['member_code'], $_POST['pay_id'], $_POST['status'])) {
    $member_id = (int)$_POST['member_id'];
    $member_code = $_POST['member_code'];
    $member_project_id = (int)$_POST['member_project_id'];
    $pay_id = (int)$_POST['pay_id'];
    $status = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    // Get payment details for the specific pay_id
    $stmt = $pdo->prepare("SELECT * FROM member_payments WHERE id = ?");
    $stmt->execute([$pay_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        // Payment not found, set error message and stay on the same page
        $_SESSION['error_msg'] = "❌ পেমেন্ট তথ্য পাওয়া যায়নি!";
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Update payment status
    $stmt = $pdo->prepare("UPDATE member_payments SET status = ? WHERE id = ? AND member_id = ? AND member_code = ?");
    $stmt->execute([$status, $pay_id, $member_id, $member_code]);
    
    $stmtProject = $pdo->prepare("UPDATE member_project SET status = ? WHERE id = ? AND member_id = ? AND member_code = ?");
    $stmtProject->execute([$status, $member_project_id, $member_id, $member_code]);

    // Set dynamic success message based on status
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ সমিতিতে আপনার পেমেন্ট এর সকল তথ্য সঠিক, অনুমোদন দেয়া হইলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার পেমেন্টটি  অপেক্ষমান অবস্থায় আছে, নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতিতে আপনার পেমেন্ট এর কোনো তথ্য পাওয়া যায়নি,বাতিল করা হইলো !";
    }

    // Send SMS if the mobile number exists
    if ($payment['mobile']) {
        $sms_response = sms_send($payment['mobile'], $_SESSION['success_msg']);
        if ($sms_response === false) {
            $sms_error_msg = '❌ SMS পাঠানো যায়নি।';
        } else {
            $sms_result = json_decode($sms_response, true);
            if (isset($sms_result['error']) && $sms_result['error'] != 0) {
                $sms_error_msg = '❌ SMS পাঠানো যায়নি: ' . ($sms_result['message'] ?? 'Unknown error');
            } else {
                $sms_success_msg = '✅ SMS সফলভাবে পাঠানো হয়েছে।';
                $_SESSION['success_msg'] .= ' ' . $sms_success_msg;
            }
        }
    }

    // If there was an SMS error, append it to success message
    if (isset($sms_error_msg)) {
        $_SESSION['success_msg'] .= ' ' . $sms_error_msg;
    }

    // Stay on the same page after submission
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
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
                        <div class="row">
                            <div class="col-md-8"><h3 class="mb-3 text-primary fw-bold">Payment Approval <span class="text-secondary">( পেমেন্ট অনুমোদন )</span></h3></div>
                            <div class="col-md-4 text-end"><a href="../admin/payment.php"><button type="button" class="btn btn-sm btn-success">
                                Payment ( পেমেন্ট )      </button></a></div>
                        </div>
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
                                                <input type="hidden" name="member_id" value="<?= $payment['member_id'] ?>">
                                                <input type="hidden" name="member_code" value="<?= $payment['member_code'] ?>">
                                                <input type="hidden" name="member_project_id" value="<?= $payment['member_project_id'] ?>">
                                                <input type="hidden" name="pay_id" value="<?= $payment['id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2" style="min-width:120px;">
                                                    <option value="A" <?= $payment['status'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                    <option value="I" <?= $payment['status'] === 'I' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                    <option value="R" <?= $payment['status'] === 'R' ? 'selected' : '' ?>>❌ Rejected</option>
                                                </select>
                                                <button type="submit" class="btn btn-success btn-sm mt-2">Update (হালনাগাদ)</button>
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


