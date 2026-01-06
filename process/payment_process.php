<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = isset($_SESSION['member_id'])? $_SESSION['member_id'] : '';

// Payment folder
$payment_folder = '../payment/';
if (!is_dir($payment_folder)) {
    mkdir($payment_folder, 0777, true);
}

// Helper to upload image
function uploadPaymentSlip($file) {
    global $payment_folder;
    global $member_id;
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return null;
        }
        $filename = 'payment_slip_' . $member_id . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $payment_folder . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_year = $_POST['payment_year'] ?? '';
    $project_id = $_POST['project_id'] ?? 0;
    $member_id = $_SESSION['member_id'] ?? 0;
    $member_code = $_SESSION['member_code'] ?? '';
    $payment_method = $_POST['payment_type'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $bank_pay_date = $_POST['payment_date'] ?? '';
    // Convert empty date to NULL
    $bank_pay_date = !empty($bank_pay_date) ? $bank_pay_date : null;
    $bank_trans_no = $_POST['bank_trans'] ?? '';
    $pay_mode = $_POST['pay_mode'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    $created_by = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');
    $total_share_value = floatval($_POST['total_share_value'] ?? 0);

    // Fetch monthly fee from utils table
    $monthly_fee = 0; // Default value
    $stmt_utils = $pdo->prepare("SELECT * FROM utils WHERE fee_type = 'monthly' AND status = 'A' LIMIT 1");
    $stmt_utils->execute();
    if ($row_utils = $stmt_utils->fetch()) {
        $monthly_fee = isset($row_utils['fee']) ? (float)$row_utils['fee'] : 2000;
    }

    // Get no_share and extra_share from member_share table
    $stmt = $pdo->prepare("SELECT no_share, extra_share, admission_fee FROM member_share WHERE member_id = ? LIMIT 1");
    $stmt->execute([$member_id]);
    $share_data = $stmt->fetch();
    $no_share = $share_data ? (float)$share_data['no_share'] : 1;
    $extra_share = $share_data ? (float)$share_data['extra_share'] : 0;

    // Check if payment already exists for this month and year
    if ($payment_method !== 'admission' && $payment_method !== 'Samity Share' && $payment_method !== 'Project Share') {
        $stmt = $pdo->prepare("SELECT id FROM member_payments WHERE member_id = ? AND payment_method = ? AND payment_year = ? LIMIT 1");
        $stmt->execute([$member_id, $payment_method, $payment_year]);
        if ($stmt->fetch()) {
            $_SESSION['error_msg'] = 'Payment for this month and year already exists.';
            header('Location: ../users/payment.php');
            exit;
        }
    }
    // Generate serial_no for this payment_method and payment_year
    $serial_no = 1;
    $stmt = $pdo->prepare("SELECT MAX(serial_no) as max_serial FROM member_payments WHERE payment_method = ? AND payment_year = ?");
    $stmt->execute([$payment_method, $payment_year]);
    if ($row = $stmt->fetch()) {
        $serial_no = intval($row['max_serial']) + 1;
    }

    // Generate trans_no as payment_method-payment_year-serial_no
    $trans_no = 'TR' . strtoupper($payment_method) . $payment_year . $serial_no;

    $pay_slip = uploadPaymentSlip($_FILES['payment_slip']);

    $months = ['january','february','march','april','may','june','july','august','september','october','november','december'];
    if ($payment_method == 'advance') {
        // Advance payment logic
        $start_month_index = isset($_POST['start_month']) ? array_search($_POST['start_month'], $months) : 0;
        $months_advance = max(1, floor($amount / $monthly_fee));
        $remaining = $amount;
        $success_count = 0;
        for ($i = 0; $i < $months_advance; $i++) {
            $cur_month_index = $start_month_index + $i;
            $cur_year = $payment_year;
            if ($cur_month_index > 11) {
                $cur_month_index = $cur_month_index % 12;
                $cur_year = $payment_year + 1;
            }
            $cur_month = $months[$cur_month_index];
            // Check if already paid
            $stmt = $pdo->prepare("SELECT id FROM member_payments WHERE member_id = ? AND payment_method = ? AND payment_year = ? LIMIT 1");
            $stmt->execute([$member_id, $cur_month, $cur_year]);
            if ($stmt->fetch()) {
                continue; // skip already paid
            }
            // Serial no for this month
            $serial_no = 1;
            $stmt = $pdo->prepare("SELECT MAX(serial_no) as max_serial FROM member_payments WHERE payment_method = ? AND payment_year = ?");
            $stmt->execute([$cur_month, $cur_year]);
            if ($row = $stmt->fetch()) {
                $serial_no = intval($row['max_serial']) + 1;
            }
            $trans_no = 'TR' . strtoupper($cur_month) . $cur_year . $serial_no;
            $cur_amount = ($remaining >= $monthly_fee) ? $monthly_fee : $remaining;
            $late_fee = 0;
            // Late fee logic: if payment date is not within 1-30 of the month
            if ($bank_pay_date) {
                $payDate = strtotime($bank_pay_date);
                $payMonth = (int)date('n', $payDate) - 1;
                $payYear = (int)date('Y', $payDate);
                $payDay = (int)date('j', $payDate);
                if ($payYear == $cur_year && $payMonth == $cur_month_index) {
                    if ($payDay < 1 || $payDay > 30) {
                        $late_fee = $late;
                        $cur_amount += $late_fee;
                    }
                } else {
                    $late_fee = $late;
                    $cur_amount += $late_fee;
                }
            }
            $for_install = round($cur_amount * 0.95, 2);
            $other_fee = round($cur_amount * 0.05, 2);
            $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, project_id, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees, created_by, payment_slip, status, pay_mode, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$member_id, $member_code, $cur_month, $project_id, $cur_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $cur_amount, $payment_method, $created_by, $pay_slip, 'I', $pay_mode, $remarks]);
            // Update member_share table
            $stmt = $pdo->prepare("UPDATE member_share SET for_install = for_install + ?, other_fee = other_fee + ?, late_fee = late_fee + ?, created_at = ? WHERE member_id = ? AND member_code = ?");
            $stmt->execute([$for_install, $other_fee, $late_fee, $created_at, $member_id, $member_code]);
            $remaining -= $cur_amount;
            $success_count++;
            if ($remaining < $monthly_fee) break;
        }
        if ($success_count > 0) {
            $_SESSION['success_msg'] = '✅ সফলভাবে ' . $success_count . ' মাসের পেমেন্ট করা হয়েছে, অনুমোদনের জন্য অপেক্ষা করুন (Payment successful for ' . $success_count . ' months, please wait for approval)';
        } else {
            $_SESSION['error_msg'] = 'Already paid for selected months or invalid amount.';
        }
        header('Location: ../users/payment.php');
        exit;
    } elseif ($amount > 0) {
        $late_fee = 0;
        if ($amount > $monthly_fee) {
            $late_fee = round($amount - $monthly_fee, 2);
        }
        $for_install = round($amount * 0.95, 2);
        $other_fee = round($amount * 0.05, 2);
        // Fees to insert
        $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, project_id, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees, created_by, payment_slip, status, pay_mode, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $member_code, $payment_method, $project_id, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $amount, $payment_method, $created_by, $pay_slip, 'I', $pay_mode, $remarks]);
        // Update member_share table and add previous_amount + late_fee
        $stmt = $pdo->prepare("UPDATE member_share SET for_install = for_install + ?, other_fee = other_fee + ?, late_fee = late_fee + ?, created_at = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$for_install, $other_fee, $late_fee, $created_at, $member_id, $member_code]);
        $_SESSION['success_msg'] = '✅ সফলভাবে পেমেন্ট করা হয়েছে, অনুমোদনের জন্য অপেক্ষা করুন (Payment successful, please wait for approval)';
        header('Location: ../users/payment.php');
        exit;
    } else {
        $_SESSION['error_msg'] = 'Invalid payment type or amount.';
        header('Location: ../users/payment.php');
        exit;
    }
}
?>
