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

    $stmtUtils = $pdo->query("SELECT fee FROM utils WHERE fee_type = 'samity_share' AND status = 'A' ORDER BY id ASC LIMIT 1");
    $stmtUtils = $stmtUtils->fetch(PDO::FETCH_ASSOC);   
    $per_share_value_utils = $stmtUtils ? (float)$stmtUtils['fee'] : 0;

    // Fetch per_share_value from project table for this project_id
    $stmtProject = $pdo->prepare("SELECT per_share_value FROM project WHERE id = ?");
    $stmtProject->execute([$project_id]);
    $projectRow = $stmtProject->fetch(PDO::FETCH_ASSOC);
    $per_share_value = $projectRow ? (float)$projectRow['per_share_value'] : 0;

    $stmtGetExtra = $pdo->prepare("SELECT no_share, samity_share, extra_share FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
    $stmtGetExtra->execute([$member_id, $member_code]);
    $rowExtra = $stmtGetExtra->fetch(PDO::FETCH_ASSOC);
    $currentExtraShare = isset($rowExtra['extra_share']) ? (int)$rowExtra['extra_share'] : 0;
    $currentSamityShare = isset($rowExtra['samity_share']) ? (int)$rowExtra['samity_share'] : 0;
    $currentNoShare = isset($rowExtra['no_share']) ? (int)$rowExtra['no_share'] : 0;

    $stmt = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = ?");
    $stmt->execute([$member_id, $member_code, $project_id]);
    $share = $stmt->fetch(PDO::FETCH_ASSOC);

    $startingNumber = 1;

    if ($project_id == 1 && $buyingShare > 0) {

        $stmtLastShare = $pdo->prepare("SELECT share_id FROM project_share WHERE project_id = 1 AND member_id = ? AND member_code = ? ORDER BY id DESC LIMIT 1");
        $stmtLastShare->execute([$member_id, $member_code]);
        $lastShare = $stmtLastShare->fetch(PDO::FETCH_ASSOC);

        if ($lastShare && !empty($lastShare['share_id'])) {
            $lastThreeDigits = substr($lastShare['share_id'], -3);
            $startingNumber = intval($lastThreeDigits) + 1;
        }

        // Check if this is first time adding CPSSL shares to project_share table
        $stmtCheckExisting = $pdo->prepare("SELECT COUNT(*) as count, id FROM member_project WHERE project_id = 1 AND member_id = ? AND member_code = ?");
        $stmtCheckExisting->execute([$member_id, $member_code]);
        $existingCount = $stmtCheckExisting->fetch(PDO::FETCH_ASSOC);
        $hasExistingShares = ($existingCount['count'] > 0);
        $existingMemberProjectId = $existingCount['id'];

        // Insert new share_id(s) into project_share table
        $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        
        // First time: add both current samity + buying shares
        // Second time onwards: add only buying shares
        if ($currentSamityShare && !$hasExistingShares && $project_id == 1) {
            $insertCount = (int)$buyingShare + $currentSamityShare;
        } else {
            $insertCount = (int)$buyingShare;
        }
        
        if (!$existingMemberProjectId) {
            $stmtInsertProject = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmtInsertProject->execute([$member_id, $member_code, 1, 0, 0]);
            $member_project_id = $pdo->lastInsertId();
        } else {
            $member_project_id = $existingMemberProjectId;
        }

        for ($i = 0; $i < $insertCount; $i++) {
            $currentShareNumber = $startingNumber + $i;
            $share_id = 'samity' . $member_id . $member_project_id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsert->execute([$member_project_id, $member_id, $member_code, 1, $share_id]);
        }

        $newExtra = $currentExtraShare - $buyingShare;
        if ($newExtra < 0) $newExtra = 0;
        $sundry_samity_share = $insertCount * $per_share_value_utils;
        $stmtUpdate = $pdo->prepare("UPDATE member_share SET samity_share = samity_share + ?, sundry_samity_share = sundry_samity_share + ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$buyingShare, $sundry_samity_share, $newExtra, $member_id, $member_code]);

        $_SESSION['success_msg'] = '✅ আপনার CPSSL শেয়ার যোগ করা হয়েছে এবং ' . $buyingShare . ' টি শেয়ার সফলভাবে যোগ হয়েছে (Your CPSSL Shares Added Successfully)';
        header('Location: ../users/project_shares.php');
        exit;

    } elseif ($project_id > 1 && $buyingShare > 0) {

        $original_project_id = (int)$project_id; // Preserve original project_id

        if (!$share && empty($share)) {
            if ($currentSamityShare > 0) {

                // Look for project_id = 1
                $stmt = $pdo->prepare("SELECT id, project_id FROM member_project WHERE member_id = ? AND project_id = 1 LIMIT 1");
                $stmt->execute([$member_id]);
                $samityProject = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$samityProject) {
                    // No samity project for this member → create one
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at)
                        VALUES (?, ?, 1, 0, 0, NOW())
                    ");
                    $stmtInsert->execute([$member_id, $member_code]);
                    $samity_member_project_id = $pdo->lastInsertId(); 
                    $samity_project_id = 1; // Use separate variable for samity project
                } else {
                    $samity_member_project_id = $samityProject['id'];
                    $samity_project_id = $samityProject['project_id'];
                }

                // Insert shares under this samity project
                if( !$samity_member_project_id && $samity_project_id == 1 ) {
                    $stmtShare = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())");

                for ($i = 0; $i < $currentSamityShare; $i++) {
                    $currentShareNumber = $startingNumber + $i;
                    $n = str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
                    $share_id = "samity{$member_id}{$samity_member_project_id}{$samity_project_id}{$n}";
                    $stmtShare->execute([$samity_member_project_id, $member_id, $member_code, $samity_project_id, $share_id]);
                }
                }   
            }

            if ($buyingShare > 0) {
                // Look for specific project_id
                $stmt2 = $pdo->prepare("SELECT id, project_id FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = ? LIMIT 1");
                $stmt2->execute([$member_id, $member_code, $original_project_id]);
                $buyingProject = $stmt2->fetch(PDO::FETCH_ASSOC);

                $buyingShareAmount = $buyingShare * $per_share_value;

                if (!$buyingProject) {
                    // Create a new project for buying shares
                    $stmtInsert2 = $pdo->prepare("
                        INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at)
                        VALUES (?, ?, $original_project_id, $buyingShare, $buyingShareAmount, NOW())
                    ");
                    $stmtInsert2->execute([$member_id, $member_code]);
                    $buying_member_project_id = $pdo->lastInsertId();
                    $buying_project_id = $original_project_id;
                } else {
                    $buying_member_project_id = $buyingProject['id'];
                    $buying_project_id = $buyingProject['project_id'];
                }

                // Insert buying shares
                $stmtShare2 = $pdo->prepare("
                    INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                for ($j = 0; $j < $buyingShare; $j++) {
                    $currentShareNumber = $startingNumber + $j;
                    $n = str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
                    $share_id = "share{$member_id}{$buying_member_project_id}{$buying_project_id}{$n}";
                    $stmtShare2->execute([$buying_member_project_id, $member_id, $member_code, $buying_project_id, $share_id]);
                }
            }

            if ($currentExtraShare == 0) {
                $newNoShare = $currentNoShare + $buyingShare;
            } else {
                $newNoShare = $currentNoShare; // Keep current no_share value unchanged
            }

            $newExtra = $currentExtraShare - $buyingShare;
            if ($newExtra < 0) $newExtra = 0;

            // Update member share table
            $stmtUpdate = $pdo->prepare("UPDATE member_share SET no_share = ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
            $stmtUpdate->execute([$newNoShare, $newExtra, $member_id, $member_code]);

        } elseif ($share) {

            $id = $share['id'];
            $share_amount = $buyingShare * $per_share_value;

            // Get the last share_id for this project
            $stmtLastShare = $pdo->prepare("SELECT share_id FROM project_share WHERE project_id = ? AND member_id = ? AND member_project_id = ? ORDER BY id DESC LIMIT 1");
            $stmtLastShare->execute([$project_id, $member_id, $id]);
            $lastShare = $stmtLastShare->fetch(PDO::FETCH_ASSOC);

            // Determine starting share_id number from last 3 digits

            if ($lastShare && !empty($lastShare['share_id'])) {
                // Extract only the last 3 digits from share_id
                $lastThreeDigits = substr($lastShare['share_id'], -3);
                $startingNumber = intval($lastThreeDigits) + 1;
            }

            $stmtUpdateProject = $pdo->prepare("UPDATE member_project SET project_share = project_share + ?, share_amount = share_amount + ? WHERE id = ? AND member_id = ? AND member_code = ? AND project_id = ?");
            $stmtUpdateProject->execute([$buyingShare, $share_amount, $id, $member_id, $member_code, $project_id]);

            // Insert rows into project_share table based on addShare count
            $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

            for ($k = 0; $k < $buyingShare; $k++) {
                $currentShareNumber = $startingNumber + $k;
                $n = str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
                $share_id = "share{$member_id}{$id}{$project_id}{$n}";
                $stmtInsert->execute([$id, $member_id, $member_code, $project_id, $share_id]);
            }
            // Update member_share
            
            if ($currentExtraShare == 0) {
            $newNoShare = $currentNoShare + $buyingShare;
            } else {
            $newNoShare = $currentNoShare; // Keep current no_share value unchanged
            }

            $newExtra = $currentExtraShare - $buyingShare;
            if ($newExtra < 0) $newExtra = 0;
            $stmtUpdate = $pdo->prepare("UPDATE member_share SET no_share = ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
            $stmtUpdate->execute([$newNoShare, $newExtra, $member_id, $member_code]);

        }

        $_SESSION['success_msg'] = '✅ আপনার শেয়ার হালনাগাদ করা হয়েছে এবং ' . $buyingShare . ' টি প্রকল্প শেয়ার যোগ করা হয়েছে (Your Share Updated and ' . $buyingShare . ' Project Shares Added Successfully)';
        header('Location: ../users/project_shares.php');
        exit;
    }
    else {
        throw new Exception('Invalid share purchase request.');
    }    
} catch (Exception $e) {
    // Set error message in session
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
    header('Location: ../users/share.php'); // Redirect back to the share page
    exit;
}
