<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

$tran_types = $_POST['fee_type'] ?? [];
$tran_type_names = $_POST['fee_type_name'] ?? [];
$gls = $_POST['gl'] ?? [];
$contras = $_POST['contra'] ?? [];
$types = $_POST['type'] ?? [];
$ids = $_POST['row_id'] ?? [];
$created_by = $_SESSION['user_id'];
$now = date('Y-m-d H:i:s');


// Delete all previous mappings (full replace)
$pdo->exec("DELETE FROM gl_mapping");

// Insert all submitted rows
foreach ($tran_types as $i => $tran_type) {
    $tran_type_name = $tran_type_names[$i] ?? '';
    $glac_id = $gls[$i] ?? null;
    $contra_glac_id = $contras[$i] ?? null;
    $is_active = (isset($types[$i]) && $types[$i] == 'সক্রিয়') ? 1 : 0;
    $stmt = $pdo->prepare("INSERT INTO gl_mapping (tran_type, tran_type_name, glac_id, contra_glac_id, is_active, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$tran_type, $tran_type_name, $glac_id, $contra_glac_id, $is_active, $created_by, $now]);
}

$_SESSION['success_msg'] = 'GL Mapping saved successfully!';
header('Location: ../admin/gl_mapping.php');
exit;
