<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $pdo->beginTransaction();
        
        if ($_POST['action'] === 'add') {
            $glac_name = $_POST['glac_name'] ?? '';
            $glac_type = $_POST['glac_type'] ?? '';
            $parent_id = intval($_POST['parent_id'] ?? 0);
            $gl_nature = $_POST['gl_nature'] ?? 'D';
            $allow_manual_dr = $_POST['allow_manual_dr'] ?? 'Y';
            $allow_manual_cr = $_POST['allow_manual_cr'] ?? 'Y';
            $parent_child = $_POST['parent_child'] ?? 'P'; // Get from radio button
            $created_by = $_SESSION['user_id'];
            
            // Determine level_code
            $level_code = 1;
            $glac_code = '';
            
            if ($parent_id == 0) {
                // Root level (Level 1)
                $level_code = 1;
                
                // Generate glac_code for Level 1: 10000000, 20000000, 30000000, 40000000 etc.
                $type_code_map = ['A' => 1, 'L' => 2, 'I' => 3, 'E' => 4];
                $base_code = $type_code_map[$glac_type] ?? 1;
                
                $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_code FROM glac_mst WHERE glac_type = ? AND level_code = 1");
                $stmt->execute([$glac_type]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['max_code']) {
                    // Increment by 1
                    $glac_code = $result['max_code'] + 1;
                } else {
                    // First entry: 10000000, 20000000, 30000000, 40000000
                    $glac_code = $base_code . '0000000';
                }
            } else {
                // Child level
                // Get parent information
                $stmt = $pdo->prepare("SELECT glac_code, level_code, glac_type FROM glac_mst WHERE id = ?");
                $stmt->execute([$parent_id]);
                $parent = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($parent) {
                    $level_code = $parent['level_code'] + 1;
                    $parent_glac_code = $parent['glac_code'];
                    $glac_type = $parent['glac_type']; // Inherit parent's type
                    
                    // Get next child number
                    $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_child FROM glac_mst WHERE parent_id = ?");
                    $stmt->execute([$parent_id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result['max_child']) {
                        // Increment by 1
                        $glac_code = $result['max_child'] + 1;
                    } else {
                        // First child: parent_code + 1
                        $glac_code = intval($parent_glac_code) + 1;
                    }
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO glac_mst (glac_code, glac_name, parent_child, parent_id, glac_type, level_code, gl_nature, allow_manual_dr, allow_manual_cr, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'A', ?)");
            $stmt->execute([$glac_code, $glac_name, $parent_child, $parent_id, $glac_type, $level_code, $gl_nature, $allow_manual_dr, $allow_manual_cr, $created_by]);
            
            $pdo->commit();
            
            $_SESSION['success_msg'] = '✅ সফলভাবে জেনারেল লেজার এন্ট্রি যোগ করা হয়েছে! Code: ' . $glac_code . ', Level: ' . $level_code;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        
        if ($_POST['action'] === 'update') {
            $id = intval($_POST['id'] ?? 0);
            $glac_name = $_POST['glac_name'] ?? '';
            $gl_nature = $_POST['gl_nature'] ?? 'D';
            $allow_manual_dr = $_POST['allow_manual_dr'] ?? 'Y';
            $allow_manual_cr = $_POST['allow_manual_cr'] ?? 'Y';
            $status = $_POST['status'] ?? 'A';
            $updated_by = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("UPDATE glac_mst SET glac_name = ?, gl_nature = ?, allow_manual_dr = ?, allow_manual_cr = ?, status = ?, updated_by = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$glac_name, $gl_nature, $allow_manual_dr, $allow_manual_cr, $status, $updated_by, $id]);
            
            $pdo->commit();
            
            $_SESSION['success_msg'] = '✅ সফলভাবে জেনারেল লেজার আপডেট করা হয়েছে!';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        
        if ($_POST['action'] === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            
            // Check if has children
            $stmt = $pdo->prepare("SELECT COUNT(*) as child_count FROM glac_mst WHERE parent_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['child_count'] > 0) {
                $_SESSION['error_msg'] = '❌ এই লেজারের চাইল্ড আছে, প্রথমে চাইল্ড ডিলিট করুন!';
            } else {
                $stmt = $pdo->prepare("DELETE FROM glac_mst WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['success_msg'] = '✅ সফলভাবে জেনারেল লেজার ডিলিট করা হয়েছে!';
            }
            
            $pdo->commit();
            header("Location: ../admin/general_ledger.php");
            exit;
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_msg'] = '❌ Error: ' . $e->getMessage();
        header("Location: ../admin/general_ledger.php");
        exit;
    }
}