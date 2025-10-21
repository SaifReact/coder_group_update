<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$action = $_POST['action'] ?? '';

if ($action === 'insert') {
    $company_name_en = $_POST['company_name_en'] ?? '';
    $company_name_bn = $_POST['company_name_bn'] ?? '';
    $company_image = $_FILES['company_image'] ?? null;
    $about_company = $_POST['about_company'] ?? '';

    if ($company_name_en && $company_name_bn && $company_image && $about_company) {
        $image_name = time() . '_' . basename($company_image['name']);
        $target_dir = '../company/';
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($company_image['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("
                INSERT INTO company (company_name_en, company_name_bn, company_image, about_company) 
                VALUES (:company_name_en, :company_name_bn, :company_image, :about_company)
            ");
            $stmt->execute([
                ':company_name_en' => $company_name_en,
                ':company_name_bn' => $company_name_bn,
                ':company_image' => $image_name,
                ':about_company' => $about_company,
            ]);
            $_SESSION['success_msg'] = "Company added successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to upload image!";
        }
    } else {
        $_SESSION['error_msg'] = "Required fields missing!";
    }

    header("Location: ../admin/company.php");
    exit;

} elseif ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $company_name_en = $_POST['edit_company_name_en'] ?? '';
    $company_name_bn = $_POST['edit_company_name_bn'] ?? '';
    $company_image = $_FILES['edit_company_image'] ?? null;
    $about_company = $_POST['edit_about_company'] ?? '';

    if ($id && $company_name_en && $company_name_bn) {
        // fetch existing company
        $stmt = $pdo->prepare("SELECT company_image FROM company WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existing_company = $stmt->fetch();

        if ($existing_company) {
            $image_name = $existing_company['company_image'];

            // handle image upload
            if ($company_image && $company_image['tmp_name']) {
                $image_name = time() . '_' . basename($company_image['name']);
                $target_dir = '../company/';
                $target_file = $target_dir . $image_name;

                if (move_uploaded_file($company_image['tmp_name'], $target_file)) {
                    // delete old image
                    if (file_exists($target_dir . $existing_company['company_image'])) {
                        unlink($target_dir . $existing_company['company_image']);
                    }
                } else {
                    $_SESSION['error_msg'] = "Failed to upload image!";
                    header("Location: ../admin/company.php");
                    exit;
                }
            }

            $stmt = $pdo->prepare("
                UPDATE company SET 
                    company_name_en = :company_name_en,
                    company_name_bn = :company_name_bn,
                    company_image = :company_image,
                    about_company = :about_company
                WHERE id = :id
            ");
            $stmt->execute([
                ':company_name_en' => $company_name_en,
                ':company_name_bn' => $company_name_bn,
                ':company_image' => $image_name,
                ':about_company' => $about_company,
                ':id' => $id,
            ]);
            $_SESSION['success_msg'] = "Company updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Company not found!";
        }
    } else {
        $_SESSION['error_msg'] = "Required fields missing!";
    }

    header("Location: ../admin/company.php");
    exit;

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';

    if ($id) {
        $stmt = $pdo->prepare("SELECT company_image FROM company WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existing_company = $stmt->fetch();

        if ($existing_company) {
            $stmt = $pdo->prepare("DELETE FROM company WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $target_dir = '../company/';
            if (file_exists($target_dir . $existing_company['company_image'])) {
                unlink($target_dir . $existing_company['company_image']);
            }

            $_SESSION['success_msg'] = "Company deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Company not found!";
        }
    } else {
        $_SESSION['error_msg'] = "Invalid request!";
    }

    header("Location: ../admin/company.php");
    exit;

} else {
    $_SESSION['error_msg'] = "Invalid action!";
    header("Location: ../admin/company.php");
    exit;
}
