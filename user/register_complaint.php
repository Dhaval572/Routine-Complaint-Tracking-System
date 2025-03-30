<?php
include('../config.php'); 
// Note: session_start() is assumed to have been called in config.php.
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

// Fetch departments for complaint selection
$dept_sql = "SELECT * FROM departments";
$dept_result = $conn->query($dept_sql);

if (isset($_POST['register_complaint'])) {
    // Sanitize and retrieve inputs
    $title         = $conn->real_escape_string($_POST['title']);
    $description   = $conn->real_escape_string($_POST['description']);
    $incident_date = $_POST['incident_date'];
    $incident_time = $_POST['incident_time'];
    $dept_id       = $_POST['dept_id'];
    $consent       = isset($_POST['consent']) ? 1 : 0;

    // Target options: 'none', 'officer', or 'dept_head'
    $target_option = $_POST['target_option'];
    $target_id     = ($target_option != 'none' && !empty($_POST['target_id'])) ? $_POST['target_id'] : null;

    // File upload handling – ensure that the 'uploads' folder exists and is writable.
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $uploadDir  = "uploads/";
        $filename   = time() . "_" . basename($_FILES['attachment']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $attachment = $filename;
        }
    }

    // Determine the department head ID (if exists) for the selected department.
    $dept_head_id = null;
    $dh_sql = "SELECT id FROM users WHERE role = 'dept_head' AND department_id = '$dept_id' LIMIT 1";
    $dh_result = $conn->query($dh_sql);
    if ($dh_result && $dh_result->num_rows > 0) {
        $dh_row = $dh_result->fetch_assoc();
        $dept_head_id = $dh_row['id'];
    }

    // Insert complaint record. (Ensure your table includes an 'attachment' column if needed.)
    $citizen_id = $_SESSION['user_id'];
    $insert_sql = "INSERT INTO complaints 
        (citizen_id, department_id, title, description, attachment, officer_id, dept_head_id, target_id, target_role, incident_date, incident_time, created_at)
        VALUES ('$citizen_id', '$dept_id', '$title', '$description', " . ($attachment ? "'$attachment'" : "NULL") . ", NULL, '$dept_head_id', '$target_id', '$target_option', '$incident_date', '$incident_time', NOW())";
    
    if ($conn->query($insert_sql)) {
        $complaint_id = $conn->insert_id;
        // Log the activity for tracking purposes.
        $activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Complaint Registered', '$citizen_id')";
        $conn->query($activity_sql);
        $success = "Complaint registered successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Complaint</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #d32f2f;">
        <a class="navbar-brand font-weight-bold" href="#"><i class="fas fa-clipboard-list mr-2"></i>Register Complaint</a>
        <div class="ml-auto">
            <a href="user_dashboard.php" class="btn btn-outline-light"><i class="fas fa-home mr-1"></i>Dashboard</a>
        </div>
    </nav>

    <div class="container my-5 p-4 border rounded shadow-sm bg-white" style="max-width: 700px;">
        <h3 class="text-danger font-weight-bold border-bottom pb-2 mb-4">
            <i class="fas fa-pen-fancy mr-2"></i>Register Your Complaint
        </h3>
        <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Title -->
            <div class="form-group">
                <label for="title" class="font-weight-bold text-danger">Title</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-danger text-white">
                            <i class="fas fa-tag"></i>
                        </span>
                    </div>
                    <input type="text" name="title" id="title" required class="form-control" placeholder="Enter complaint title">
                </div>
            </div>
            <!-- Description -->
            <div class="form-group">
                <label for="description" class="font-weight-bold text-danger">Description</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-danger text-white">
                            <i class="fas fa-comment"></i>
                        </span>
                    </div>
                    <textarea name="description" id="description" required class="form-control" placeholder="Enter detailed description" rows="4"></textarea>
                </div>
            </div>
            <!-- Incident Date and Time -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="incident_date" class="font-weight-bold text-danger">Date of Incident</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-danger text-white">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="date" name="incident_date" id="incident_date" required class="form-control">
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="incident_time" class="font-weight-bold text-danger">Time of Incident</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-danger text-white">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <input type="time" name="incident_time" id="incident_time" required class="form-control">
                    </div>
                </div>
            </div>
            <!-- Department Selection -->
            <div class="form-group">
                <label for="dept_id" class="font-weight-bold text-danger">Select Department</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-danger text-white">
                            <i class="fas fa-building"></i>
                        </span>
                    </div>
                    <select name="dept_id" id="dept_id" required class="form-control">
                        <option value="">Select Department</option>
                        <?php while ($dept = $dept_result->fetch_assoc()) { ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <!-- Target Option -->
            <div class="form-group">
                <label class="font-weight-bold text-danger">Is this complaint against a specific officer or department head?</label><br>
                <div class="form-check form-check-inline">
                    <input type="radio" name="target_option" id="target_none" value="none" class="form-check-input" checked>
                    <label class="form-check-label" for="target_none">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="target_option" id="target_officer" value="officer" class="form-check-input">
                    <label class="form-check-label" for="target_officer">Officer</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="target_option" id="target_dept_head" value="dept_head" class="form-check-input">
                    <label class="form-check-label" for="target_dept_head">Dept Head</label>
                </div>
            </div>
            <!-- Target Section (loaded via AJAX) -->
            <div id="target_section" class="border-left pl-3 mb-3" style="display: none;">
                <div class="form-group">
                    <label for="target_dept" class="font-weight-bold text-danger">Select Target Department</label>
                    <select name="target_dept" id="target_dept" class="form-control">
                        <option value="">Select Department</option>
                        <?php 
                        // Re-run query for departments
                        $dept_result2 = $conn->query("SELECT * FROM departments");
                        while ($d = $dept_result2->fetch_assoc()) { ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="target_id" class="font-weight-bold text-danger">Select Target</label>
                    <select name="target_id" id="target_id" class="form-control">
                        <option value="">Select</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>
            </div>
            <!-- Attachment -->
            <div class="form-group">
                <label for="attachment" class="font-weight-bold text-danger">Attachment (if any)</label>
                <input type="file" name="attachment" id="attachment" class="form-control-file">
            </div>
            <!-- Consent -->
            <div class="form-group form-check">
                <input type="checkbox" name="consent" class="form-check-input" id="consentCheck" required>
                <label class="form-check-label text-danger" for="consentCheck">I give my consent to register this complaint.</label>
            </div>
            <button type="submit" name="register_complaint" class="btn btn-danger btn-block font-weight-bold">Register Complaint</button>
        </form>
    </div>

    <!-- jQuery AJAX for Target Section -->
    <script>
        $(document).ready(function () {
            $("#target_section").hide();

            $("input[name='target_option']").change(function () {
                if ($(this).val() == 'none') {
                    $("#target_section").slideUp();
                } else {
                    $("#target_section").slideDown();
                    loadTargets();
                }
            });

            $("select[name='target_dept']").change(function () {
                loadTargets();
            });

            function loadTargets() {
                var dept_id = $("select[name='target_dept']").val();
                var target_option = $("input[name='target_option']:checked").val();
                if (dept_id && target_option != 'none') {
                    $.ajax({
                        url: 'fetch_targets.php',
                        type: 'GET',
                        data: { dept_id: dept_id, target: target_option },
                        success: function (data) {
                            $("select[name='target_id']").html(data);
                        },
                        error: function () {
                            $("select[name='target_id']").html("<option value=''>Error loading targets</option>");
                        }
                    });
                } else {
                    $("select[name='target_id']").html("<option value=''>Select</option>");
                }
            }
        });
    </script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>