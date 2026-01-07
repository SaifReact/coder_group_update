<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'insert') {
        $exp_date = $_POST['exp_date'] ?? '';
        $exp_cat = $_POST['exp_cat'] ?? 0;
        $amount = $_POST['amount'] ?? 0;
        $reference = $_POST['reference'] ?? '';
        $note = $_POST['note'] ?? '';
        $status = $_POST['status'] ?? 'A';
        $glacc = $_POST['gl_acc'] ?? 0;
        $exp_slip = '';
        if (!empty($_FILES['exp_slip']['name'])) {
            $targetDir = '../expenses/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['exp_slip']['name']);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['exp_slip']['tmp_name'], $targetFile)) {
                $exp_slip = $fileName;
            }
        }
        if (!empty($exp_date) && $exp_cat && $amount) {
            $stmt = $pdo->prepare("INSERT INTO expenses (exp_date, exp_cat, amount, reference, note, exp_slip, status, glac_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$exp_date, $exp_cat, $amount, $reference, $note, $exp_slip, $status, $glacc]);
            $_SESSION['success_msg'] = "✅ Expense Added Successfully..! (সফলভাবে যোগ করা হয়েছে..!)";
        } else {
            $_SESSION['error_msg'] = 'Date, Category, and Amount are required!';
        }
        header("Location: ../admin/expenses.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $exp_date = $_POST['edit_exp_date'] ?? '';
        $exp_cat = $_POST['edit_exp_cat'] ?? 0;
        $amount = $_POST['edit_amount'] ?? 0;
        $reference = $_POST['edit_reference'] ?? '';
        $note = $_POST['edit_note'] ?? '';
        $status = $_POST['edit_status'] ?? 'A';
        $glacc = $_POST['edit_gl_acc'] ?? 0;
        $exp_slip = '';
        if (!empty($_FILES['edit_exp_slip']['name'])) {
            $targetDir = '../expenses/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['edit_exp_slip']['name']);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['edit_exp_slip']['tmp_name'], $targetFile)) {
                $exp_slip = $fileName;
            }
        }
        if ($id && !empty($exp_date) && $exp_cat && $amount) {
            if ($exp_slip) {
                $stmt = $pdo->prepare("UPDATE expenses SET exp_date=?, exp_cat=?, amount=?, reference=?, note=?, exp_slip=?, status=?, glac_id=? WHERE id=?");
                $stmt->execute([$exp_date, $exp_cat, $amount, $reference, $note, $exp_slip, $status, $glacc, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE expenses SET exp_date=?, exp_cat=?, amount=?, reference=?, note=?, status=?, glac_id=? WHERE id=?");
                $stmt->execute([$exp_date, $exp_cat, $amount, $reference, $note, $status, $glacc, $id]);
            }
            $_SESSION['success_msg'] = "✅ Expense Updated Successfully..! (সফলভাবে হালনাগাদ করা হলো..!)";
        } else {
            $_SESSION['error_msg'] = 'Date, Category, and Amount are required!';
        }
        header("Location: ../admin/expenses.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ Expense Deleted Successfully..! (সফলভাবে মুছে ফেলা হয়েছে..!)";
        }
        header("Location: ../admin/expenses.php");
        exit;
    }

    $_SESSION['error_msg'] = "Error: Invalid action.";
    header("Location: ../admin/expenses.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: ../admin/expenses.php");
    exit;
}
