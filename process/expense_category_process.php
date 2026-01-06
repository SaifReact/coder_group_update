<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: ../login.php");
    exit;
}
include_once __DIR__ . '/../config/config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'insert') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = 'A';
        if (!empty($name)) {
            $stmt = $pdo->prepare("INSERT INTO expense_category (name, description, status) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $status]);
            $_SESSION['success_msg'] = "✅ Category Added Successfully..! (সফলভাবে যোগ করা হয়েছে..!)";
        } else {
            $_SESSION['error_msg'] = 'Name is required!';
        }
        header("Location: ../admin/expense_category.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['edit_name'] ?? '';
        $description = $_POST['edit_description'] ?? '';
        $status = $_POST['edit_status'] ?? 'A';
        if ($id && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE expense_category SET name = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $description, $status, $id]);
            $_SESSION['success_msg'] = "✅ Category Updated Successfully..! (সফলভাবে হালনাগাদ করা হলো..!)";
        } else {
            $_SESSION['error_msg'] = 'Name is required!';
        }
        header("Location: ../admin/expense_category.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM expense_category WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ Category Deleted Successfully..! (সফলভাবে মুছে ফেলা হয়েছে..!)";
        }
        header("Location: ../admin/expense_category.php");
        exit;
    }

    $_SESSION['error_msg'] = "Error: Invalid action.";
    header("Location: ../admin/expense_category.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: ../admin/expense_category.php");
    exit;
}
