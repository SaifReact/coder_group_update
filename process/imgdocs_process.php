<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Banner folder
$banner_folder = '../banner/';
if (!is_dir($banner_folder)) {
    mkdir($banner_folder, 0777, true);
}

// Helper to upload image or PDF
function uploadBannerFile($file) {
    global $banner_folder;
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

        // Check if the file extension is allowed
        if (!in_array($ext, $allowed_extensions)) {
            $_SESSION['error_msg'] = '❌ Only JPG, JPEG, PNG, or PDF files are allowed 1.';
            return null;
        }

        // MIME type validation (optional but recommended)
        $allowed_mime_types = ['image/jpeg', 'image/png', 'application/pdf', 'application/octet-stream'];
        $file_mime_type = mime_content_type($file['tmp_name']);  // Get MIME type

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png']) && in_array($file_mime_type, ['image/jpeg', 'image/png']);
        // Accept PDF if extension is pdf and mime is application/pdf, application/octet-stream, or if mime detection fails
        $isPDF = $ext === 'pdf' && (
            in_array($file_mime_type, ['application/pdf', 'application/octet-stream']) ||
            strpos($file_mime_type, 'pdf') !== false ||
            empty($file_mime_type)
        );

        if (!$isImage && !$isPDF) {
            $_SESSION['error_msg'] = '❌ Invalid file type. Only JPG, JPEG, PNG, or PDF files are allowed.';
            return null;
        }

        // Generate a unique file name
        $filename = 'banner_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target = $banner_folder . $filename;

        // Move the uploaded file to the target folder
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        }
    }
    return null;
}

$action = $_POST['action'] ?? '';

// AJAX field update (name_bn, name_en)
if ($action === 'update_field') {
    $id = $_POST['id'] ?? '';
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';
    if ($id && in_array($field, ['banner_name_bn', 'banner_name_en'])) {
        $stmt = $pdo->prepare("UPDATE banner SET $field=? WHERE id=?");
        $stmt->execute([$value, $id]);
        echo 'success';
    } else {
        echo 'error';
    }
    exit;
}

// AJAX image/PDF update (on file change)
if ($action === 'update_file') {
    $id = $_POST['id'] ?? '';
    $file = !empty($_FILES['banner_file']['name']) ? uploadBannerFile($_FILES['banner_file']) : null;
    if ($id && $file) {
        // Delete old file
        $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $oldFile = $stmt->fetchColumn();
        if ($oldFile && file_exists($banner_folder . $oldFile)) {
            unlink($banner_folder . $oldFile);
        }
        // Update DB
        $stmt = $pdo->prepare("UPDATE banner SET banner_image=? WHERE id=?");
        $stmt->execute([$file, $id]);
    }
    header('Location: ../admin/imgdocs.php');
    exit;
}

if ($action === 'insert') {
    $banner_category = $_POST['banner_category'] ?? '';
    $name_bn = $_POST['banner_name_bn'] ?? '';
    $name_en = $_POST['banner_name_en'] ?? '';
    // Support both banner_file and banner_image field names
    $fileField = isset($_FILES['banner_file']) ? $_FILES['banner_file'] : (isset($_FILES['banner_image']) ? $_FILES['banner_image'] : null);
    $file = $fileField ? uploadBannerFile($fileField) : null;
    if ($name_bn && $name_en && $file) {
        $stmt = $pdo->prepare("INSERT INTO banner (banner_category, banner_name_bn, banner_name_en, banner_image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$banner_category, $name_bn, $name_en, $file]);
        $_SESSION['success_msg'] = '✅ Image/PDF added successfully...! (সফলভাবে হালনাগাদ করা হলো ..!)';
    } else {
        if (!$name_bn || !$name_en) {
            $_SESSION['error_msg'] = '❌ Image/PDF name (Bangla/English) is required.';
        } elseif (!$file) {
            $_SESSION['error_msg'] = '❌ Only JPG, JPEG, PNG, or PDF files are allowed 2.';
        } else {
            $_SESSION['error_msg'] = '❌ Failed to add banner due to unknown error.';
        }
    }
    header('Location: ../admin/imgdocs.php');
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $banner_category = $_POST['banner_category'] ?? '';
    $name_bn = $_POST['banner_name_bn'] ?? '';
    $name_en = $_POST['banner_name_en'] ?? '';
    // Support both banner_file and banner_image field names
    $fileField = isset($_FILES['banner_file']) ? $_FILES['banner_file'] : (isset($_FILES['banner_image']) ? $_FILES['banner_image'] : null);
    $file = $fileField ? uploadBannerFile($fileField) : null;
    if ($id && $name_bn && $name_en) {
        if ($file) {
            // Delete old file if a new one is uploaded
            $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
            $stmt->execute([$id]);
            $oldFile = $stmt->fetchColumn();
            if ($oldFile && file_exists($banner_folder . $oldFile)) {
                unlink($banner_folder . $oldFile);
            }
            // Update DB with new file
            $stmt = $pdo->prepare("UPDATE banner SET banner_category=?, banner_name_bn=?, banner_name_en=?, banner_image=? WHERE id=?");
            $stmt->execute([$banner_category, $name_bn, $name_en, $file, $id]);
        } else {
            // No new file, just update other fields
            $stmt = $pdo->prepare("UPDATE banner SET banner_category=?, banner_name_bn=?, banner_name_en=? WHERE id=?");
            $stmt->execute([$banner_category, $name_bn, $name_en, $id]);
        }
        $_SESSION['success_msg'] = '✅ Image/PDF updated successfully..! (সফলভাবে হালনাগাদ করা হলো ..!)';
    } else {
        $_SESSION['error_msg'] = '❌ Failed to update image/PDF.';
    }
    header('Location: ../admin/imgdocs.php');
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        if ($file && file_exists($banner_folder . $file)) {
            unlink($banner_folder . $file);
        }
        $stmt = $pdo->prepare("DELETE FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = '✅ Image/PDF deleted successfully..! (সফলভাবে মুছে ফেলা হলো ..!)';
    } else {
        $_SESSION['error_msg'] = '❌ Failed to delete image/PDF.';
    }
    header('Location: ../admin/imgdocs.php');
    exit;
}
?>
