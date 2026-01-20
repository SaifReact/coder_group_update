<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Example: Insert Level 1
function insertLevel1($pdo, $name) {
    $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_code FROM glac_mst WHERE level_code = 1");
    $stmt->execute();
    $max = $stmt->fetchColumn();
    $new_code = $max ? $max + 1 : 1;
    $stmt = $pdo->prepare("INSERT INTO glac_mst (glac_name, glac_code, parent_id, level_code) VALUES (?, ?, 0, 1)");
    $stmt->execute([$name, $new_code]);
    return $pdo->lastInsertId();
}

// Example: Insert Level 2
function insertLevel2($pdo, $parent_id, $name) {
    // Get parent glac_code
    $stmt = $pdo->prepare("SELECT glac_code FROM glac_mst WHERE id = ?");
    $stmt->execute([$parent_id]);
    $parent_code = $stmt->fetchColumn();
    // Find max child code
    $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_code FROM glac_mst WHERE parent_id = ? AND level_code = 2");
    $stmt->execute([$parent_id]);
    $max = $stmt->fetchColumn();
    $suffix = $max ? substr($max, -2) + 1 : 1;
    $new_code = $parent_code . str_pad($suffix, 2, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO glac_mst (glac_name, glac_code, parent_id, level_code) VALUES (?, ?, ?, 2)");
    $stmt->execute([$name, $new_code, $parent_id]);
    return $pdo->lastInsertId();
}

// Example: Insert Level 3
function insertLevel3($pdo, $parent_id, $name) {
    $stmt = $pdo->prepare("SELECT glac_code FROM glac_mst WHERE id = ?");
    $stmt->execute([$parent_id]);
    $parent_code = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_code FROM glac_mst WHERE parent_id = ? AND level_code = 3");
    $stmt->execute([$parent_id]);
    $max = $stmt->fetchColumn();
    $suffix = $max ? substr($max, -2) + 1 : 1;
    $new_code = $parent_code . str_pad($suffix, 2, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO glac_mst (glac_name, glac_code, parent_id, level_code) VALUES (?, ?, ?, 3)");
    $stmt->execute([$name, $new_code, $parent_id]);
    return $pdo->lastInsertId();
}

// Example: Insert Level 4
function insertLevel4($pdo, $parent_id, $name) {
    $stmt = $pdo->prepare("SELECT glac_code FROM glac_mst WHERE id = ?");
    $stmt->execute([$parent_id]);
    $parent_code = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT MAX(CAST(glac_code AS UNSIGNED)) as max_code FROM glac_mst WHERE parent_id = ? AND level_code = 4");
    $stmt->execute([$parent_id]);
    $max = $stmt->fetchColumn();
    $suffix = $max ? substr($max, -2) + 1 : 1;
    $new_code = $parent_code . str_pad($suffix, 2, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO glac_mst (glac_name, glac_code, parent_id, level_code) VALUES (?, ?, ?, 4)");
    $stmt->execute([$name, $new_code, $parent_id]);
    return $pdo->lastInsertId();
}

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
            $parent_child = $_POST['parent_child'] ?? 'P';
            $created_by = $_SESSION['user_id'];
            $inserted_id = null;
            if ($parent_id == 0) {
                // Level 1
                $inserted_id = insertLevel1($pdo, $glac_name);
                // Update other fields for this row
                $stmt = $pdo->prepare("UPDATE glac_mst SET glac_type=?, gl_nature=?, allow_manual_dr=?, allow_manual_cr=?, parent_child=?, created_by=? WHERE id=?");
                $stmt->execute([$glac_type, $gl_nature, $allow_manual_dr, $allow_manual_cr, $parent_child, $created_by, $inserted_id]);
            } else {
                // Get parent level
                $stmt = $pdo->prepare("SELECT level_code FROM glac_mst WHERE id = ?");
                $stmt->execute([$parent_id]);
                $parent_level = $stmt->fetchColumn();
                if ($parent_level == 1) {
                    $inserted_id = insertLevel2($pdo, $parent_id, $glac_name);
                } elseif ($parent_level == 2) {
                    $inserted_id = insertLevel3($pdo, $parent_id, $glac_name);
                } elseif ($parent_level == 3) {
                    $inserted_id = insertLevel4($pdo, $parent_id, $glac_name);
                }
                // Update other fields for this row
                $stmt = $pdo->prepare("UPDATE glac_mst SET glac_type=?, gl_nature=?, allow_manual_dr=?, allow_manual_cr=?, parent_child=?, created_by=? WHERE id=?");
                $stmt->execute([$glac_type, $gl_nature, $allow_manual_dr, $allow_manual_cr, $parent_child, $created_by, $inserted_id]);
            }
            $pdo->commit();
            $_SESSION['success_msg'] = '✅ সফলভাবে জেনারেল লেজার এন্ট্রি যোগ করা হয়েছে!';
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



// Usage Example:
// $id1 = insertLevel1($pdo, 'Assets');
// $id2 = insertLevel2($pdo, $id1, 'Current Assets');
// $id3 = insertLevel3($pdo, $id2, 'Cash');
// $id4 = insertLevel4($pdo, $id3, 'Petty Cash');
?>