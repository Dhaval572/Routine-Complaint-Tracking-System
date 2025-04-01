<?php
include('..\config.php');

if (!isset($_GET['complaint_id'])) {
    die("Complaint ID is required.");
}

$complaint_id = $_GET['complaint_id'];

// Fetch complaint details along with the officer's signature
$sql = "SELECT c.*, d.name as dept_name, u.name as citizen_name, 
               o.name as officer_name, s.signature_filename
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id
        LEFT JOIN users u ON c.citizen_id = u.id
        LEFT JOIN users o ON c.officer_id = o.id
        LEFT JOIN signatures s ON s.user_id = o.id
        WHERE c.id = '$complaint_id'";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Complaint not found.");
}

$complaint = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complaint Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        h1 {
            font-size: 24px;
        }

        h2 {
            font-size: 20px;
            margin-top: 10px;
        }

        h3 {
            font-size: 18px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }

        img.logo {
            width: 100px;
            margin-bottom: 20px;
        }

        .signature-block {
            text-align: right;
            margin-top: 50px;
            margin-right: 50px;
        }

        .signature {
            width: 150px;
            display: block;
            margin-bottom: 5px;
        }

        .print-btn {
            margin: 20px;
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <img class="logo" src="../assets/emblem.png" alt="Logo">
    <h1><strong>Govt of Gujarat</strong></h1>
    <h2>Complaint Report</h2>

    <h3>Citizen Details</h3>
    <table>
        <tr>
            <th>Name</th>
            <td><?= htmlspecialchars($complaint['citizen_name']) ?></td>
        </tr>
    </table>

    <h3>Complaint Details</h3>
    <table>
        <tr>
            <th>Complaint ID</th>
            <td><?= htmlspecialchars($complaint['id']) ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= htmlspecialchars($complaint['title']) ?></td>
        </tr>
        <tr>
            <th>Department</th>
            <td><?= htmlspecialchars($complaint['dept_name']) ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?= nl2br(htmlspecialchars($complaint['description'])) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= ucfirst(htmlspecialchars($complaint['status'])) ?></td>
        </tr>
    </table>

    <h3>Response</h3>
    <table>
        <tr>
            <th>AI Summary (Response)</th>
            <td><?= nl2br(htmlspecialchars($complaint['ai_summary_response'])) ?></td>
        </tr>
        <tr>
            <th>Solved By</th>
            <td><?= htmlspecialchars($complaint['officer_name']) ?></td>
        </tr>
        <tr>
            <th>Referred To</th>
            <td><?= htmlspecialchars($complaint['target_role'] ?? "N/A") ?></td>
        </tr>
    </table>

    <h3>Activity Log</h3>
    <table>
        <tr>
            <th>Timestamp</th>
            <th>Action</th>
        </tr>
        <?php
        // Fetch complaint activity log
        $activity_sql = "SELECT * FROM complaint_activity WHERE complaint_id = '$complaint_id' ORDER BY activity_time ASC";
        $activity_result = $conn->query($activity_sql);
        if ($activity_result->num_rows > 0) {
            while ($activity = $activity_result->fetch_assoc()) {
                echo "<tr><td>" . $activity['activity_time'] . "</td><td>" . htmlspecialchars($activity['activity']) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No activity recorded</td></tr>";
        }
        ?>
    </table>

    <!-- Signature at the bottom right -->
    <div class="signature-block">
        <?php if (!empty($complaint['signature_filename'])): ?>
            <img class="signature" src="../signatures/<?= htmlspecialchars($complaint['signature_filename']) ?>" alt="Officer Signature">
        <?php else: ?>
            <p>No signature available.</p>
        <?php endif; ?>
        <p><strong>Sign,</strong></p>
        <p><strong><?= htmlspecialchars($complaint['officer_name']) ?></strong></p>
        <p>Date & Time: <?= htmlspecialchars($complaint['updated_at'] ?? "N/A") ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?= date("Y") ?> Complaint Management System</p>
    </div>

    <button class="print-btn" onclick="window.print()">Print Report</button>

</body>

</html>