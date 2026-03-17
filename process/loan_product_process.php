<?php
session_start();
include_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

// Insert new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'insert') {
    $product_type = $_POST['product_type'] ?? '';
    $product_code = $_POST['product_code'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $start_range = floatval($_POST['start_range'] ?? 0);
    $end_range = floatval($_POST['end_range'] ?? 0);
    $service_charge = floatval($_POST['service_charge'] ?? 0);
    $tenure = intval($_POST['tenure'] ?? 0);
    $installment_type = $_POST['installment_type'] ?? '';
    $processing_fee = floatval($_POST['processing_fee'] ?? 0);
    $late_fee = floatval($_POST['late_fee'] ?? 0);
    $max_loan_per_member = intval($_POST['max_loan_per_member'] ?? 1);
    $allow_extra_payment = $_POST['allow_extra_payment'] ?? '';
    $allow_multiple_disbursement = $_POST['allow_multiple_disbursement'] ?? '';

    // Validation
    if ($service_charge < 8 || $service_charge > 10) {
        die('Service charge must be between 8% and 10%.');
    }
    if ($tenure < 3 || $tenure > 12) {
        die('Tenure must be between 3 and 12 months.');
    }
    if ($processing_fee < 0 || $processing_fee > 1) {
        die('Processing fee must be between 0% and 1%.');
    }
    if ($max_loan_per_member != 1) {
        die('Maximum loan per member must be 1.');
    }

    $stmt = $pdo->prepare("INSERT INTO loan_products (product_type, product_code, product_name, start_range, end_range, service_charge, tenure, installment_type, processing_fee, late_fee, max_loan_per_member, allow_extra_payment, allow_multiple_disbursement) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_type, $product_code, $product_name, $start_range, $end_range, $service_charge, $tenure, $installment_type, $processing_fee, $late_fee, $max_loan_per_member, $allow_extra_payment, $allow_multiple_disbursement]);
    header('Location: ../admin/loan_product.php');
    exit;
}

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM loan_products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ../admin/loan_product.php');
    exit;
}

// Edit product logic can be added here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $product_type = $_POST['product_type'] ?? '';
    $product_code = $_POST['product_code'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $start_range = floatval($_POST['start_range'] ?? 0);
    $end_range = floatval($_POST['end_range'] ?? 0);
    $service_charge = floatval($_POST['service_charge'] ?? 0);
    $tenure = intval($_POST['tenure'] ?? 0);
    $installment_type = $_POST['installment_type'] ?? '';
    $processing_fee = floatval($_POST['processing_fee'] ?? 0);
    $late_fee = floatval($_POST['late_fee'] ?? 0);
    $max_loan_per_member = intval($_POST['max_loan_per_member'] ?? 1);
    $allow_extra_payment = $_POST['allow_extra_payment'] ?? '';
    $allow_multiple_disbursement = $_POST['allow_multiple_disbursement'] ?? '';

    // Validation
    if ($service_charge < 8 || $service_charge > 10) {
        die('Service charge must be between 8% and 10%.');
    }
    if ($tenure < 3 || $tenure > 12) {
        die('Tenure must be between 3 and 12 months.');
    }
    if ($processing_fee < 0 || $processing_fee > 1) {
        die('Processing fee must be between 0% and 1%.');
    }
    if ($max_loan_per_member != 1) {
        die('Maximum loan per member must be 1.');
    }

    $stmt = $pdo->prepare("UPDATE loan_products SET product_type=?, product_code=?, product_name=?, start_range=?, end_range=?, service_charge=?, tenure=?, installment_type=?, processing_fee=?, late_fee=?, max_loan_per_member=?, allow_extra_payment=?, allow_multiple_disbursement=? WHERE id=?");
    $stmt->execute([$product_type, $product_code, $product_name, $start_range, $end_range, $service_charge, $tenure, $installment_type, $processing_fee, $late_fee, $max_loan_per_member, $allow_extra_payment, $allow_multiple_disbursement, $id]);
    header('Location: ../admin/loan_product.php');
    exit;
}
