<?php
include('../config.php'); // Change the include path to point to the parent directory
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
    echo "<div class='timeline-container p-2'>";
    while ($row = $result->fetch_assoc()) {
        $activity_time = date('M d, Y h:i A', strtotime($row['activity_time']));
        echo "<div class='timeline-item mb-3 p-3 rounded shadow-sm' style='background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 247, 255, 0.95) 100%); border: 1px solid rgba(41, 98, 255, 0.1); transition: all 0.3s ease;' onmouseover=\"this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(41, 98, 255, 0.15)'\" onmouseout=\"this.style.transform=''; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.08)'\">";
        echo "<div class='d-flex align-items-center mb-2'>";
        echo "<div class='rounded-circle d-flex align-items-center justify-content-center mr-3' style='width: 40px; height: 40px; background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); color: white;'><i class='fas fa-history'></i></div>";
        echo "<div>";
        echo "<h5 class='mb-0 font-weight-bold' style='color: #2962ff;'>" . htmlspecialchars($row['activity']) . "</h5>";
        echo "<div class='text-muted small'><i class='fas fa-user-circle mr-1'></i> " . htmlspecialchars($row['actor_name']) . "</div>";
        echo "</div>";
        echo "<div class='ml-auto text-right'>";
        echo "<span class='badge badge-pill px-3 py-2' style='background: rgba(41, 98, 255, 0.1); color: #1565c0;'><i class='far fa-clock mr-1'></i> " . $activity_time . "</span>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<div class='text-center py-5' style='background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%); border-radius: 15px;'>";
    echo "<div class='mb-4 text-primary mx-auto rounded-circle d-flex align-items-center justify-content-center' style='width: 80px; height: 80px; background: rgba(41, 98, 255, 0.15); font-size: 2.5rem;'>";
    echo "<i class='fas fa-history'></i>";
    echo "</div>";
    echo "<h5 class='text-primary mb-2'>No Activity Found</h5>";
    echo "<p class='text-muted'>There is no recorded activity for this complaint yet.</p>";
    echo "</div>";
}
?>