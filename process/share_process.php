<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'edit' || $action === 'insert') {
            // Insert new share
            $member_id = $_POST['member_id'] ?? '';
            $member_code = $_POST['member_code'] ?? '';
            $share_type = $_POST['share_type'] ?? ($_POST['type'] ?? '');
            $project_id = $_POST['project_id'] ?? 0;
            $no_share = intval($_POST['share_amount'] ?? $_POST['no_share'] ?? 0);
            $status = $_POST['status'] ?? 'I';
            if (!$share_type || $no_share <= 0) {
                throw new Exception('শেয়ার টাইপ ও সংখ্যা দিন (Provide share type and amount)');
            }
            if ($share_type === 'samity') {
                $project_id = 0;
            }
            $stmt = $pdo->prepare("INSERT INTO share (member_id, member_code, type, project_id, no_share, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$member_id, $member_code, $share_type, $project_id, $no_share, $status]);
            $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে যোগ হয়েছে (Share added successfully)";
            header("Location: ../users/add_share.php");
            exit;
        } elseif ($action === 'update') {
            // Update share
            $id = $_POST['id'] ?? '';
            $share_type = $_POST['type'] ?? '';
            $project_id = $_POST['project_id'] ?? 0;
            $no_share = intval($_POST['no_share'] ?? 0);
            $status = $_POST['status'] ?? 'I';
            if (!$id || !$share_type || $no_share <= 0) {
                throw new Exception('সঠিক তথ্য দিন (Provide valid data)');
            }
            if ($share_type === 'samity') {
                $project_id = 0;
            }
            $stmt = $pdo->prepare("UPDATE share SET type=?, project_id=?, no_share=?, status=? WHERE id=?");
            $stmt->execute([$share_type, $project_id, $no_share, $status, $id]);
            $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে হালনাগাদ হয়েছে (Share updated successfully)";
            header("Location: ../users/add_share.php");
            exit;
        } elseif ($action === 'delete') {
            // Delete share
            $id = $_POST['id'] ?? '';
            if (!$id) throw new Exception('ID missing');
            $stmt = $pdo->prepare("DELETE FROM share WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে মুছে ফেলা হয়েছে (Share deleted successfully)";
            header("Location: ../users/add_share.php");
            exit;
        } else {
            throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        header("Location: ../users/add_share.php");
        exit;
    }
} else {
    $_SESSION['error_msg'] = 'Invalid request';
    header("Location: ../users/add_share.php");
    exit;
}
