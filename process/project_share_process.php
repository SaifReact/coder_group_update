<?php
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$project_id = (int)($_GET['project_id'] ?? 0);
if (!$project_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
    exit;
}

try {
    // Project info
    $stmt = $pdo->prepare("SELECT project_name_bn, project_name_en, project_share, per_share_value FROM project WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }

    // Allocated shares count from project_share table
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM project_share WHERE project_id = ? AND status ='A'");
    $stmt2->execute([$project_id]);
    $allocated = (int)$stmt2->fetchColumn();

    // Total samity shares (sum across all members from member_share)
    $total_samity = (int)$pdo->query("SELECT COALESCE(SUM(samity_share),0) FROM member_share")->fetchColumn();

    $total_shares = (int)$project['project_share'];
    $standby      = max(0, $total_shares - $allocated);

    // Member-wise breakdown for this project
    $stmt3 = $pdo->prepare("
        SELECT m.name_bn, m.member_code, ms.samity_share,
               ps.project_share AS project_shares
        FROM member_project ps
        JOIN members_info m ON m.id = ps.member_id
        LEFT JOIN member_share ms ON ms.member_id = ps.member_id
        WHERE ps.project_id = ?
        AND ps.status = 'A'
        GROUP BY ps.member_id
        ORDER BY m.id ASC
    ");
    $stmt3->execute([$project_id]);
    $members = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'       => true,
        'project'       => $project,
        'total_shares'  => $total_shares,
        'allocated'     => $allocated,
        'standby'       => $standby,
        'samity_share'  => $total_samity,
        'members'       => $members,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
