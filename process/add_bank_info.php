<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];   
$member_code = $_SESSION['member_code'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_id = isset($_POST['bank_id']) ? (int)$_POST['bank_id'] : 0;
    $ac_no = isset($_POST['ac_no']) ? trim($_POST['ac_no']) : '';
    $ac_title = isset($_POST['ac_title']) ? trim($_POST['ac_title']) : '';
    $bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
    $branch_name = isset($_POST['branch_name']) ? trim($_POST['branch_name']) : '';
    $routing_no = isset($_POST['routing_no']) ? trim($_POST['routing_no']) : '';
    
    // Validate required fields
    if (empty($member_id) || empty($member_code) || empty($ac_no) || empty($ac_title) || empty($bank_name) || empty($branch_name)) {
        $_SESSION['error_msg'] = '❌ সকল প্রয়োজনীয় তথ্য প্রদান করুন! (Please provide all required information!)';
        header('Location: ../users/index.php');
        exit;
    }
    
    try {
        
        if ($bank_id > 0) {
            // Update existing record
            $updateStmt = $pdo->prepare("
                UPDATE member_bank 
                SET ac_no = ?, ac_title = ?, bank_name = ?, branch_name = ?, routing_no = ?
                WHERE id = ? AND member_id = ? AND member_code = ?
            ");
            
            $updateStmt->execute([
                $ac_no,
                $ac_title,
                $bank_name,
                $branch_name,
                $routing_no,
                $bank_id,
                $member_id,
                $member_code
            ]);
            
            $_SESSION['success_msg'] = '✅ ব্যাংক হিসাব তথ্য সফলভাবে আপডেট করা হয়েছে! (Bank account information updated successfully!)';
            
        } else {
            // Check if this account already exists for this member
            $checkStmt = $pdo->prepare("SELECT id FROM member_bank WHERE member_id = ? AND member_code = ? AND ac_no = ?");
            $checkStmt->execute([$member_id, $member_code, $ac_no]);
            
            if ($checkStmt->fetch()) {
                $_SESSION['error_msg'] = '❌ এই হিসাব নম্বরটি ইতিমধ্যে যুক্ত আছে! (This account number already exists!)';
                header('Location: ../users/index.php');
                exit;
            }
            
            // Insert into member_bank table
            $insertStmt = $pdo->prepare("
                INSERT INTO member_bank (member_id, member_code, ac_no, ac_title, bank_name, branch_name, routing_no, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'A')
            ");
            
            $insertStmt->execute([
                $member_id,
                $member_code,
                $ac_no,
                $ac_title,
                $bank_name,
                $branch_name,
                $routing_no
            ]);
            
            $_SESSION['success_msg'] = '✅ ব্যাংক হিসাব তথ্য সফলভাবে যুক্ত করা হয়েছে! (Bank account information added successfully!)';
        }
        
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $_SESSION['error_msg'] = '❌ ডাটাবেস ত্রুটি! তথ্য সংরক্ষণ করা যায়নি। (Database error! Could not save information.)';
    }
    
    header('Location: ../users/index.php');
    exit;
    
} else {
    header('Location: ../users/index.php');
    exit;
}
