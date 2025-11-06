<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$action = $_POST['action'] ?? '';

if ($action === 'insert') {
    $member_id = $_POST['member_id'];
    $designation = $_POST['designation'];
    $facebook = $_POST['facebook'];
    $linkedin = $_POST['linkedin'];
    $role = isset($_POST['role']) && $_POST['role'] === 'Entrepreneur' ? 'Entrepreneur' : 'Committee Member';

    // Fetch member_code from members_info table
    $stmt = $pdo->prepare("SELECT member_code FROM members_info WHERE id = ?");
    $stmt->execute([$member_id]);
    $member_code = $stmt->fetchColumn();

    if (!$member_code) {
        $_SESSION['error_msg'] = "Invalid member selected!";
        header('Location: ../admin/committee.php');
        exit;
    }

    // Insert into committee_member table
    $stmt = $pdo->prepare("INSERT INTO committee_member (member_id, member_code, committee_role_id, fb, li, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$member_id, $member_code, $designation, $facebook, $linkedin, $role]);

    $_SESSION['success_msg'] = "✅ Committee member added successfully!";
    header('Location: ../admin/committee.php');
    exit;

} elseif ($action === 'update') {
    $id = $_POST['id'];
    $member_id = $_POST['edit_member_id'];
    $designation = $_POST['edit_designation'];
    $facebook = $_POST['edit_facebook'];
    $linkedin = $_POST['edit_linkedin'];
    $role = isset($_POST['edit_role']) && $_POST['edit_role'] === 'Entrepreneur' ? 'Entrepreneur' : 'Committee Member';

    // Fetch member_code from members_info table
    $stmt = $pdo->prepare("SELECT member_code FROM members_info WHERE id = ?");
    $stmt->execute([$member_id]);
    $member_code = $stmt->fetchColumn();

    if (!$member_code) {
        $_SESSION['error_msg'] = "Invalid member selected!";
        header('Location: ../admin/committee.php');
        exit;
    }

    // Update committee_member table
    $stmt = $pdo->prepare("UPDATE committee_member SET member_id = ?, member_code = ?, committee_role_id = ?, fb = ?, li = ?, role = ? WHERE id = ?");
    $stmt->execute([$member_id, $member_code, $designation, $facebook, $linkedin, $role, $id]);

    $_SESSION['success_msg'] = "✅ Committee member updated successfully!";
    header('Location: ../admin/committee.php');
    exit;

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM committee_member WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = "✅ Committee member deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Invalid request!";
    }

    header("Location: ../admin/committee.php");
    exit;

} else {
    $_SESSION['error_msg'] = "Invalid action!";
    header("Location: ../admin/committee.php");
    exit;
}
