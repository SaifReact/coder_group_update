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
        $project_name_bn = $_POST['project_name_bn'] ?? '';
        $project_name_en = $_POST['project_name_en'] ?? '';
        $about_project = $_POST['about_project'] ?? '';
        $project_value = $_POST['project_value'] ?? 0;
        $project_share = $_POST['project_share'] ?? 0;

        $stmt = $pdo->prepare("INSERT INTO project
            (project_name_bn, project_name_en, about_project, project_value, project_share) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$project_name_bn, $project_name_en, $about_project, $project_value, $project_share]);

        $_SESSION['success_msg'] = "✅ Project Added Successfully..! (সফলভাবে যোগ করা হয়েছে..!)";
        header("Location: ../admin/project.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $project_name_bn = $_POST['edit_project_name_bn'] ?? '';
        $project_name_en = $_POST['edit_project_name_en'] ?? '';
        $about_project = $_POST['edit_about_project'] ?? '';
        $project_value = $_POST['edit_project_value'] ?? 0;
        $project_share = $_POST['edit_project_share'] ?? 0;

        $stmt = $pdo->prepare("UPDATE project
            SET project_name_bn = ?, project_name_en = ?, about_project = ?, project_value = ?, project_share = ? 
            WHERE id = ?");

        $stmt->execute([$project_name_bn, $project_name_en, $about_project, $project_value, $project_share, $id]);

        $_SESSION['success_msg'] = "✅ Project Updated Successfully..! (সফলভাবে হালনাগাদ করা হলো..!)";
        header("Location: ../admin/project.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;   // ✅ FIXED
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM project WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ Project Deleted Successfully..! (সফলভাবে মুছে ফেলা হয়েছে..!)";
        }
        header("Location: ../admin/project.php");
        exit;
    }

    // Invalid action
    $_SESSION['error'] = "Error: Invalid action.";
    header("Location: ../admin/project.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: ../admin/project.php");
    exit;
}
