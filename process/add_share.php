<?php
include_once __DIR__ . '/../config/config.php';
session_start();

try {
    $project_id = $_POST['project_id'] ?? 0;
    $member_id = $_POST['member_id'];
    $member_code = $_POST['member_code'];
    $previousShare = $_POST['previousShare'] ?? 0;
    $buyingShare = $_POST['buyingShare'] ?? 0;

    // Validate project_id
    if (empty($project_id) || $project_id == 0) {
        throw new Exception('Please select a project.');
    }

    // Fetch the existing no_share value from the database
    $stmt = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = ?");
    $stmt->execute([$member_id, $member_code, $project_id]);
    $share = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch per_share_value from project table for this project_id
    $stmtProject = $pdo->prepare("SELECT per_share_value FROM project WHERE id = ?");
    $stmtProject->execute([$project_id]);
    $projectRow = $stmtProject->fetch(PDO::FETCH_ASSOC);
    $per_share_value = $projectRow ? (float)$projectRow['per_share_value'] : 0;

    if ($project_id == 1 && $buyingShare > 0) {
        $stmtUtils = $pdo->query("SELECT * FROM utils WHERE fee_type = 'samity_share' AND status = 'A' ORDER BY id ASC LIMIT 1");
        $stmtUtils = $stmtUtils->fetch(PDO::FETCH_ASSOC);
        $utilsper_share_value = $stmtUtils ? $stmtUtils['fee'] : 0;

        $stmtGetExtra = $pdo->prepare("SELECT samity_share, extra_share FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
        $stmtGetExtra->execute([$member_id, $member_code]);
        $rowExtra = $stmtGetExtra->fetch(PDO::FETCH_ASSOC);
        $currentExtra = isset($rowExtra['extra_share']) ? (int)$rowExtra['extra_share'] : 0;
        $currentSamity = isset($rowExtra['samity_share']) ? (int)$rowExtra['samity_share'] : 0;

        $stmtLastShare = $pdo->prepare("SELECT share_id FROM project_share WHERE project_id = 1 AND member_id = ? ORDER BY id DESC LIMIT 1");
        $stmtLastShare->execute([$member_id]);
        $lastShare = $stmtLastShare->fetch(PDO::FETCH_ASSOC);
        $startingNumber = 1;
        if ($lastShare && !empty($lastShare['share_id'])) {
            $lastThreeDigits = substr($lastShare['share_id'], -3);
            $startingNumber = intval($lastThreeDigits) + 1;
        }

        // Check if this is first time adding CPSSL shares to project_share table
        $stmtCheckExisting = $pdo->prepare("SELECT COUNT(*) as count FROM project_share WHERE project_id = 1 AND member_id = ?");
        $stmtCheckExisting->execute([$member_id]);
        $existingCount = $stmtCheckExisting->fetch(PDO::FETCH_ASSOC);
        $hasExistingShares = ($existingCount['count'] > 0);

        // Insert new share_id(s) into project_share table
        $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        
        // First time: add both current samity + buying shares
        // Second time onwards: add only buying shares
        if ($currentSamity && !$hasExistingShares) {
            $insertCount = (int)$buyingShare + $currentSamity;
        } else {
            $insertCount = (int)$buyingShare;
        }

        // Find member_project_id for this member and project_id=1
        $stmtProject = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND project_id = 1 ORDER BY id DESC LIMIT 1");
        $stmtProject->execute([$member_id]);
        $projectRow = $stmtProject->fetch(PDO::FETCH_ASSOC);
        $member_project_id = $projectRow ? $projectRow['id'] : null;
        
        if (!$member_project_id) {
            $stmtInsertProject = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmtInsertProject->execute([$member_id, $member_code, 1, 0, 0]);
            $member_project_id = $pdo->lastInsertId();
        } 

        for ($i = 0; $i < $insertCount; $i++) {
            $currentShareNumber = $startingNumber + $i;
            $share_id = 'samity' . $member_id . $member_project_id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsert->execute([$member_project_id, $member_id, $member_code, 1, $share_id]);
        }

        $newExtra = $currentExtra - $buyingShare;
        if ($newExtra < 0) $newExtra = 0;
        $sundry_samity_share = $insertCount * $utilsper_share_value;
        $stmtUpdate = $pdo->prepare("UPDATE member_share SET samity_share = samity_share + ?, sundry_samity_share = sundry_samity_share + ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$buyingShare, $sundry_samity_share, $newExtra, $member_id, $member_code]);

        $_SESSION['success_msg'] = '✅ আপনার CPSSL শেয়ার যোগ করা হয়েছে এবং ' . $buyingShare . ' টি শেয়ার সফলভাবে যোগ হয়েছে (Your CPSSL Shares Added Successfully)';
        header('Location: ../users/project_shares.php');
        exit;
       
    } else if ($project_id > 1 && $buyingShare > 0 && empty($share)) {   
    // For other projects
    $stmtGetPerShare = $pdo->prepare("SELECT per_share_value FROM project WHERE id = ? LIMIT 1");
    $stmtGetPerShare->execute([$project_id]);
    $rowPerShare = $stmtGetPerShare->fetch(PDO::FETCH_ASSOC);
    $per_share_value = isset($rowPerShare['per_share_value']) ? (float)$rowPerShare['per_share_value'] : 0;

    // get samity share
    $stmtGetSamityShare = $pdo->prepare("SELECT no_share, extra_share, samity_share FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
    $stmtGetSamityShare->execute([$member_id, $member_code]);
    $rowSamityShare = $stmtGetSamityShare->fetch(PDO::FETCH_ASSOC);
    $samity_share = isset($rowSamityShare['samity_share']) ? (int)$rowSamityShare['samity_share'] : 0;
    $currentExtra = isset($rowSamityShare['extra_share']) ? (int)$rowSamityShare['extra_share'] : 0;
    $currentNoShare = isset($rowSamityShare['no_share']) ? (int)$rowSamityShare['no_share'] : 0;

    $share_amount = $buyingShare * $per_share_value;

    // Insert into member_project for the selected project
    $stmtInsert = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtInsert->execute([$member_id, $member_code, $project_id, $buyingShare, $share_amount]);
    $member_project_id = $pdo->lastInsertId();

    // Check if this member already has CPSSL shares in project_share table (project_id = 1)
    $stmtCheckCPSSL = $pdo->prepare("SELECT COUNT(*) as count FROM project_share WHERE member_id = ? AND member_code = ? AND project_id = 1");
    $stmtCheckCPSSL->execute([$member_id, $member_code]);
    $cpsslCount = $stmtCheckCPSSL->fetch(PDO::FETCH_ASSOC);
    $hasCPSSLShares = ($cpsslCount['count'] > 0);

    // If samity_share exists, handle the samity shares insertion
    if ($samity_share > 0) {
        // Check if the member has no `project_id = 1` in member_project
        $stmtFindSamityProject = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND project_id = 1 ORDER BY id DESC LIMIT 1");
        $stmtFindSamityProject->execute([$member_id]);
        $samity_member_project = $stmtFindSamityProject->fetch(PDO::FETCH_ASSOC);
        
        if (!$samity_member_project) {
            // If no such entry exists, create a new row with project_id = 1
            $stmtInsertSamityProject = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmtInsertSamityProject->execute([$member_id, $member_code, 1, 0, 0]);
            $samity_member_project_id = $pdo->lastInsertId();
        } else {
            $samity_member_project_id = $samity_member_project['id'];
        }

        // Insert samity share records into project_share
        $stmtInsertShare = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

        for ($i = 0; $i < $samity_share; $i++) {
            $samityShareNumber = str_pad(($i + 1), 2, '0', STR_PAD_LEFT);
            $share_id = 'samity' . $member_id . $samity_member_project_id . $samityShareNumber;
            $stmtInsertShare->execute([$samity_member_project_id, $member_id, $member_code, 1, $share_id]);
        }
    }

    // Insert project share for the selected project (for project_id > 1)
    if ($buyingShare > 0) {
        $startingNumber = 1;
        for ($i = 0; $i < $buyingShare; $i++) {
            $currentShareNumber = $startingNumber + $i;
            $share_id = 'share' . $member_id . $member_project_id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsertShare->execute([$member_project_id, $member_id, $member_code, $project_id, $share_id]);
        }
    }

    // Update `no_share` and `extra_share` in `member_share` based on the current values and buyingShare
    if ($currentExtra == 0) {
        $newNoShare = $currentNoShare + $buyingShare;
    } else {
        $newNoShare = $currentNoShare; // Keep current no_share value unchanged
    }

    $newExtra = $currentExtra - $buyingShare;
    if ($newExtra < 0) $newExtra = 0;

    // Update member share table
    $stmtUpdate = $pdo->prepare("UPDATE member_share SET no_share = ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
    $stmtUpdate->execute([$newNoShare, $newExtra, $member_id, $member_code]);

    $_SESSION['success_msg'] = '✅ আপনার শেয়ার যোগ করা হয়েছে এবং ' . $buyingShare . ' টি প্রকল্প শেয়ার যোগ করা হয়েছে (Your Share Added and ' . $buyingShare . ' Project Shares Added Successfully)';
    header('Location: ../users/project_shares.php'); // Redirect to the appropriate page
    exit;
    } elseif ($project_id > 1 && $buyingShare > 0 && $share) {
        // For other projects when member_project exists
        $stmtGetExtra = $pdo->prepare("SELECT no_share, extra_share FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
        $stmtGetExtra->execute([$member_id, $member_code]);
        $rowExtra = $stmtGetExtra->fetch(PDO::FETCH_ASSOC);
        $currentNoShare = isset($rowExtra['no_share']) ? (int)$rowExtra['no_share'] : 0;
        $currentExtra = isset($rowExtra['extra_share']) ? (int)$rowExtra['extra_share'] : 0;
        // If the member_project exists, select then update it
        $id = $share['id'];
        $share_amount = $buyingShare * $per_share_value;

        $stmtUpdateProject = $pdo->prepare("UPDATE member_project SET project_share = project_share + ?, share_amount = share_amount + ? WHERE id = ? AND member_id = ? AND member_code = ? AND project_id = ?");
        $stmtUpdateProject->execute([$buyingShare, $share_amount, $id, $member_id, $member_code, $project_id]);
        // Get the last share_id for this project
        $stmtLastShare = $pdo->prepare("SELECT share_id FROM project_share WHERE project_id = ? AND member_id = ? AND member_project_id = ? ORDER BY id DESC LIMIT 1");
        $stmtLastShare->execute([$project_id, $member_id, $id]);
        $lastShare = $stmtLastShare->fetch(PDO::FETCH_ASSOC);
        // Determine starting share_id number from last 3 digits
        $startingNumber = 1;
        if ($lastShare && !empty($lastShare['share_id'])) {
            // Extract only the last 3 digits from share_id
            $lastThreeDigits = substr($lastShare['share_id'], -3);
            $startingNumber = intval($lastThreeDigits) + 1;
        }
        // Insert rows into project_share table based on addShare count
        $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

        for ($i = 0; $i < $buyingShare; $i++) {
            $currentShareNumber = $startingNumber + $i;
            $share_id = 'share' . $member_id . $id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsert->execute([$id, $member_id, $member_code, $project_id, $share_id]);
        }
        // Update member_share
        
         if ($currentExtra == 0) {
           $newNoShare = $currentNoShare + $buyingShare;
        } else {
           $newNoShare = $currentNoShare; // Keep current no_share value unchanged
        }

        $newExtra = $currentExtra - $buyingShare;
        if ($newExtra < 0) $newExtra = 0;
        $stmtUpdate = $pdo->prepare("UPDATE member_share SET no_share = ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$newNoShare, $newExtra, $member_id, $member_code]);
        $_SESSION['success_msg'] = '✅ আপনার শেয়ার হালনাগাদ করা হয়েছে এবং ' . $buyingShare . ' টি প্রকল্প শেয়ার যোগ করা হয়েছে (Your Share Updated and ' . $buyingShare . ' Project Shares Added Successfully)';
        header('Location: ../users/project_shares.php'); // Redirect to the appropriate page
        exit;  
    } else {
        throw new Exception('Invalid share purchase request.');
    }      
} catch (Exception $e) {
    // Set error message in session
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
    header('Location: ../users/share.php'); // Redirect back to the share page
    exit;
}
