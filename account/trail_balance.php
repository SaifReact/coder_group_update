<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Fetch all GL summary data with type
$stmt = $pdo->query("SELECT g.glac_code, g.glac_name, g.glac_type, s.debit_amount, s.credit_amount FROM gl_summary s JOIN glac_mst g ON s.glac_id = g.id ORDER BY g.glac_type ASC, g.glac_code ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group mapping
$typeMap = [
    '1' => ['title' => 'সম্পদ (Assets)', 'examples' => 'Cash, Bank, Receivable'],
    '2' => ['title' => 'দায় (Liabilities)', 'examples' => 'Payable'],
    '3' => ['title' => 'আয় (Income)', 'examples' => 'Sales'],
    '4' => ['title' => 'ব্যয় (Expenses)', 'examples' => 'Rent, Salary'],
    '5' => ['title' => 'মূলধন (Capital)', 'examples' => 'Capital'],
];

// Group rows by type
$grouped = [];
foreach ($rows as $row) {
    $type = $row['glac_type'] ?? '0';
    $grouped[$type][] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance Report</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Trial Balance Report</h2>
    <?php 
    $grand_debit = 0;
    $grand_credit = 0;
    foreach ($typeMap as $type => $info):
        if (empty($grouped[$type])) continue;
        $type_debit = 0;
        $type_credit = 0;
    ?>
    <div class="mb-4">
        <h4 class="bg-primary text-white p-2 rounded"> <?= $info['title'] ?> <span class="text-warning" style="font-size:0.9em;">→ <?= $info['examples'] ?></span></h4>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>GL Code</th>
                    <th>GL Name</th>
                    <th>Debit Amount</th>
                    <th>Credit Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped[$type] as $row): 
                $type_debit += $row['debit_amount'];
                $type_credit += $row['credit_amount'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['glac_code']) ?></td>
                    <td><?= htmlspecialchars($row['glac_name']) ?></td>
                    <td class="text-end"><?= number_format($row['debit_amount'], 2) ?></td>
                    <td class="text-end"><?= number_format($row['credit_amount'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="2" class="text-end">Subtotal</td>
                    <td class="text-end"><?= number_format($type_debit, 2) ?></td>
                    <td class="text-end"><?= number_format($type_credit, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php 
        $grand_debit += $type_debit;
        $grand_credit += $type_credit;
    endforeach; ?>
    <div class="alert alert-success fw-bold">
        Grand Total: <span class="float-end">Debit: <?= number_format($grand_debit, 2) ?> | Credit: <?= number_format($grand_credit, 2) ?></span>
    </div>
</div>
</body>
</html>
