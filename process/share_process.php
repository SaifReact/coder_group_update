<?php
// process/share_add.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $member_code = $_SESSION['member_code'];
    $type = isset($_POST['share_type']) ? $_POST['share_type'] : '';
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $no_share = isset($_POST['share_amount']) ? intval($_POST['share_amount']) : 0;
    $status = 'I'; // Default active
    $errors = [];

    if (!$type) {
        $errors[] = 'শেয়ার টাইপ নির্বাচন করুন (Select share type)';
    }
    if ($no_share <= 0) {
        $errors[] = 'শেয়ার সংখ্যা সঠিকভাবে দিন (Enter valid share amount)';
    }
    if ($type === 'project' && !$project_id) {
        $errors[] = 'প্রকল্প নির্বাচন করুন (Select project)';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO share (member_id, member_code, type, project_id, no_share, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$member_id, $member_code, $type, $project_id, $no_share, $status]);
            $_SESSION['success_msg'] = 'শেয়ার সফলভাবে যোগ হয়েছে (Share added successfully)';
        } catch (Exception $e) {
            $_SESSION['share_error'] = 'DB Error: ' . $e->getMessage();
        }
        header('Location: ../users/add_share.php');
        exit;
    } else {
        $_SESSION['share_error'] = implode('<br>', $errors);
        header('Location: ../users/add_share.php');
        exit;
    }
}


// UPDATE share
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $type = isset($_POST['share_type']) ? $_POST['share_type'] : '';
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $no_share = isset($_POST['share_amount']) ? intval($_POST['share_amount']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'I';
    $errors = [];

    if ($id <= 0) {
        $errors[] = 'Invalid share ID.';
    }
    if (!$type) {
        $errors[] = 'শেয়ার টাইপ নির্বাচন করুন (Select share type)';
    }
    if ($no_share <= 0) {
        $errors[] = 'শেয়ার সংখ্যা সঠিকভাবে দিন (Enter valid share amount)';
    }
    if ($type === 'project' && !$project_id) {
        $errors[] = 'প্রকল্প নির্বাচন করুন (Select project)';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE share SET type = ?, project_id = ?, no_share = ?, status = ? WHERE id = ?");
            $stmt->execute([$type, $project_id, $no_share, $status, $id]);
            $_SESSION['success_msg'] = 'শেয়ার সফলভাবে আপডেট হয়েছে (Share updated successfully)';
        } catch (Exception $e) {
            $_SESSION['share_error'] = 'DB Error: ' . $e->getMessage();
        }
        header('Location: ../users/add_share.php');
        exit;
    } else {
        $_SESSION['share_error'] = implode('<br>', $errors);
        header('Location: ../users/add_share.php');
        exit;
    }
}

// DELETE share
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM share WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = 'শেয়ার সফলভাবে ডিলিট হয়েছে (Share deleted successfully)';
        } catch (Exception $e) {
            $_SESSION['share_error'] = 'DB Error: ' . $e->getMessage();
        }
    } else {
        $_SESSION['share_error'] = 'Invalid share ID.';
    }
    header('Location: ../users/add_share.php');
    exit;
}

