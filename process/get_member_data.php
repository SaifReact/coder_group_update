<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_GET['member_id']) || !isset($_GET['member_code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$member_id = $_GET['member_id'];
$member_code = $_GET['member_code'];
$project_id = $_GET['project_id'] ?? 0;

$response = [
    'success' => false,
    'admission_paid' => false,
    'samity_share_amt' => 0,
    'sundry_samity_share' => 0,
    'projects' => []
];

// Fetch member share data (using LEFT JOIN so data shows even if no project exists)
$stmt1 = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
$stmt1->execute([$member_id, $member_code]);
if ($row1 = $stmt1->fetch()) {
    $response['success'] = true;
    $response['no_share'] = (float)$row1['no_share'];
    $response['samity_share'] = (float)$row1['samity_share'];
    $response['samity_share_amt'] = (float)$row1['samity_share_amt'];
    $response['extra_share'] = isset($row1['extra_share']) ? (float)$row1['extra_share'] : 0;
    $response['admission_paid'] = isset($row1['admission_fee']) && (float)$row1['admission_fee'] > 0;
    $response['late_assign'] = isset($row1['late_assign']) ? $row1['late_assign'] : '';
    $response['late_fee'] = isset($row1['late_fee']) ? (float)$row1['late_fee'] : 0;
    $response['sundry_samity_share'] = isset($row1['sundry_samity_share']) ? (float)$row1['sundry_samity_share'] : 0;
    $response['install_advance'] = isset($row1['install_advance']) ? (float)$row1['install_advance'] : 0;
}

// Fetch all projects (not filtered by member_id)
$stmtProj = $pdo->prepare("SELECT id, project_name_bn, project_name_en FROM project WHERE id > 1 ORDER BY id ASC");
$stmtProj->execute();
while($proj = $stmtProj->fetch(PDO::FETCH_ASSOC)) {
    $response['projects'][] = [
        'project_id' => $proj['id'],
        'project_name_bn' => $proj['project_name_bn'],
        'project_name_en' => $proj['project_name_en']
    ];
}


// Fetch already paid monthly payments
$response['payments'] = [];
$stmtPayment = $pdo->prepare("SELECT payment_method, payment_year, amount FROM member_payments WHERE member_id = ? AND member_code = ?");
$stmtPayment->execute([$member_id, $member_code]);
while($rowpay = $stmtPayment->fetch()) {
    $response['payments'][] = [
        'payment_method' => $rowpay['payment_method'],
        'payment_year' => $rowpay['payment_year'],
        'amount' => $rowpay['amount']
    ];
}

$response['memProject'] = [];
if ($project_id > 0) {
    // If project_id is provided, fetch only that project
    $stmtMemPro = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = ?");
    $stmtMemPro->execute([$member_id, $member_code, $project_id]);
} else {
    // If no project_id, fetch all projects for the member
    $stmtMemPro = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ? AND member_code = ?");
    $stmtMemPro->execute([$member_id, $member_code]);
}

while($memProject = $stmtMemPro->fetch()) {
    $response['memProject'][] = [
        'project_id' => $memProject['project_id'],
        'project_share' => $memProject['project_share'],
        'share_amount' => $memProject['share_amount'],
        'paid_amount' => $memProject['paid_amount'],
        'sundry_amount' => $memProject['sundry_amount']
    ];
}

echo json_encode($response);
