<?php
// Start session FIRST with proper settings
session_set_cookie_params(
    1800,                // lifetime
    '/; samesite=Strict',// path + samesite
    $_SERVER['HTTP_HOST'], // domain
    true,                // secure
    true                 // httponly
);

ini_set('session.gc_maxlifetime', 1800);
session_start();

// Then set these after session starts
$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['created'] = time();

require_once 'includes/config.php';

// Validation (improved check)
if(empty($_SESSION['aid'])) {
    header('location:index.php');
    exit();
}


include('includes/config.php'); // Database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'];
    $eventDescription = $_POST['event_description'];
    $eventDate = $_POST['event_date'];
    if (!preg_match('/^[A-Za-z ]+$/', $eventDescription)) {
    die("Invalid description - only letters and spaces allowed");
}
    // Insert event data into the database
    $stmt = $con->prepare("INSERT INTO events (event_name, event_description, event_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $eventName, $eventDescription, $eventDate);

    if ($stmt->execute()) {
        echo "Event added successfully!";
    } else {
        echo "Failed to add event.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balvatika APS Basoli</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
  
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <?php include_once("includes/navbar.php"); ?>
    <!-- /.navbar -->

    <?php include_once("includes/sidebar.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                      
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                   
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
            <h1>Upload Event</h1>
    <form action="admin_upload_event.php" method="POST">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" pattern="[A-Za-z ]+" required><br><br>
        
        <label for="event_description">Event Description:</label>
        <textarea type="text" name="event_description" id="event_description" required></textarea><br><br>
        <script>
            function validateForm() {
    const description = document.getElementById('event_description').value;
    const regex = /^[A-Za-z ]+$/;
    
    if (!regex.test(description)) {
        alert('Event description can only contain letters and spaces');
        return false;
    }
    return true;
}
            
        </script>
        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" required><br><br>
        
        <button type="submit">Add Event</button>
    </form>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php include_once('includes/footer.php'); ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->


<!-- AdminLTE App -->

<!-- Page specific script -->
<script>

    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
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
</script>
</body>
</html>
