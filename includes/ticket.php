<?php

include_once __DIR__ . '/../config/config.php';

// Fetch counts from members_info table
$stmt = $pdo->query("SELECT 
    (SELECT registration_no FROM setup) AS registration,
    '২০২৩' AS establish,
    (SELECT COUNT(*) 
     FROM members_info a 
     JOIN user_login b ON a.id = b.member_id 
     WHERE b.status = 'A') AS members,
    (SELECT COALESCE(SUM(a.no_share), 0) 
     FROM member_share a 
     JOIN user_login b ON a.member_id = b.member_id 
     WHERE b.status = 'A') AS shares
");
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

function convertToBangla($number) {
    $banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($englishDigits, $banglaDigits, $number);
}
?>

<div class="container">
    <div class="row">
        <!-- Total Members -->
        <div class="col-md-3">
            <div class="card text-center custom-card">
                <div class="card-body">
                    <h4 class="card-title"><b>নিবন্ধন নং</b></h4>
                    <p class="card-text display-10" style="padding-top:23px; color:#FF0000; font-weight:bold"><?= htmlspecialchars($counts['registration'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Male Members -->
        <div class="col-md-3"> 
            <div class="card text-center custom-card">
                <div class="card-body">
                    <h4 class="card-title"><b>স্থাপিত সন</b></h4>
                    <p class="card-text display-6"><?= htmlspecialchars($counts['establish'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Female Members -->
        <div class="col-md-3">
            <div class="card text-center custom-card">
                <div class="card-body">
                    <h4 class="card-title"><b>সদস্য সংখ্যা</b></h4>
                    <p class="card-text display-6"><?= htmlspecialchars(convertToBangla($counts['members']) ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Married Members -->
        <div class="col-md-3">
            <div class="card text-center custom-card">
                <div class="card-body">
                    <h4 class="card-title"><b>শেয়ার সংখ্যা</b></h4>
                    <p class="card-text display-6"><?= htmlspecialchars(convertToBangla($counts['shares']) ?? 0); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>