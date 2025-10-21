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

// Helper to upload image
function uploadBannerImage($file) {
    global $banner_folder;
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return null;
        }
        $filename = 'banner_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $banner_folder . $filename;
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

// AJAX image update (on file change)
if ($action === 'update_image') {
    $id = $_POST['id'] ?? '';
    $image = !empty($_FILES['banner_image']['name']) ? uploadBannerImage($_FILES['banner_image']) : null;
    if ($id && $image) {
        // Delete old image
        $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $oldImg = $stmt->fetchColumn();
        if ($oldImg && file_exists($banner_folder . $oldImg)) {
            unlink($banner_folder . $oldImg);
        }
        // Update DB
        $stmt = $pdo->prepare("UPDATE banner SET banner_image=? WHERE id=?");
        $stmt->execute([$image, $id]);
    }
    header('Location: ../admin/images.php');
    exit;
}

if ($action === 'insert') {
    $banner_category = $_POST['banner_category'] ?? '';
    $name_bn = $_POST['banner_name_bn'] ?? '';
    $name_en = $_POST['banner_name_en'] ?? '';
    $image = uploadBannerImage($_FILES['banner_image']);
    if ($name_bn && $name_en && $image) {
        $stmt = $pdo->prepare("INSERT INTO banner (banner_category, banner_name_bn, banner_name_en, banner_image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$banner_category, $name_bn, $name_en, $image]);
        $_SESSION['success_msg'] = '✅ Image added successfully...!';
    } else {
        if (!$name_bn || !$name_en) {
            $_SESSION['error_msg'] = 'Image name (Bangla/English) is required.';
        } elseif (!$image) {
            $_SESSION['error_msg'] = 'Only JPG, JPEG, PNG images are allowed.';
        } else {
            $_SESSION['error_msg'] = 'Failed to add banner due to unknown error.';
        }
    }
    header('Location: ../admin/images.php');
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $banner_category = $_POST['banner_category'] ?? '';
    $name_bn = $_POST['banner_name_bn'] ?? '';
    $name_en = $_POST['banner_name_en'] ?? '';
    $image = !empty($_FILES['banner_image']['name']) ? uploadBannerImage($_FILES['banner_image']) : null;
    if ($id && $name_bn && $name_en) {
        if ($image) {
            $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
            $stmt->execute([$id]);
            $oldImg = $stmt->fetchColumn();
            if ($oldImg && file_exists($banner_folder . $oldImg)) {
                unlink($banner_folder . $oldImg);
            }
            $stmt = $pdo->prepare("UPDATE banner SET banner_category=?, banner_name_bn=?, banner_name_en=?, banner_image=? WHERE id=?");
            $stmt->execute([$banner_category, $name_bn, $name_en, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE banner SET banner_category=?, banner_name_bn=?, banner_name_en=? WHERE id=?");
            $stmt->execute([$banner_category, $name_bn, $name_en, $id]);
        }
        $_SESSION['success_msg'] = '✅ Image updated successfully..! (সফলভাবে হালনাগাদ করা হলো ..!)';
    } else {
        $_SESSION['error_msg'] = 'Failed to update image.';
    }
    header('Location: ../admin/images.php');
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($id) {
        $stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        if ($img && file_exists($banner_folder . $img)) {
            unlink($banner_folder . $img);
        }
        $stmt = $pdo->prepare("DELETE FROM banner WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = '✅ Image deleted successfully..! (সফলভাবে মুছে ফেলা হলো ..!)';
    } else {
        $_SESSION['error_msg'] = 'Failed to delete image.';
    }
    header('Location: ../admin/images.php');
    exit;
}
