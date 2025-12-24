<?php
// process/meeting_process.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// ACTION: Rely only on POST as the form uses POST method.
$action = $_POST['action'] ?? '';

if ($action === 'insert') {
    // Collect and sanitize data
    $meeting_date = $_POST['meeting_date'] ?? '';
    $place = $_POST['meeting_place'] ?? '';
    $agenda = $_POST['meeting_agenda'] ?? '';
    $decision = $_POST['meeting_decision'] ?? '';
    $presided_by = $_POST['presided_by'] ?? '';
    
    // Member list
    $members = $_POST['meeting_members'] ?? [];
    $members_json = json_encode($members, JSON_UNESCAPED_UNICODE);

    try {
        // SQL prepared statement for secure insertion
        $stmt = $pdo->prepare("INSERT INTO meeting (mdate, place, agenda, decision, members, presided_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$meeting_date, $place, $agenda, $decision, $members_json, $presided_by]);
        
        $_SESSION['success_msg'] = "✅ Meeting Added Successfully..! (সফলভাবে যোগ করা হয়েছে..!)";
    } catch (Exception $e) {
        // Log the error for debugging, display a user-friendly message
        error_log('Error saving meeting: ' . $e->getMessage());
        $_SESSION['error'] = 'Error saving meeting. Please try again or check logs.';
    }
    
    header('Location: ../admin/meeting.php');
    exit;
    
} else {
    // No valid action provided, redirect
    header('Location: ../admin/meeting.php');
    exit;
}