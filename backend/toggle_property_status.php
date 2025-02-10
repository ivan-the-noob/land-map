<?php
session_start();
require '../db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$propertyId = $data['property_id'] ?? null;
$currentStatus = $data['current_status'] ?? null;
$restrictDays = $data['days'] ?? null;
$reason = $data['reason'] ?? 'Administrative action'; // Add reason parameter

if (!$propertyId) {
    echo json_encode(['success' => false, 'message' => 'Property ID is required']);
    exit;
}

try {
    $conn->begin_transaction();

    if ($currentStatus === 'restricted') {
        // Unrestrict the property
        $sql = "UPDATE properties SET status = 'active', restriction_end = NULL WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $propertyId);
        
        // Create report for unrestriction
        $reportSql = "INSERT INTO reports (property_id, report_type, description, status, created_by) 
                     VALUES (?, 'unrestriction', ?, 'resolved', ?)";
        $reportStmt = $conn->prepare($reportSql);
        $description = "Property unrestricted by admin";
        $reportStmt->bind_param('isi', $propertyId, $description, $_SESSION['user_id']);
        $reportStmt->execute();
        
    } else {
        // Restrict the property
        $restrictionEnd = date('Y-m-d H:i:s', strtotime("+$restrictDays days"));
        $sql = "UPDATE properties SET status = 'restricted', restriction_end = ? WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $restrictionEnd, $propertyId);
        
        // Create report for restriction
        $reportSql = "INSERT INTO reports (property_id, report_type, description, status, created_by) 
                     VALUES (?, 'restriction', ?, 'active', ?)";
        $reportStmt = $conn->prepare($reportSql);
        $description = "Property restricted by admin. Reason: " . $reason;
        $reportStmt->bind_param('isi', $propertyId, $description, $_SESSION['user_id']);
        $reportStmt->execute();
    }

    if ($stmt->execute()) {
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

$conn->close();
?>

<div class="modal fade" id="restrictPropertyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restrict Property</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Restriction Duration (days)</label>
                    <input type="number" class="form-control" id="restrictionDays" min="1" value="7">
                </div>
                <div class="form-group">
                    <label>Reason for Restriction</label>
                    <textarea class="form-control" id="restrictionReason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRestriction()">Restrict Property</button>
            </div>
        </div>
    </div>
</div> 