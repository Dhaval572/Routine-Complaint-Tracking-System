<?php
include('../config.php');

if (!isset($_GET['complaint_id'])) {
    echo "Invalid request.";
    exit;
}

$complaint_id = intval($_GET['complaint_id']);

// Query: Join complaint_activity with complaints and fetch additional details.
$sql = "SELECT ca.*, 
         u.name as actor_name, 
         c.officer_id, 
         c.dept_head_id,
         (SELECT name FROM users WHERE id = c.officer_id) as assigned_officer_name,
         (SELECT name FROM users WHERE id = c.dept_head_id) as dept_head_name
        FROM complaint_activity ca
        LEFT JOIN users u ON ca.activity_by = u.id
        LEFT JOIN complaints c ON ca.complaint_id = c.id
        WHERE ca.complaint_id = '$complaint_id'
        ORDER BY ca.activity_time ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        $activity = trim($row['activity']);
        $time = $row['activity_time'];
        $actor = htmlspecialchars($row['actor_name']);
        $message = "";
        // Customize messages based on the activity.
        if ($activity == "Complaint Registered") {
            $message = "<strong>Complaint Registered</strong> by <strong>{$actor}</strong> at <strong>{$time}</strong>";
        } elseif ($activity == "Assigned to Officer") {
            // For assignment, assume the actor is the dept head.
            $dept_head = htmlspecialchars($row['dept_head_name']);
            $assignedOfficer = htmlspecialchars($row['assigned_officer_name']);
            $message = "<strong>Assigned complaint to Officer {$assignedOfficer}</strong> by <strong>Dept Head {$dept_head}</strong> at <strong>{$time}</strong>";
        } elseif ($activity == "Complaint Referred to Officer") {
            // For referral, the actor is the referring officer.
            $assignedOfficer = htmlspecialchars($row['assigned_officer_name']);
            $message = "<strong>Referred complaint to Officer {$assignedOfficer}</strong> by <strong>{$actor}</strong> at <strong>{$time}</strong>";
        } elseif ($activity == "Complaint Solved by Officer") {
            $assignedOfficer = htmlspecialchars($row['assigned_officer_name']);
            $message = "<strong>Complaint Solved by Officer {$assignedOfficer}</strong> at <strong>{$time}</strong> by <strong>{$actor}</strong>";
        } else {
            // Fallback: show the raw activity.
            $message = "<strong>{$activity}</strong> by <strong>{$actor}</strong> at <strong>{$time}</strong>";
        }
        echo "<li class='list-group-item'>{$message}</li>";
    }
    echo "</ul>";
} else {
    echo "<p><strong>No activity found for this complaint.</strong></p>";
}
?>
