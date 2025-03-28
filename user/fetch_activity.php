<?php
include('config.php');
if (!isset($_GET['complaint_id'])) {
    echo "Invalid request.";
    exit;
}
$complaint_id = intval($_GET['complaint_id']);
$sql = "SELECT ca.*, u.name as actor_name FROM complaint_activity ca 
        LEFT JOIN users u ON ca.activity_by = u.id 
        WHERE ca.complaint_id = '$complaint_id'
        ORDER BY ca.activity_time ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>";
        echo "<strong>" . htmlspecialchars($row['activity']) . "</strong> by " . htmlspecialchars($row['actor_name']) . " at " . $row['activity_time'];
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "No activity found for this complaint.";
}
?>