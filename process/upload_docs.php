<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Helper to upload document image
function uploadDocumentImage($file, $doc_type, $member_code) {
    $baseDir = dirname(__DIR__) . '/user_images';
    $memberDir = $baseDir . '/member_' . $member_code;
    
    if (!is_dir($memberDir)) {
        mkdir($memberDir, 0777, true);
    }
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return null;
        }
        
        // Check file size (3MB max)
        if ($file['size'] > 3 * 1024 * 1024) {
            return null;
        }
        
        // Verify MIME type
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            return null;
        }
        
        $filename = 'doc_' . preg_replace('/\D+/', '', $doc_type) . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target = $memberDir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'user_images/member_' . $member_code . '/' . $filename;
        }
    }
    return null;
}

// Handle delete action (delete existing document and its file)
$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        // Restrict deletion to logged-in member when available
        $sessionMemberId = $_SESSION['member_id'] ?? null;
        if ($sessionMemberId) {
            $stmt = $pdo->prepare("SELECT doc_path FROM member_documents WHERE id = ? AND member_id = ?");
            $stmt->execute([$id, $sessionMemberId]);
        } else {
            $stmt = $pdo->prepare("SELECT doc_path FROM member_documents WHERE id = ?");
            $stmt->execute([$id]);
        }
        $docPath = $stmt->fetchColumn();

        if ($docPath) {
            $absPath = dirname(__DIR__) . '/' . ltrim($docPath, '/');
            if (file_exists($absPath)) {
                @unlink($absPath);
            }

            // Delete record
            if ($sessionMemberId) {
                $stmt = $pdo->prepare("DELETE FROM member_documents WHERE id = ? AND member_id = ?");
                $stmt->execute([$id, $sessionMemberId]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM member_documents WHERE id = ?");
                $stmt->execute([$id]);
            }

            $_SESSION['error_msg'] = '❌ ডকুমেন্ট সফলভাবে মুছে ফেলা হয়েছে।';
    } else {
            $_SESSION['error_msg'] = '❌ ডকুমেন্ট পাওয়া যায়নি বা অনুমতি নেই।';
        }
    } else {
        $_SESSION['error_msg'] = '❌ অবৈধ ডিলিট অনুরোধ।';
    }
    header('Location: ../users/documents.php');
    exit;
}

// (Insert logic continues below)

// Handle insert action
if ($action === 'insert') {
    // Get form data
    $member_id = $_POST['member_id'] ?? 0;
    $member_code = $_POST['member_code'] ?? '';
    $doc_types = $_POST['required_document_types'] ?? [];
    $files = $_FILES['required_documents'] ?? null;

    // Validate member data
    if (empty($member_id) || empty($member_code)) {
        $_SESSION['error_msg'] = '❌ সদস্যের তথ্য পাওয়া যায়নি।';
        header('Location: ../users/documents.php');
        exit;
    }

    // Validate files
    if (!$files || !isset($files['name']) || !is_array($files['name'])) {
        $_SESSION['error_msg'] = '❌ আপলোডের জন্য কোনও ফাইল নির্বাচন করা হয়নি।';
        header('Location: ../users/documents.php');
        exit;
    }

    $file_count = count($files['name']);

    if ($file_count === 0) {
        $_SESSION['error_msg'] = '❌ আপলোডের জন্য কোনও ফাইল নির্বাচন করা হয়নি।';
        header('Location: ../users/documents.php');
        exit;
    }

    if (!is_array($doc_types) || count($doc_types) !== $file_count) {
        $_SESSION['error_msg'] = '❌ ডকুমেন্টের ধরন এবং ফাইলের সংখ্যা মধ্যে অমিল।';
        header('Location: ../users/documents.php');
        exit;
    }

    $uploaded_count = 0;
    $updated_count = 0;
    $failed_count = 0;

    for ($i = 0; $i < $file_count; $i++) {
        $doc_type = $doc_types[$i];

        // Check if document type already exists
        $stmt = $pdo->prepare("SELECT id, doc_path FROM member_documents WHERE member_id = ? AND member_code = ? AND doc_type = ?");
        $stmt->execute([$member_id, $member_code, $doc_type]);
        $existing_doc = $stmt->fetch(PDO::FETCH_ASSOC);

        // Prepare file array for single file
        $single_file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        // Upload file
        $doc_path = uploadDocumentImage($single_file, $doc_type, $member_code);

        if ($doc_path) {
            if ($existing_doc) {
                // Update existing document
                // Delete old file
                $old_file_path = dirname(__DIR__) . '/' . $existing_doc['doc_path'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }

                // Update database
                $stmt = $pdo->prepare("UPDATE member_documents SET doc_path = ?, created_at = NOW() WHERE id = ?");
                $stmt->execute([$doc_path, $existing_doc['id']]);
                $updated_count++;
            } else {
                // Insert new document
                $stmt = $pdo->prepare("INSERT INTO member_documents (member_id, member_code, doc_type, doc_path, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$member_id, $member_code, $doc_type, $doc_path]);
                $uploaded_count++;
            }
        } else {
            $failed_count++;
        }
    }

    // Set appropriate message
    if ($uploaded_count > 0 || $updated_count > 0) {
        $parts = [];
        if ($uploaded_count > 0) { $parts[] = "$uploaded_count টি নতুন ডকুমেন্ট আপলোড"; }
        if ($updated_count > 0) { $parts[] = "$updated_count টি ডকুমেন্ট আপডেট"; }
        $_SESSION['success_msg'] = '✅ সফলভাবে ' . implode(' এবং ', $parts) . ' হয়েছে।';
    } else {
        $_SESSION['error_msg'] = '❌ ডকুমেন্ট আপলোড ব্যর্থ হয়েছে। শুধুমাত্র JPG/PNG ফাইল (সর্বোচ্চ 3MB) গ্রহণযোগ্য।';
    }

    header('Location: ../users/documents.php');
    exit;
}

