<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Check action
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'insert') {
        $service_name_bn = $_POST['service_name_bn'] ?? '';
        $service_name_en = $_POST['service_name_en'] ?? '';
        $about_service = $_POST['about_service'] ?? '';
        $icon = $_POST['icon'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO services 
            (service_name_bn, service_name_en, about_service, icon) 
            VALUES (?, ?, ?, ?)");
        $stmt->execute([$service_name_bn, $service_name_en, $about_service, $icon]);

        $_SESSION['success_msg'] = "✅ Service Added Successfully..! (সফলভাবে যোগ করা হয়েছে..!)";
        header("Location: ../admin/service.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $service_name_bn = $_POST['edit_service_name_bn'] ?? '';
        $service_name_en = $_POST['edit_service_name_en'] ?? '';
        $about_service = $_POST['edit_about_service'] ?? '';
        $icon = $_POST['edit_icon'] ?? '';

        $stmt = $pdo->prepare("UPDATE services 
            SET service_name_bn = ?, service_name_en = ?, about_service = ?, icon = ? 
            WHERE id = ?");

        $stmt->execute([$service_name_bn, $service_name_en, $about_service, $icon, $id]);

        $_SESSION['success_msg'] = "✅ Service Updated Successfully..! (সফলভাবে হালনাগাদ করা হলো..!)";
        header("Location: ../admin/service.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;   // ✅ FIXED
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ Service Deleted Successfully..! (সফলভাবে মুছে ফেলা হয়েছে..!)";
        }
        header("Location: ../admin/service.php");
        exit;
    }

    // Invalid action
    $_SESSION['error'] = "Error: Invalid action.";
    header("Location: ../admin/service.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: ../admin/service.php");
    exit;
}
