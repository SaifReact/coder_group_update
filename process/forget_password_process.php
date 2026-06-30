<?php
include_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$nid         = trim($_POST['nid']          ?? '');
$dob         = trim($_POST['dob']          ?? '');
$new_password= $_POST['new_password']      ?? '';

if (!$nid || !$dob) {
    echo json_encode(['success' => false, 'message' => 'NID এবং জন্ম তারিখ প্রয়োজন। (NID and Date of Birth are required.)']);
    exit;
}

if (!$new_password || strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে। (Password must be at least 6 characters.)']);
    exit;
}

try {
    // Verify identity: NID + DOB
    $stmt = $pdo->prepare("SELECT id, member_code FROM members_info WHERE nid = ? AND dob = ? LIMIT 1");
    $stmt->execute([$nid, $dob]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        echo json_encode(['success' => false, 'message' => '❌ NID বা জন্ম তারিখ সঠিক নয়। কোনো সদস্য পাওয়া যায়নি। (No member found with this NID and Date of Birth.)']);
        exit;
    }

    $member_id   = $member['id'];
    $member_code = $member['member_code'];

    // Update password with user's chosen password
    $upd = $pdo->prepare(
        "UPDATE user_login SET password = MD5(?), re_password = ? WHERE member_id = ? AND member_code = ?"
    );
    $upd->execute([$new_password, $new_password, $member_id, $member_code]);

    if ($upd->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => '❌ এই সদস্যের কোনো লগইন অ্যাকাউন্ট পাওয়া যায়নি। (No login account found for this member.)']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => '✅ পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে!',
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
