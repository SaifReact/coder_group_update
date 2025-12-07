<?php
include_once __DIR__ . '/../config/config.php';
session_start();

try {
    $member_id = $_POST['member_id'];
    $member_code = $_POST['member_code'];
    $addShare = $_POST['addShare'] ?? 0;  // Ensure there's a default value in case it's not provided

    // Fetch the existing no_share value from the database
    $stmt = $pdo->prepare("SELECT no_share FROM member_share WHERE member_id = ? AND member_code = ?");
    $stmt->execute([$member_id, $member_code]);
    $share = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($share) {
        // Calculate the new no_share after adding addShare
        $no_share = $share['no_share'] + $addShare;

        // Subtract 2 from the new no_share
        $extra_share = $no_share - 2;

        // Update the member_share table with new values
        $stmtUpdate = $pdo->prepare("UPDATE member_share SET no_share = ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$no_share, $extra_share, $member_id, $member_code]);

        // Set success message in session
        $_SESSION['success_msg'] = '✅ আপনার শেয়ার হালনাগাদ করা হয়েছে (Your Share Update successfully)';
        header('Location: ../users/share.php'); // Redirect to the appropriate page
        exit;
    } else {
        throw new Exception('Share not found.');
    }
} catch (Exception $e) {
    // Set error message in session
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
    header('Location: ../users/share.php'); // Redirect back to the share page
    exit;
}
