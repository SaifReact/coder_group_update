<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Add project_image column if not exists
try { $pdo->exec("ALTER TABLE project ADD COLUMN project_image VARCHAR(255) DEFAULT NULL"); }
catch (Exception $e) { /* already exists */ }

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Upload helper
function uploadProjectImage($file, $old_image = null) {
    $upload_dir = __DIR__ . '/../assets/img/';
    $allowed    = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        throw new Exception('শুধুমাত্র JPG, PNG, GIF, WEBP ছবি অনুমোদিত।');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('ছবির সাইজ ৫MB এর বেশি হওয়া যাবে না।');
    }

    $filename    = 'project_' . time() . '_' . uniqid() . '.' . $ext;
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('ছবি আপলোড ব্যর্থ হয়েছে।');
    }

    // Delete old image
    if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
        unlink(__DIR__ . '/../' . $old_image);
    }

    return 'assets/img/' . $filename;
}

try {
    if ($action === 'insert') {
        $project_name_bn = trim($_POST['project_name_bn']  ?? '');
        $project_name_en = trim($_POST['project_name_en']  ?? '');
        $about_project   = $_POST['about_project']         ?? '';
        $project_value   = trim($_POST['project_value']    ?? 0);
        $project_share   = trim($_POST['project_share']    ?? 0);
        $per_share_value = trim($_POST['per_share_value']  ?? 0);
        $project_image   = null;

        if (!empty($_FILES['project_image']['name'])) {
            $project_image = uploadProjectImage($_FILES['project_image']);
        }

        $stmt = $pdo->prepare(
            "INSERT INTO project
             (project_name_bn, project_name_en, about_project, project_value, project_share, per_share_value, project_image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$project_name_bn, $project_name_en, $about_project,
                        $project_value, $project_share, $per_share_value, $project_image]);

        $_SESSION['success_msg'] = "✅ Project Added Successfully! (সফলভাবে যোগ করা হয়েছে!)";
        header("Location: ../admin/project.php");
        exit;
    }

    if ($action === 'update') {
        $id              = (int)($_POST['id']                   ?? 0);
        $project_name_bn = trim($_POST['edit_project_name_bn']  ?? '');
        $project_name_en = trim($_POST['edit_project_name_en']  ?? '');
        $about_project   = $_POST['edit_about_project']         ?? '';
        $project_value   = trim($_POST['edit_project_value']    ?? 0);
        $project_share   = trim($_POST['edit_project_share']    ?? 0);
        $per_share_value = trim($_POST['edit_per_share_value']  ?? 0);

        // Get current image
        $cur = $pdo->prepare("SELECT project_image FROM project WHERE id = ?");
        $cur->execute([$id]);
        $old_image = $cur->fetchColumn();

        if (!empty($_FILES['project_image']['name'])) {
            $project_image = uploadProjectImage($_FILES['project_image'], $old_image ?: null);
            $stmt = $pdo->prepare(
                "UPDATE project
                 SET project_name_bn=?, project_name_en=?, about_project=?,
                     project_value=?, project_share=?, per_share_value=?, project_image=?
                 WHERE id=?"
            );
            $stmt->execute([$project_name_bn, $project_name_en, $about_project,
                            $project_value, $project_share, $per_share_value, $project_image, $id]);
        } else {
            $stmt = $pdo->prepare(
                "UPDATE project
                 SET project_name_bn=?, project_name_en=?, about_project=?,
                     project_value=?, project_share=?, per_share_value=?
                 WHERE id=?"
            );
            $stmt->execute([$project_name_bn, $project_name_en, $about_project,
                            $project_value, $project_share, $per_share_value, $id]);
        }

        $_SESSION['success_msg'] = "✅ Project Updated Successfully! (সফলভাবে হালনাগাদ হয়েছে!)";
        header("Location: ../admin/project.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            // Delete image file
            $cur = $pdo->prepare("SELECT project_image FROM project WHERE id = ?");
            $cur->execute([$id]);
            $img = $cur->fetchColumn();
            if ($img && file_exists(__DIR__ . '/../' . $img)) {
                unlink(__DIR__ . '/../' . $img);
            }
            $stmt = $pdo->prepare("DELETE FROM project WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_msg'] = "✅ Project Deleted! (সফলভাবে মুছে ফেলা হয়েছে!)";
        }
        header("Location: ../admin/project.php");
        exit;
    }

    $_SESSION['error_msg'] = "Error: Invalid action.";
    header("Location: ../admin/project.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: ../admin/project.php");
    exit;
}
