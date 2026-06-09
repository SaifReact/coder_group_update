<?php
/**
 * Loan Schedule Generator
 * Generates and stores installment schedules when a loan is approved with SCDF method
 */

include_once __DIR__ . '/../config/config.php';

function create_loan_schedule_table() {
    global $pdo;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS loan_schedule (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT NOT NULL,
            member_code VARCHAR(50),
            product_code VARCHAR(50),
            loan_id INT NOT NULL UNIQUE,
            installment_no INT NOT NULL,
            principal_amt DECIMAL(15, 2),
            service_charge_amt DECIMAL(15, 2),
            verification_amt DECIMAL(15, 2),
            total_installment DECIMAL(15, 2),
            pay_date DATE,
            status ENUM('P', 'C', 'D') DEFAULT 'P' COMMENT 'P=Pending, C=Completed, D=Defaulted',
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_by INT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_member_id (member_id),
            KEY idx_loan_id (loan_id),
            KEY idx_pay_date (pay_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Error creating loan_schedule table: " . $e->getMessage());
        return false;
    }
}

function generate_loan_schedule($loan_id, $loan_amount, $months, $service_charge_total, $verification_amount, $member_id, $member_code, $product_code, $created_by) {
    global $pdo;
    
    try {
        // Calculate installment amounts
        $principal_base = floor($loan_amount / $months);
        $service_base = floor($service_charge_total / $months);
        $verification_base = floor($verification_amount / $months);
        
        $total_principal = 0;
        $total_service = 0;
        $total_verification = 0;
        $start_date = new DateTime('now');
        
        // Insert schedule rows
        for ($i = 1; $i <= $months; $i++) {
            $principal = ($i === $months) ? ($loan_amount - $total_principal) : $principal_base;
            $total_principal += $principal;
            
            $service_charge = ($i === $months) ? ($service_charge_total - $total_service) : $service_base;
            $total_service += $service_charge;
            
            $verification = ($i === $months) ? ($verification_amount - $total_verification) : $verification_base;
            $total_verification += $verification;
            
            $total_installment = $principal + $service_charge + $verification;
            
            // Calculate pay date (add months to start date)
            $pay_date = clone $start_date;
            $pay_date->modify('+' . $i . ' month');
            
            $stmt = $pdo->prepare("
                INSERT INTO loan_schedule (member_id, member_code, product_code, loan_id, installment_no, principal_amt, service_charge_amt, verification_amt, total_installment, pay_date, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $member_id,
                $member_code,
                $product_code,
                $loan_id,
                $i,
                $principal,
                $service_charge,
                $verification,
                $total_installment,
                $pay_date->format('Y-m-d'),
                $created_by
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error generating loan schedule: " . $e->getMessage());
        return false;
    }
}

// If called directly as AJAX
if (isset($_POST['action']) && $_POST['action'] === 'generate_schedule') {
    $loan_id = (int)$_POST['loan_id'];
    $member_id = (int)$_POST['member_id'];
    
    try {
        // Fetch loan details
        $stmt = $pdo->prepare("SELECT * FROM loan_application WHERE id = ? LIMIT 1");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$loan) {
            echo json_encode(['success' => false, 'message' => 'Loan not found']);
            exit;
        }
        
        // Fetch product info for installment method
        $stmt = $pdo->prepare("SELECT installment_measurement_method FROM loan_info WHERE product_code = ? LIMIT 1");
        $stmt->execute([$loan['product_code']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || $product['installment_measurement_method'] !== 'SCDF') {
            echo json_encode(['success' => false, 'message' => 'Product does not use SCDF method']);
            exit;
        }
        
        // Create table if not exists
        create_loan_schedule_table();
        
        // Delete existing schedule if any
        $stmt = $pdo->prepare("DELETE FROM loan_schedule WHERE loan_id = ?");
        $stmt->execute([$loan_id]);
        
        // Generate new schedule
        $created_by = $_SESSION['user_id'] ?? $loan['created_by'];
        $result = generate_loan_schedule(
            $loan_id,
            $loan['loan_amount'],
            $loan['duration'],
            $loan['service_charge'],
            $loan['verification_charge'],
            $loan['member_id'],
            $loan['member_code'],
            $loan['product_code'],
            $created_by
        );
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Loan schedule generated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate schedule']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>
