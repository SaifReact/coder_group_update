<?php
session_start();
include_once '../config/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
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
    // Check if admission_fee already paid for this user
    if ($payment_method === 'admission') {
        $stmt = $pdo->prepare("SELECT admission_fee FROM member_share WHERE member_id = ? LIMIT 1");
        $stmt->execute([$member_id]);
        $row = $stmt->fetch();
        if ($row && !is_null($row['admission_fee'])) {
            $_SESSION['error_msg'] = 'Admission fee already paid for this user.';
            header('Location: ../users/payment.php');
            exit;
        }
    }
    if ($payment_method === 'admission' && $amount > 0) {
        // Calculate fees
        $idcard_fee = round($amount * 0.02, 2);
        $passbook_fee = round($amount * 0.02, 2);
        $other_fee = round($amount * 0.10, 2);
        $softuses_fee = round($amount * 0.06, 2);
        $for_samity = round($amount * 0.30, 2);
        $rest = $amount * 0.50;
        $rest_each = round($rest / 5, 2);
        $cma = $rest_each;
        $chb = $rest_each;
        $cii = $rest_each;
        $cht = $rest_each;
        $cnf = $rest_each;
        $for_install = 0;
        // Fees to insert
        $fees = [
            'idcard_fee' => $idcard_fee,
            'passbook_fee' => $passbook_fee,
            'other_fee' => $other_fee,
            'softuses_fee' => $softuses_fee,
            'for_samity' => $for_samity,
            'for_install' => $for_install,
            'cma' => $cma,
            'chb' => $chb,
            'cii' => $cii,
            'cht' => $cht,
            'cnf' => $cnf
        ];
        foreach ($fees as $method => $fee_amount) {
            if ($fee_amount > 0) {
                // Generate serial_no for this payment_method and payment_year
                $serial_no = 1;
                $stmt = $pdo->prepare("SELECT MAX(serial_no) as max_serial FROM member_payments WHERE payment_method = ? AND payment_year = ?");
                $stmt->execute([$method, $payment_year]);
                if ($row = $stmt->fetch()) {
                    $serial_no = intval($row['max_serial']) + 1;
                }
                // Generate trans_no as payment_method-payment_year-serial_no
                $trans_no = 'TR' . $payment_method . $payment_year . $serial_no;
                // Insert into member_payments table
                $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$member_id, $member_code, $payment_method, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $fee_amount, $method]);
            }
        }
        // Update member_share table
        $stmt = $pdo->prepare("UPDATE member_share SET admission_fee = ?, idcard_fee = ?, passbook_fee = ?, other_fee = ?, softuses_fee = ?, for_samity = ?, for_install = ?, cma = ?, chb = ?, cii = ?, cht = ?, cnf = ?, created_at = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$amount, $idcard_fee, $passbook_fee, $other_fee, $softuses_fee, $for_samity, $for_install, $cma, $chb, $cii, $cht, $cnf, $created_at, $member_id, $member_code]);
        $_SESSION['success_msg'] = '✅ Admission - Share Fee Payment Successfully..! (সফলভাবে ভর্তি - শেয়ার ফি পেমেন্ট করা হলো..!) Admission payment successful.';
        header('Location: ../users/payment.php');
        exit;
    } else if ($payment_method != 'admission' && $amount > 0) {
        // Calculate fees for non-admission
        $for_install = round($amount * 0.20, 2);
        $other_fee = round($amount * 0.05, 2);
        $for_samity = round($amount * 0.30, 2);
        $rest = $amount * 0.45;
        $rest_each = round($rest / 5, 2);
        $cma = $rest_each;
        $chb = $rest_each;
        $cii = $rest_each;
        $cht = $rest_each;
        $cnf = $rest_each;
        // Fees to insert
        $fees = [
            'for_install' => $for_install,
            'other_fee' => $other_fee,
            'for_samity' => $for_samity,
            'cma' => $cma,
            'chb' => $chb,
            'cii' => $cii,
            'cht' => $cht,
            'cnf' => $cnf
        ];
        foreach ($fees as $method => $fee_amount) {
            if ($fee_amount > 0) {
                // Generate serial_no for this payment_method and payment_year
                $serial_no = 1;
                $stmt = $pdo->prepare("SELECT MAX(serial_no) as max_serial FROM member_payments WHERE payment_method = ? AND payment_year = ?");
                $stmt->execute([$method, $payment_year]);
                if ($row = $stmt->fetch()) {
                    $serial_no = intval($row['max_serial']) + 1;
                }
                // Generate trans_no as payment_method-payment_year-serial_no
                $trans_no = 'TR' . $payment_method . $payment_year . $serial_no;
                // Insert into member_payments table
                $stmt = $pdo->prepare("INSERT INTO member_payments (member_id, member_code, payment_method, payment_year, bank_pay_date, bank_trans_no, trans_no, serial_no, amount, for_fees) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$member_id, $member_code, $payment_method, $payment_year, $bank_pay_date, $bank_trans_no, $trans_no, $serial_no, $fee_amount, $method]);
            }
        }
        // Update member_share table and add previous_amount
        $stmt = $pdo->prepare("UPDATE member_share SET for_install = for_install + ?, other_fee = other_fee + ?, for_samity = for_samity + ?, cma = cma + ?, chb = chb + ?, cii = cii + ?, cht = cht + ?, cnf = cnf + ?, created_at = ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$for_install, $other_fee, $for_samity, $cma, $chb, $cii, $cht, $cnf, $created_at, $member_id, $member_code]);
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
