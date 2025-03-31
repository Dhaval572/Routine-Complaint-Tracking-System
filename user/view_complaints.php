<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
  header("Location: user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$records_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of complaints for pagination
$total_query = "SELECT COUNT(*) as total FROM complaints WHERE citizen_id = '$user_id'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get complaints with pagination
$sql = "SELECT c.*, d.name as dept_name 
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id 
        WHERE c.citizen_id = '$user_id' 
        ORDER BY c.created_at DESC
        LIMIT $offset, $records_per_page";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function loadActivity(complaint_id) {
      $.ajax({
        url: 'fetch_activity.php',
        type: 'GET',
        data: { complaint_id: complaint_id },
        success: function (data) {
          $("#activityContent").html(data);
          $("#activityModal").modal('show');
        }
      });
    }
  </script>
</head>

<body class="min-vh-100" style="background: linear-gradient(135deg, #1a3a8f 0%, #0d2b6b 100%);">
  <!-- Animated floating blobs using Bootstrap utilities -->
  <div class="position-fixed w-100 h-100" style="z-index: -1;">
    <div class="position-absolute rounded-circle" style="width: 400px; height: 400px; background: rgba(100, 181, 246, 0.15); filter: blur(60px); top: 20%; left: 10%; animation: float 12s infinite;"></div>
    <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(66, 165, 245, 0.12); filter: blur(60px); top: 50%; right: 15%; animation: float 12s infinite 4s;"></div>
    <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(144, 202, 249, 0.15); filter: blur(60px); bottom: 10%; left: 30%; animation: float 12s infinite 8s;"></div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark shadow-lg mx-md-4 mx-2" style="
      background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
      border-radius: 20px;
      margin-top: 15px;
      padding: 12px 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 10px rgba(0, 0, 0, 0.05);
    ">
      <div class="container">
        <span class="navbar-brand d-flex align-items-center">
          <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-primary mr-2" style="width: 38px; height: 38px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <span class="font-weight-bold ml-1" style="letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">My Complaints</span>
        </span>
        <div class="ml-auto">
          <a href="user_dashboard.php" class="btn rounded-pill px-4 shadow-sm" style="
            background: rgba(255, 255, 255, 0.15); 
            color: white; 
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
          " onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'">
            <i class="fas fa-home mr-2"></i>Dashboard
          </a>
        </div>
      </div>
    </nav>

  <div class="container py-5">
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden" style="
        border-radius: 30px !important; 
        backdrop-filter: blur(20px); 
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 8px 24px -4px rgba(41, 98, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.5);
      ">
      <div class="card-header border-0 py-4" style="
          background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
          border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        ">
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3" style="
              width: 50px; 
              height: 50px; 
              background: rgba(255, 255, 255, 0.2); 
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.2);
              border-radius: 15px;
            ">
            <i class="fas fa-list-alt"></i>
          </div>
          <h4 class="mb-0 text-white font-weight-bold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">Your Registered Complaints</h4>
        </div>
      </div>

      <div class="card-body p-md-4 p-3" style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);">
        <?php if ($result->num_rows > 0) { ?>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr style="background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); border-radius: 15px; color: white;">
                  <th class="py-3 pl-4 border-0" style="border-top-left-radius: 15px; border-bottom-left-radius: 15px;">ID</th>
                  <th class="py-3 border-0">Title</th>
                  <th class="py-3 border-0 d-none d-md-table-cell">Department</th>
                  <th class="py-3 border-0">Status</th>
                  <th class="py-3 border-0 d-none d-lg-table-cell">Referred To</th>
                  <th class="py-3 border-0 d-none d-md-table-cell">Created</th>
                  <th class="py-3 pr-4 border-0" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()) {
                  $referred_to = ($row['target_id'] && $row['target_role'] != 'none') ? ucfirst($row['target_role']) : 'Dept Head';
                  $status_class = $row['status'] == 'solved' ? 'badge-success' : ($row['status'] == 'pending' ? 'badge-warning' : 'badge-info');
                  $created_date = date('M d, Y', strtotime($row['created_at']));
                ?>
                  <tr class="border-0 shadow-sm mb-3 transition-all" style="
                      border-radius: 15px; 
                      background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 247, 255, 0.95) 100%);
                      margin-bottom: 15px;
                      box-shadow: 0 4px 12px rgba(41, 98, 255, 0.08);
                      border: 1px solid rgba(41, 98, 255, 0.05);
                    " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(41, 98, 255, 0.15)'" 
                    onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 12px rgba(41, 98, 255, 0.08)'">
                    <td class="py-3 pl-4 font-weight-bold" style="color: #2962ff;">#<?php echo $row['id']; ?></td>
                    <td class="py-3 font-weight-medium"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td class="py-3 d-none d-md-table-cell">
                      <span class="badge badge-pill px-3 py-2" style="
                          background: linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%); 
                          color: #1565c0;
                          border: 1px solid rgba(41, 98, 255, 0.1);
                        ">
                        <i class="fas fa-building mr-1 text-primary"></i>
                        <?php echo htmlspecialchars($row['dept_name']); ?>
                      </span>
                    </td>
                    <td class="py-3">
                      <span class="badge badge-pill <?php echo $status_class; ?> px-3 py-2" style="box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
                        <?php echo ucfirst($row['status']); ?>
                      </span>
                    </td>
                    <td class="py-3 d-none d-lg-table-cell">
                      <span class="badge badge-pill px-3 py-2" style="
                          background: linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%); 
                          color: #1565c0;
                          border: 1px solid rgba(41, 98, 255, 0.1);
                        ">
                        <i class="fas fa-user-tie mr-1 text-primary"></i>
                        <?php echo $referred_to; ?>
                      </span>
                    </td>
                    <td class="py-3 text-muted d-none d-md-table-cell">
                      <i class="far fa-calendar-alt mr-1 text-primary"></i>
                      <?php echo $created_date; ?>
                    </td>
                    <td class="py-3 pr-4">
                      <button class="btn btn-sm rounded-pill shadow-sm" style="
                          background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
                          color: white;
                          border: none;
                          transition: all 0.3s ease;
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(41, 98, 255, 0.3)'" 
                        onmouseout="this.style.transform=''; this.style.boxShadow=''" 
                        onclick="loadActivity(<?php echo $row['id']; ?>)">
                        <i class="fas fa-history mr-1"></i> <span class="d-none d-md-inline">Activity</span>
                      </button>
                      <?php if ($row['status'] == 'solved') { ?>
                        <a href="feedback.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm rounded-pill shadow-sm ml-1" style="
                            transition: all 0.3s ease;
                          " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(40, 167, 69, 0.3)'" 
                          onmouseout="this.style.transform=''; this.style.boxShadow=''">
                          <i class="fas fa-star mr-1"></i> <span class="d-none d-md-inline">Feedback</span>
                        </a>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination Controls -->
          <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
              <span class="text-muted">
                Showing <?= min($offset + 1, $total_records) ?> to <?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> complaints
              </span>
              <nav aria-label="Complaints pagination">
                <ul class="pagination pagination-sm mb-0">
                  <!-- Previous button -->
                  <?php if ($page > 1): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill mr-2 shadow-sm" href="?page=<?= $page - 1 ?>" 
                         style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%);
                                color: #1565c0; border: 1px solid rgba(41, 98, 255, 0.1); transition: all 0.3s ease;"
                         onmouseover="this.style.background='rgba(41, 98, 255, 0.2)'" 
                         onmouseout="this.style.background='linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%)'">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                  
                  <?php
                  // Optimized page number display
                  $pageStyle = function($pageNum) use ($page) {
                    $isActive = $page == $pageNum;
                    $bg = $isActive ? 'linear-gradient(135deg, #2962ff 0%, #1565c0 100%)' : 'linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%)';
                    $color = $isActive ? 'white' : '#1565c0';
                    return "background: $bg; color: $color; border: 1px solid rgba(41, 98, 255, 0.1); transition: all 0.3s ease;";
                  };
                  
                  // Calculate visible page range
                  $start_page = max(1, $page - 1);
                  $end_page = min($total_pages, $page + 1);
                  
                  // First page
                  if ($start_page > 1): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill mr-2 shadow-sm" href="?page=1" style="<?= $pageStyle(1) ?>">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                      <li class="page-item disabled"><span class="page-link border-0 bg-transparent">...</span></li>
                    <?php endif; ?>
                  <?php endif; ?>
                  
                  <!-- Page numbers -->
                  <?php for ($i = $start_page; $i <= $end_page; $i++): 
                    if ($i == 1 || $i == $total_pages) continue; // Skip first and last page as they're handled separately
                  ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill mr-2 shadow-sm" href="?page=<?= $i ?>" style="<?= $pageStyle($i) ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                  
                  <!-- Last page -->
                  <?php if ($end_page < $total_pages): 
                    if ($end_page < $total_pages - 1): ?>
                      <li class="page-item disabled"><span class="page-link border-0 bg-transparent">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill mr-2 shadow-sm" href="?page=<?= $total_pages ?>" style="<?= $pageStyle($total_pages) ?>"><?= $total_pages ?></a>
                    </li>
                  <?php endif; ?>
                  
                  <!-- Next button -->
                  <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill shadow-sm" href="?page=<?= $page + 1 ?>" 
                         style="background: linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%);
                                color: #1565c0; border: 1px solid rgba(41, 98, 255, 0.1); transition: all 0.3s ease;"
                         onmouseover="this.style.background='rgba(41, 98, 255, 0.2)'" 
                         onmouseout="this.style.background='linear-gradient(135deg, rgba(41, 98, 255, 0.1) 0%, rgba(21, 101, 192, 0.1) 100%)'">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          <?php endif; ?>
          
          <!-- View All Option -->
          <div class="text-center mt-3">
            <a href="view_all_complaints.php" class="btn btn-sm rounded-pill shadow-sm px-4" style="
                background: linear-gradient(135deg, rgba(41, 98, 255, 0.15) 0%, rgba(21, 101, 192, 0.15) 100%);
                color: #1565c0;
                border: 1px solid rgba(41, 98, 255, 0.1);
                transition: all 0.3s ease;
              " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(41, 98, 255, 0.2)'" 
              onmouseout="this.style.transform=''; this.style.boxShadow=''">
              <i class="fas fa-list-alt mr-2"></i>View All Complaints
            </a>
          </div>
        <?php } else { ?>
          <div class="text-center py-5 my-3" style="
              background: linear-gradient(135deg, rgba(230, 240, 255, 0.95), rgba(210, 230, 255, 0.95));
              border-radius: 20px;
              box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(41, 98, 255, 0.1);
              border: 1px solid rgba(41, 98, 255, 0.2);
              backdrop-filter: blur(10px);">
            <div class="mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="
                width: 100px;
                height: 100px;
                background: linear-gradient(135deg, rgba(41, 98, 255, 0.2), rgba(21, 101, 192, 0.2));
                font-size: 3rem;
                color: #1565c0;
                box-shadow: 0 10px 25px rgba(41, 98, 255, 0.15), inset 0 5px 10px rgba(255, 255, 255, 0.5);
                border: 1px solid rgba(41, 98, 255, 0.2);">
              <i class="fas fa-clipboard-list"></i>
            </div>
            <h4 class="text-primary mb-3 font-weight-bold">No Complaints Found</h4>
            <p class="text-muted mb-4 px-4" style="max-width: 500px; margin: 0 auto;">You haven't registered any complaints yet. If you're experiencing any issues that need attention, please register a new complaint using the button below.</p>
            <div class="border-top border-bottom py-3 my-3" style="border-color: rgba(41, 98, 255, 0.15) !important;">
              <p class="text-primary mb-0"><i class="fas fa-info-circle mr-2"></i>Your registered complaints will appear here for easy tracking and management</p>
            </div>
            <a href="register_complaint.php" class="btn btn-primary rounded-pill px-5 py-2 shadow" style="
                background: linear-gradient(135deg, #2962ff, #1565c0);
                border: none;
                font-weight: 600;
                transition: all 0.3s ease;
                onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(41, 98, 255, 0.3)'"
                onmouseout="this.style.transform=''; this.style.boxShadow=''">
              <i class="fas fa-plus-circle mr-2"></i>Register New Complaint
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- Modal with Bootstrap classes -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content border-0 shadow rounded-lg" style="border-radius: 20px !important; backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.98);">
        <div class="modal-header text-white border-0 rounded-top" style="background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>Complaint Activity Timeline
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4" id="activityContent" style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);">
          <!-- AJAX content loaded here -->
        </div>
      </div>
    </div>
  </div>

  <style>
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
    
    .transition-all {
      transition: all 0.3s ease;
    }
    
    tr.transition-all:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(13, 110, 253, 0.1) !important;
    }
    
    @media (max-width: 768px) {
      .card-header h4 {
        font-size: 1.25rem;
      }
    }
  </style>
</body>
</html>