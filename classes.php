<?php 
include_once('includes/config.php');
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https:");

if(isset($_POST['submit'])){
    // Input validation and sanitization
    $fname = filter_input(INPUT_POST, 'fathername', FILTER_SANITIZE_STRING);
    $mname = filter_input(INPUT_POST, 'mothername', FILTER_SANITIZE_STRING);
    $pmobno = filter_input(INPUT_POST, 'parentmobno', FILTER_SANITIZE_STRING);
    $pemail = filter_input(INPUT_POST, 'parentemail', FILTER_SANITIZE_EMAIL);
    $cname = filter_input(INPUT_POST, 'cname', FILTER_SANITIZE_STRING);
    $agegroup = filter_input(INPUT_POST, 'agegroup', FILTER_SANITIZE_STRING);
    $erollprogram = filter_input(INPUT_POST, 'erollprogram', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $enrollno = mt_rand(100000000, 999999999);
    
    // Validate inputs
    $errors = [];
    
    if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $fname)) {
        $errors[] = "Invalid father name format";
    }
    
    if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $mname)) {
        $errors[] = "Invalid mother name format";
    }
    
    if (!preg_match('/^[0-9]{10}$/', $pmobno)) {
        $errors[] = "Mobile number must be 10 digits";
    }
    
    if (!filter_var($pemail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $cname)) {
        $errors[] = "Invalid child name format";
    }
    
    if (empty($agegroup) || !in_array($agegroup, ['2-3 Year', '3-4 Year', '4-5 Year', '5-6 Year'])) {
        $errors[] = "Invalid age group selection";
    }
    
    if (empty($erollprogram) || !in_array($erollprogram, ['Nursery-2.5 to 4 years', 'LKG-3.5 to 5 years', 'UKG- 4.5 to 6 years'])) {
        $errors[] = "Invalid program selection";
    }
    if (!preg_match('/^[\p{L}\p{N}\s\.,!?@#\-\(\)]{3,500}$/u', $message)) {
    $errors[] = "Message can only contain letters, numbers, spaces, and basic punctuation (. , ! ? @ # -)";
}
    if (empty($errors)) {
        // Prepared statement to prevent SQL injection
        $stmt = $con->prepare("INSERT INTO tblenrollment(enrollmentNumber, fatherName, motherName, parentmobNo, parentEmail, childName, childAge, enrollProgram, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $enrollno, $fname, $mname, $pmobno, $pemail, $cname, $agegroup, $erollprogram, $message);
        
        if ($stmt->execute()) {
            $_SESSION['form_success'] = true;
            echo "<script>alert('submitted successfully');</script>";
            header("Location: classes.php");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Balvatika APS School</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@600&family=Lobster+Two:wght@700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 py-lg-0">
            <a href="index.php" class="navbar-brand">
               <h1 class="m-0 text-primary">
                    <img src="img/logo.png" alt="Kider Logo" style="height: 120px; width: auto; margin-right: 10px;">
                    Balvatika APS Basoli
                </h1>
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.php" class="nav-item nav-link">About Us</a>
                    <a href="classes.php" class="nav-item nav-link">Classes</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Activities</a>
                        <div class="dropdown-menu rounded-0 rounded-bottom border-0 shadow-sm m-0">
                            <a href="facility.php" class="dropdown-item">School Facilities</a>
                            <a href="team.php" class="dropdown-item active">Glimpses of Student Life</a>
                            <a href="call-to-action.php" class="dropdown-item">Grow with Us</a>
                            <a href="appointment.php" class="dropdown-item">Make Appointment</a>
                            <a href="testimonial.php" class="dropdown-item">Rules and Regulations</a>
                           <a href="principaldesk.php" class="dropdown-item">Principal's Desk</a>
                        </div>
                    </div>
                    <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                    <a href="downloads.php" class="nav-item nav-link">Downloads</a>
                    <a href="gallery.php" class="nav-item nav-link">Gallery</a>
                </div>
               
            </div>
        </nav>
        <!-- Navbar End -->


        <!-- Page Header End -->
        <div class="container-xxl py-5 page-header position-relative mb-5">
            <div class="container py-5">
                <h1 class="display-2 text-white animated slideInDown mb-4">Classes</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Classes</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header End -->

        <!-- Classes Start -->
         <!-- Classes Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">School Classes</h1>
                    <p>At APPS, we offer a diverse range of classes designed to inspire creativity, critical thinking, and collaboration among students. Each class is tailored to provide a unique learning experience that fosters personal growth and academic excellence.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-1.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">Art & Craft</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                    
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-2.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">Color Management</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                  
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-3.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">Drawing</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                     
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-4.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">Language & Speaking</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                  
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-5.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">Religion & History</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                     
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="classes-item">
                            <div class="bg-light rounded-circle w-75 mx-auto p-3">
                                <img class="img-fluid rounded-circle" src="img/classes-6.jpg" alt="">
                            </div>
                            <div class="bg-light rounded p-4 pt-5 mt-n5">
                                <a class="d-block text-center h3 mt-3 mb-4" href="">General Knowledge</a>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                        
                                        <div class="ms-3">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">Details</span>
                                </div>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <div class="border-top border-3 border-primary pt-2">
                                            <h6 class="text-primary mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-success pt-2">
                                            <h6 class="text-success mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-top border-3 border-warning pt-2">
                                            <h6 class="text-warning mb-1"></h6>
                                            <small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Classes End -->
        <!-- Classes End -->        <!-- Appointment Start -->
        <?php if (isset($_SESSION['form_success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Message submitted successfully!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['form_success']);
endif; ?>

<?php if (isset($_SESSION['form_errors'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?php foreach ($_SESSION['form_errors'] as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['form_errors']);
endif; ?>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded">
                    <div class="row g-0">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="h-100 d-flex flex-column justify-content-center p-5">
                                <h1 class="mb-4">Start your Child’s Early Education(Enrollment)</h1>
                               <form method="post" action="classes.php">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="fathername" name="fathername" 
                                                       pattern="[A-Za-z\s]{3,50}" 
                                                       title="Only letters and spaces (3-50 characters)" 
                                                       required>
                                                <label for="fathername">Father Name</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="mothername" name="mothername" 
                                                       pattern="[A-Za-z\s]{3,50}" 
                                                       title="Only letters and spaces (3-50 characters)" 
                                                       required>
                                                <label for="mothername">Mother Name</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control border-0" id="parentmobno" name="parentmobno" 
                                                       pattern="[0-9]{10}" 
                                                       title="10 digit mobile number" 
                                                       required>
                                                <label for="parentmobno">Parents Mobile No.</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control border-0" id="parentemail" name="parentemail" 
                                                       required>
                                                <label for="parentemail">Parents Email</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="cname" name="cname" 
                                                       pattern="[A-Za-z\s]{3,50}" 
                                                       title="Only letters and spaces (3-50 characters)" 
                                                       required>
                                                <label for="cname">Child Name</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <select class="form-control" id="agegroup" name="agegroup" required>
                                                    <option value="">Select</option>
                                                    <option value="2-3 Year">2-3 Year</option>
                                                    <option value="3-4 Year">3-4 Year</option>
                                                    <option value="4-5 Year">4-5 Year</option>
                                                    <option value="5-6 Year">5-6 Year</option>
                                                </select>
                                                <label for="agegroup">Child Age</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-floating">
                                                <select class="form-control" id="erollprogram" name="erollprogram" required>
                                                    <option value="">Select a Program*</option>
                                                    <option value="Nursery-2.5 to 4 years">Nursery-2.5 to 4 years</option>
                                                    <option value="LKG-3.5 to 5 years">LKG-3.5 to 5 years</option>
                                                    <option value="UKG- 4.5 to 6 years">UKG- 4.5 to 6 years</option>
                                                </select>
                                                <label for="erollprogram">Program Enroll For</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control border-0" oninput="sanitizeMessage(this)" placeholder="Leave a message here" id="message" style="height: 100px" name="message" maxlength="500" required></textarea>
                                                <label for="message">Message</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 py-3" type="submit" name="submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s" style="min-height: 400px;">
                            <div class="position-relative h-100">
                                <img class="position-absolute w-100 h-100 rounded" src="img/appointment.jpg" style="object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- Appointment End -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Upcoming Events</h1>
            <p>Join us for exciting events that are fun and educational for all our students!</p>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
           
            <?php
            $result = $con->query("SELECT event_name, event_description, event_date FROM events ORDER BY event_date ASC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="testimonial-item bg-light rounded p-5">';
                    echo '<p class="fs-5">' . htmlspecialchars($row['event_description']) . '</p>';
                    echo '<div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">';
                    echo '<div class="ps-3">';
                    echo '<h3 class="mb-1">' . htmlspecialchars($row['event_name']) . '</h3>';
                    echo '<span>Date: ' . htmlspecialchars(date('F d, Y', strtotime($row['event_date']))) . '</span>';
                    echo '</div>';
                    echo '<i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No upcoming events available.</p>";
            }
            ?>
       
    </div>
    </div>
</div>

       

       
        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h3 class="text-white mb-4">Get In Touch</h3>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Balvatika APS Basoli, Ward no. 6, Near Army Camp, Dhanore (J&K) Pin-184152</p>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>9103913321</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>https://balvatikaapsbasoli.org/</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h3 class="text-white mb-4">Quick Links</h3>
                        <a class="btn btn-link text-white-50" href="about.php">About Us</a>
                        <a class="btn btn-link text-white-50" href="contact.php">Contact Us</a>
                        <a class="btn btn-link text-white-50" href="facility.php">Our Services</a>
                        <a class="btn btn-link text-white-50" href="testimonial.php">Rules and Regulation</a>
                        <a class="btn btn-link text-white-50" href="downloads.php">Downloads</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h3 class="text-white mb-4">Photo Gallery</h3>
                        <div class="row g-2 pt-2">
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-1.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-2.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-3.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-4.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-5.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-6.jpg" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h3 class="text-white mb-4">Few Words</h3>
                        <p>At APPS, we offer a diverse range of classes designed to inspire creativity, critical thinking, and collaboration among students. Each class is tailored to provide a unique learning experience that fosters personal growth and academic excellence.</p>
                        <div class="position-relative mx-auto" style="max-width: 400px;">
                  
                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="#">balvatikaapsbasoli.org</a>, All Right Reserved. 
							
							<!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
						<a class="border-bottom" href="https://htmlcodex.com"></a>
                            <br><a class="border-bottom" href="https://themewagon.com" target="_blank"></a>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu">
                                <a href="index.php">Home</a>
                                <a href="">Cookies</a>
                                <a href="contact.php">Help</a>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
<script>
function sanitizeMessage(textarea) {
    // Remove HTML tags from input in real-time
    textarea.value = textarea.value.replace(/<[^>]*>/g, '');
    
    // Optionally show remaining characters
    const remaining = 500 - textarea.value.length;
    document.getElementById('char-count').textContent = 
        `${remaining} characters remaining`;
}
</script>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>