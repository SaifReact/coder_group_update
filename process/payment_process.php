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
    $member_id = $_SESSION['member_id'];
    $member_code = $_SESSION['member_code'];
    $payment_method = $_POST['payment_type'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $bank_pay_date = $_POST['payment_date'] ?? '';
    $bank_trans_no = $_POST['bank_trans'] ?? '';
    $created_by = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');

    // Get no_share from member_share table
    $stmt = $pdo->prepare("SELECT no_share, admission_fee FROM member_share WHERE member_id = ? LIMIT 1");
    $stmt->execute([$member_id]);
    $share_data = $stmt->fetch();
    $no_share = $share_data ? (float)$share_data['no_share'] : 1;

    // Check if admission_fee already paid for this user
    if ($payment_method === 'admission') {
        if ($share_data && isset($share_data['admission_fee']) && (float)$share_data['admission_fee'] > 0) {
            $_SESSION['error_msg'] = 'Admission fee already paid for this user.';
            header('Location: ../users/payment.php');
            exit;
        }
    }

    // Check if payment already exists for this month and year
    if ($payment_method !== 'admission' && $payment_method !== 'share') {
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

    if ($payment_method === 'admission' && $amount > 0) {
        // Fixed admission fees
        $admission_fee = $amount;
        $idcard_fee = 150;
        $passbook_fee = 200;
        $softuses_fee = 400;
        $sms_fee = 100;
        $office_rent = 300;
        $office_staff = 200;
        $other_fee = 150;

        // Insert into member_payments table
        $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees, created_by, payment_slip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $member_code, $payment_method, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $amount, 'admission', $created_by, $pay_slip]);

        // Update member_share table
        $stmt = $pdo->prepare("UPDATE member_share SET admission_fee = ?, idcard_fee = ?, passbook_fee = ?, softuses_fee = ?, sms_fee = ?, office_rent = ?, office_staff = ?, other_fee = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$admission_fee, $idcard_fee, $passbook_fee, $softuses_fee, $sms_fee, $office_rent, $office_staff, $other_fee, $member_id, $member_code]);

        $_SESSION['success_msg'] = '✅ Admission Fee Payment Successfully..! (সফলভাবে ভর্তি ফি পেমেন্ট করা হলো..!)';
        header('Location: ../users/payment.php');
        exit;
    } else if ($payment_method === 'share' && $amount > 0) {
        // Fixed admission fees
        $share_amount = round($amount * 0.98, 2);
        $other_fee = round($amount * 0.02, 2);

        // Insert into member_payments table
        $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees, created_by, payment_slip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $member_code, $payment_method, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $amount, 'share', $created_by, $pay_slip]);

        // Update member_share table
        $stmt = $pdo->prepare("UPDATE member_share SET share_amount = ?, other_fee = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$share_amount, $other_fee, $member_id, $member_code]);

        $_SESSION['success_msg'] = '✅ Share Fee Payment Successfully..! (সফলভাবে শেয়ার ফি পেমেন্ট করা হলো..!)';
        header('Location: ../users/payment.php');
        exit;
    } else if ($payment_method != 'admission' && $payment_method != 'share' && $amount > 0) {
        // Calculate fees for non-admission
        $for_install = round($amount * 0.98, 2);
        $other_fee = round($amount * 0.02, 2);

        // Fees to insert
        $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees, created_by, payment_slip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $member_code, $payment_method, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $amount, $payment_method, $created_by, $pay_slip]);

        // Update member_share table and add previous_amount
        $stmt = $pdo->prepare("UPDATE member_share SET for_install = for_install + ?, other_fee = other_fee + ?, created_at = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$for_install, $other_fee, $created_at, $member_id, $member_code]);
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
