<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Fetch Asset, Liability, Capital (Owner’s Equity) from glac_mst (id 11 is parent)
$stmt = $pdo->query("SELECT g.id, g.glac_name, g.glac_type, g.glac_code, s.debit_amount, s.credit_amount FROM glac_mst g
    LEFT JOIN gl_summary s ON g.id = s.glac_id
    WHERE g.parent_id = 11 OR g.id = 11
    ORDER BY g.glac_type ASC, g.glac_code ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by glac_type
$grouped = [
    '1' => [], // Asset
    '2' => [], // Liability
    '5' => [], // Capital/Equity
];
foreach ($rows as $row) {
    $type = $row['glac_type'] ?? '';
    if (isset($grouped[$type])) {
        $grouped[$type][] = $row;
    }
}

$typeMap = [
    '1' => 'সম্পদ (Assets)',
    '2' => 'দায় (Liabilities)',
    '5' => 'মূলধন (Owner’s Equity)',
];

function bn($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Balance Sheet</h2>
    <div class="row">
        <?php foreach ($typeMap as $type => $title): ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white fw-bold"><?= $title ?></div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                        <?php 
                        $total = 0;
                        foreach ($grouped[$type] as $row): 
                            $balance = ($row['debit_amount'] ?? 0) - ($row['credit_amount'] ?? 0);
                            $total += $balance;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['glac_name']) ?> <span class="text-muted small"><?= htmlspecialchars($row['glac_code']) ?></span></td>
                                <td class="text-end">৳ <?= bn(number_format($balance, 2)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold bg-light">
                            <td>Total</td>
                            <td class="text-end">৳ <?= bn(number_format($total, 2)) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
