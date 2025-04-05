<?php
include('../config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Officer ID is required']);
    exit;
}

$officer_id = intval($_POST['id']);

// Get officer details before deletion (for signature file deletion)
$query = "SELECT u.*, s.signature_filename FROM users u 
          LEFT JOIN signatures s ON s.user_id = u.id 
          WHERE u.id = ? AND u.role = 'officer'";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $officer_id);
$stmt->execute();
$result = $stmt->get_result();
$officer = $result->fetch_assoc();

if (!$officer) {
    echo json_encode(['success' => false, 'message' => 'Officer not found']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete signature if exists
    if (!empty($officer['signature_filename'])) {
        $stmt = $conn->prepare("DELETE FROM signatures WHERE user_id = ?");
        $stmt->bind_param('i', $officer_id);
        $stmt->execute();
        
        // Delete signature file
        $signature_path = "../signatures/" . $officer['signature_filename'];
        if (file_exists($signature_path)) {
            unlink($signature_path);
        }
    }
    
    // Delete the officer
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'officer'");
    $stmt->bind_param('i', $officer_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("No officer was deleted");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>