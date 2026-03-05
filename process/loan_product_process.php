<?php
session_start();
include_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

// Insert new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'insert') {
    $product_name = $_POST['product_name'] ?? 'Nano Loan';
    $start_range = floatval($_POST['start_range'] ?? 1000);
    $end_range = floatval($_POST['end_range'] ?? 10000);
    $service_charge = floatval($_POST['service_charge'] ?? 8);
    $tenure = intval($_POST['tenure'] ?? 3);
    $installment_type = 'Monthly';
    $processing_fee = floatval($_POST['processing_fee'] ?? 0);
    $late_fee = floatval($_POST['late_fee'] ?? 0);
    $max_loan_per_member = 1;

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

    $stmt = $pdo->prepare("INSERT INTO loan_products (product_name, start_range, end_range, service_charge, tenure, installment_type, processing_fee, late_fee, max_loan_per_member) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_name, $start_range, $end_range, $service_charge, $tenure, $installment_type, $processing_fee, $late_fee, $max_loan_per_member]);
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
    $service_charge = floatval($_POST['service_charge'] ?? 8);
    $tenure = intval($_POST['tenure'] ?? 3);
    $processing_fee = floatval($_POST['processing_fee'] ?? 0);
    $late_fee = floatval($_POST['late_fee'] ?? 0);

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

    $stmt = $pdo->prepare("UPDATE loan_products SET service_charge=?, tenure=?, processing_fee=?, late_fee=? WHERE id=?");
    $stmt->execute([$service_charge, $tenure, $processing_fee, $late_fee, $id]);
    header('Location: ../admin/loan_product.php');
    exit;
}
