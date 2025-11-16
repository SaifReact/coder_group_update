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

    if ($project_id == 1) {

        $stmtUtils = $pdo->query("SELECT * FROM utils WHERE fee_type = 'samity_share' AND status = 'A' ORDER BY id ASC LIMIT 1");
        $stmtUtils = $stmtUtils->fetch(PDO::FETCH_ASSOC);
        $utilsper_share_value = $stmtUtils ? $stmtUtils['fee'] : 0;

        $stmtLastShare = $pdo->prepare("SELECT share_id FROM project_share WHERE project_id = 1 AND member_id = ? ORDER BY id DESC LIMIT 1");
        $stmtLastShare->execute([$member_id]);
        $lastShare = $stmtLastShare->fetch(PDO::FETCH_ASSOC);
        $startingNumber = 1;
        if ($lastShare && !empty($lastShare['share_id'])) {
            $lastThreeDigits = substr($lastShare['share_id'], -3);
            $startingNumber = intval($lastThreeDigits) + 1;
        }
        // Insert new share_id(s) into project_share table
        $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertCount = (int)$buyingShare;
        if ($insertCount < 2) {
            throw new Exception('Minimum 2 shares are required for CPSSL project.');
        }
        // Find member_project_id for this member and project_id=1
        $stmtProject = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND project_id = 1 ORDER BY id DESC LIMIT 1");
        $stmtProject->execute([$member_id]);
        $projectRow = $stmtProject->fetch(PDO::FETCH_ASSOC);
        $member_project_id = $projectRow ? $projectRow['id'] : null;
        if (!$member_project_id) {
            throw new Exception('No member_project found for CPSSL.');
        }
        for ($i = 0; $i < $insertCount; $i++) {
            $currentShareNumber = $startingNumber + $i;
            $share_id = 'samity' . $member_id . $member_project_id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsert->execute([$member_project_id, $member_id, $member_code, 1, $share_id]);
        }

        $stmtGetExtra = $pdo->prepare("SELECT extra_share FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
        $stmtGetExtra->execute([$member_id, $member_code]);
        $rowExtra = $stmtGetExtra->fetch(PDO::FETCH_ASSOC);
        $currentExtra = isset($rowExtra['extra_share']) ? (int)$rowExtra['extra_share'] : 0;
        $newExtra = $currentExtra - $buyingShare;
        if ($newExtra < 0) $newExtra = 0;
        $stmtUpdate = $pdo->prepare("UPDATE member_share SET samity_share = samity_share + ?, sundry_samity_share = sundry_samity_share + ?, extra_share = ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$buyingShare, $buyingShare * $utilsper_share_value, $newExtra, $member_id, $member_code]);

        $_SESSION['success_msg'] = '✅ আপনার CPSSL শেয়ার যোগ করা হয়েছে এবং ' . $buyingShare . ' টি শেয়ার সফলভাবে যোগ হয়েছে (Your CPSSL Shares Added Successfully)';
        header('Location: ../users/project_shares.php');
        exit;
       
    } else {
        
    if ($share) {
        // Calculate the new no_share after adding addShare
        $id = $share['id'];
        if (isset($share['project_share']) && $share['project_share'] == 0) {
            $no_share = $buyingShare;
        } else {
            $no_share = $buyingShare;
        }

        $share_amount = $no_share * $per_share_value;

        $stmtUpdate = $pdo->prepare("UPDATE member_share SET extra_share = extra_share - ? WHERE member_id = ? AND member_code = ?");
        $stmtUpdate->execute([$buyingShare, $member_id, $member_code]);

        // Update the member_project table with new project_share and share_amount
        $stmtUpdateProject = $pdo->prepare("UPDATE member_project SET project_share = project_share + $buyingShare, share_amount = share_amount + $share_amount WHERE id = ? AND member_id = ? AND member_code = ? AND project_id = ?");
        $stmtUpdateProject->execute([$id, $member_id, $member_code, $project_id]);

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
        
        for ($i = 0; $i < $no_share; $i++) {
            $currentShareNumber = $startingNumber + $i;
            // Format as share{member_id}{member_project_id}{project_id}001, share{member_id}{member_project_id}{project_id}002, etc.
            $share_id = 'share' . $member_id . $id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
            $stmtInsert->execute([$id, $member_id, $member_code, $project_id, $share_id]);
        }

        // Set success message in session
        $_SESSION['success_msg'] = '✅ আপনার শেয়ার হালনাগাদ করা হয়েছে এবং ' . $buyingShare . ' টি প্রকল্প শেয়ার যোগ করা হয়েছে (Your Share Updated and ' . $buyingShare . ' Project Shares Added Successfully)';
        header('Location: ../users/project_shares.php'); // Redirect to the appropriate page
        exit;
    } elseif ($buyingShare && empty($share)) {
            // member_share Update Data
            $samityShare = 2;
            $samityShareAmount = $samityShare * $per_share_value;
            $extraShare =  $previousShare - $buyingShare;
            if ($extraShare < 0) $extraShare =  $previousShare - $buyingShare;

            // Member_project and project_share Insert Data
            $buyShare = $buyingShare;
            $shareAmount = $buyShare * $per_share_value;

            // member_share update
            $stmtUpdate = $pdo->prepare("UPDATE member_share SET samity_share = ?, samity_share_amt = ?, extra_share =  ? WHERE member_id = ? AND member_code = ?");
            $stmtUpdate->execute([$samityShare, $samityShareAmount, $extraShare, $member_id, $member_code]);

            // Insert into member_project and get the inserted id
            $stmtInsert = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmtInsert->execute([$member_id, $member_code, $project_id, $buyShare, $shareAmount]);
            $member_project_id = $pdo->lastInsertId();

            if ($buyShare > 0 && $member_project_id) {
            $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            // First, insert samity shares if samityShare > 0
            // if ($samityShare > 0 ) {
            //     for ($i = 0; $i < $samityShare; $i++) {
            //         $samityShareNumber = str_pad(($i + 1), 2, '0', STR_PAD_LEFT);
            //         $share_id = 'samity' . $member_code . $member_project_id . $samityShareNumber;
            //         $ok_samity_share = $stmtInsert->execute([$member_project_id, $member_id, $member_code, 0, $share_id]);
            //         if (!$ok_samity_share) throw new Exception('Samity Share Insert Failed');
            //     }
            // }
            // Then, insert project shares if buyShare > 0
            if ($buyShare > 0) {
                $startingNumber = 1;
                for ($i = 0; $i < $buyShare; $i++) {
                    $currentShareNumber = $startingNumber + $i;
                    // Format as share{member_project_id}{project_id}001, ...
                    $share_id = 'share' . $member_id . $member_project_id . $project_id . str_pad($currentShareNumber, 3, '0', STR_PAD_LEFT);
                    $ok_project_share = $stmtInsert->execute([$member_project_id, $member_id, $member_code, $project_id, $share_id]);
                    if (!$ok_project_share) throw new Exception('Project Share Insert Failed');
                }
            }
        }
       
        if (!headers_sent()) {
        $_SESSION['success_msg'] = '✅ আপনার শেয়ার যোগ করা হয়েছে এবং ' . $buyingShare . ' টি প্রকল্প শেয়ার যোগ করা হয়েছে (Your Share Updated and ' . $buyingShare . ' Project Shares Added Successfully)';
        header('Location: ../users/project_shares.php'); // Redirect to the appropriate page
        exit;
            }
        }
        else {
        throw new Exception('No existing share found for the member in this project.');
        }
    }
        
} catch (Exception $e) {
    // Set error message in session
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
    header('Location: ../users/share.php'); // Redirect back to the share page
    exit;
}
