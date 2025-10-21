<?php
session_start();
include_once __DIR__ . '/../config/config.php';

// This endpoint accepts only POST from the form (no AJAX/JSON). Responses use session messages + redirect.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_msg'] = 'Invalid request method.';
    header('Location: ../users/documents.php');
    exit;
}

// Detect AJAX request (fetch/XHR) to decide response type
$isAjax = false;
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $isAjax = true;
} elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
    $isAjax = true;
}
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('display_errors', 0);

try {
    // No AJAX detection anymore; this script always redirects back with session messages

    // --- Validate identity ---
    $member_id   = isset($_POST['member_id'])   ? (int)$_POST['member_id']   : 0;
    $member_code = isset($_POST['member_code']) ? trim($_POST['member_code']) : '';

    if ($member_id <= 0 || $member_code === '') {
        throw new Exception('Member context missing.');
    }

    if (!isset($_FILES['required_documents'])) {
        throw new Exception('No files received.');
    }

    $doc_types = $_POST['required_document_types'] ?? [];
    $files     = $_FILES['required_documents'];

    $count = is_array($files['name']) ? count($files['name']) : 0;
    if ($count === 0) {
        throw new Exception('No files selected for upload.');
    }

    if (!is_array($doc_types) || count($doc_types) !== $count) {
        throw new Exception('Mismatch between document types and files count.');
    }

    // --- Prepare storage directory ---
    $baseDir   = dirname(__DIR__) . '/user_images';
    $memberDir = $baseDir . '/member_' . $member_code;

    if (!is_dir($memberDir)) {
        mkdir($memberDir, 0777, true);
    }

    $allowedExt  = ['jpg', 'jpeg', 'png'];
    $allowedMime = ['image/jpeg', 'image/png'];
    $maxBytes    = 3 * 1024 * 1024; // 3MB

    $savedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        $doc_type = $doc_types[$i];
        $name     = $files['name'][$i];
        $tmp_name = $files['tmp_name'][$i];
        $size     = $files['size'][$i];
        $error    = $files['error'][$i];

        if ($error !== UPLOAD_ERR_OK) {
            error_log("Upload error for $name: code=$error");
            continue;
        }

        if (!is_uploaded_file($tmp_name)) {
            error_log("Not a valid uploaded file for $name");
            continue;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            error_log("Invalid file extension for $name");
            continue;
        }

        if ($size > $maxBytes) {
            error_log("File too large ($size bytes): $name");
            continue;
        }

        $mime = mime_content_type($tmp_name);
        if (!in_array($mime, $allowedMime)) {
            error_log("Invalid MIME type ($mime) for $name");
            continue;
        }

        // --- Check duplicate ---
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM member_documents WHERE member_id=? AND member_code=? AND doc_type=?");
        $stmtCheck->execute([$member_id, $member_code, $doc_type]);
        if ($stmtCheck->fetchColumn() > 0) {
            continue; // Skip duplicates
        }

        // --- Save file ---
        $uniqueName = 'doc_' . preg_replace('/\D+/', '', $doc_type) . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath   = $memberDir . '/' . $uniqueName;

        if (!move_uploaded_file($tmp_name, $destPath)) {
            error_log("Failed to move uploaded file $name");
            continue;
        }

        $doc_path = 'user_images/member_' . $member_code . '/' . $uniqueName;

        $stmt = $pdo->prepare("INSERT INTO member_documents (member_id, member_code, doc_type, doc_path, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$member_id, $member_code, $doc_type, $doc_path]);

        $savedFiles[] = [
            'doc_type' => $doc_type,
            'file' => $doc_path
        ];
    }

    if (empty($savedFiles)) {
        $_SESSION['error_msg'] = '❌ No files were uploaded or all files were duplicates.';
        throw new Exception($_SESSION['error_msg']);
    }

    // Build success message, store in session
    $successMsg = "ডকুমেন্টগুলো সফলভাবে আপলোড হয়েছে। (" . count($savedFiles) . ")";
    $_SESSION['success_msg'] = $successMsg;

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'saved_count' => count($savedFiles),
            'files' => $savedFiles,
            'message' => $successMsg
        ]);
        exit;
    }

    // Non-AJAX: redirect back to documents page where toast will be shown
    header('Location: ../users/documents.php');
    exit;

} catch (Exception $e) {
    error_log('Upload failed: ' . $e->getMessage());
    $_SESSION['error_msg'] = $e->getMessage();
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $_SESSION['error_msg']]);
        exit;
    }
    header('Location: ../users/documents.php');
    exit;
}
