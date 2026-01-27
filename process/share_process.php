<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Check action
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'insert') {
        $member_id = $_POST['member_id'] ?? '';
        $member_code = $_POST['member_code'] ?? '';
        $share_type = $_POST['share_type'] ?? ($_POST['type'] ?? '');
        $project_id = $_POST['project_id'] ?? 1;
        $no_share = intval($_POST['share_amount'] ?? $_POST['no_share'] ?? 0);
        $status = $_POST['status'] ?? 'I';

        // Normalize share_type for 'samity' (if needed)
        $share_type = trim(strtolower($share_type));
        if ($share_type === 'samity') {
            $share_type = 'samity'; // keep as is, or map to DB value if needed
        }

        // Prevent insert if type is empty
        if ($share_type === '') {
            $_SESSION['error'] = "Error: শেয়ার টাইপ নির্বাচন করুন (Please select share type)";
            header("Location: ../users/add_share.php");
            exit;
        }

        // Check if a share already exists for this member, project, and status='I'
        $stmt = $pdo->prepare("SELECT id, no_share FROM share WHERE member_id = ? AND project_id = ? AND status = 'I' AND type = ?");
        $stmt->execute([$member_id, $project_id, $share_type]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            // Update the existing share's no_share
            $new_no_share = $existing['no_share'] + $no_share;
            $stmt = $pdo->prepare("UPDATE share SET no_share = ? WHERE id = ?");
            $stmt->execute([$new_no_share, $existing['id']]);
            $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে হালনাগাদ হয়েছে (Share updated successfully)";
        } else {
            // Insert new share
            $stmt = $pdo->prepare("INSERT INTO share (member_id, member_code, type, project_id, no_share, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$member_id, $member_code, $share_type, $project_id, $no_share, $status]);
            $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে যোগ হয়েছে (Share added successfully)";
        }
        header("Location: ../users/add_share.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $share_type = $_POST['type'] ?? '';
        $project_id = $_POST['project_id'] ?? 0;
        $no_share = intval($_POST['no_share'] ?? 0);
        $status = $_POST['status'] ?? 'I';

        $stmt = $pdo->prepare("UPDATE share SET type=?, project_id=?, no_share=?, status=? WHERE id=?");
        $stmt->execute([$share_type, $project_id, $no_share, $status, $id]);
        $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে হালনাগাদ হয়েছে (Share updated successfully)";
        header("Location: ../users/add_share.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (!$id) throw new Exception('ID missing');
        $stmt = $pdo->prepare("DELETE FROM share WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = "✅ শেয়ার সফলভাবে মুছে ফেলা হয়েছে (Share deleted successfully)";
        header("Location: ../users/add_share.php");
        exit;
    }

    // Invalid action
    $_SESSION['error'] = "Error: Invalid action.";
    header("Location: ../users/add_share.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: ../users/add_share.php");
    exit;
}
