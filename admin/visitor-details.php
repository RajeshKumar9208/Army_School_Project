<?php 
session_start();
error_reporting(0);

// Database Connection
include('includes/config.php');

// Validating Session
if(strlen($_SESSION['aid'])==0) { 
    header('location:index.php');
    exit();
} else {
    // Code For Updating the visitor with security fixes
    if(isset($_POST['submit'])){
        $vid = intval($_GET['vid']);
        $estatus = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');
        $oremark = htmlspecialchars($_POST['officialremak'], ENT_QUOTES, 'UTF-8');
        
        // Use prepared statement to prevent SQL injection
        $query = $con->prepare("UPDATE tblvisitor SET officeRemark=?, status=? WHERE id=?");
        $query->bind_param("ssi", $oremark, $estatus, $vid);
        $query->execute();
        
        if($query->affected_rows > 0){
            echo "<script>alert('Visitor Status updated successfully.');</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
        $query->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Balvatika APS Basoli</title>
  
  <!-- Security Headers -->
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; img-src 'self' data:;">
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="X-Frame-Options" content="DENY">
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include_once("includes/navbar.php");?>
  <!-- /.navbar -->

  <?php include_once("includes/sidebar.php");?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>New Enrollments</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Visitor Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Visitor Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <tbody>
                    <?php 
                    $vid = intval($_GET['vid']);
                    $query = $con->prepare("SELECT * FROM tblvisitor WHERE id=?");
                    $query->bind_param("i", $vid);
                    $query->execute();
                    $result = $query->get_result();
                    $cnt = 1;
                    
                    while($row = $result->fetch_assoc()){
                    ?>
                    <tr>
                      <th>Guardian Name</th>
                      <td><?php echo htmlspecialchars($row['gurdianName'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <th>Guardian Email</th>
                      <td><?php echo htmlspecialchars($row['gurdianEmail'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                      <th>Child Name</th>
                      <td><?php echo htmlspecialchars($row['childName'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <th>Child Age</th>
                      <td><?php echo htmlspecialchars($row['childAge'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                      <th>Visit Time</th>
                      <td><?php echo htmlspecialchars($row['visitTime'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <th>Posting Date</th>
                      <td><?php echo htmlspecialchars($row['postingDate'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                      <th>Message</th>
                      <td colspan="3"><?php echo htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>

                    <?php if(!empty($row['status'])): ?>
                    <tr>
                      <th>Visit Status</th>
                      <td><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <th>Updation Date</th>
                      <td><?php echo htmlspecialchars($row['updationDate'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                      <th>Official Remark</th>
                      <td colspan="3"><?php echo htmlspecialchars($row['officeRemark'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(empty($row['status'])): ?>
                    <tr>
                      <td colspan="4" style="text-align:center;">
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Take Action</button>
                      </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php 
                    $cnt++;
                    }
                    $query->close();
                    ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include_once('includes/footer.php');?>
</div>
<!-- ./wrapper -->

<!-- Update Status Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Update Visitor Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form name="takeaction" method="post" onsubmit="return validateForm()">
          <div class="form-group">
            <select class="form-control" name="status" required>
              <option value="">Select Visitor Status</option>
              <option value="Visited">Visited</option>
              <option value="Not-Visited">Not Visited</option>
            </select>
          </div>
          <div class="form-group">
            <textarea type="text" class="form-control" name="officialremak" placeholder="Official Remark" rows="5" pattern="[A-Za-z]{14}" required readonly value="official remarks"></textarea>
          </div>
          <input type="submit" class="btn btn-primary" name="submit" value="Update">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
  
  function validateForm() {
    var status = document.forms["takeaction"]["status"].value;
    var remark = document.forms["takeaction"]["officialremak"].value;
    
    // Basic validation
    if (status == "") {
      alert("Please select visitor status");
      return false;
    }
    if (remark == "") {
      alert("Please enter official remark");
      return false;
    }
    
    return true;
  }
</script>
</body>
</html>
<?php } ?>