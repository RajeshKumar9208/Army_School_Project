<?php 
session_start(); // Make sure session is started
include_once('includes/config.php');

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process form submission
if(isset($_POST['submit'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    // Validate and sanitize inputs
    $gname = htmlspecialchars(strip_tags($_POST['gname']), ENT_QUOTES, 'UTF-8');
    $emailid = filter_var($_POST['emailid'], FILTER_SANITIZE_EMAIL);
    $cname = htmlspecialchars(strip_tags($_POST['cname']), ENT_QUOTES, 'UTF-8');
    $cage = htmlspecialchars(strip_tags($_POST['agegroup']), ENT_QUOTES, 'UTF-8');
    $vtime = htmlspecialchars(strip_tags($_POST['visittime']), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(strip_tags($_POST['message']), ENT_QUOTES, 'UTF-8');

    // Additional validation
    if (!filter_var($emailid, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if (!preg_match("/^[a-zA-Z ]*$/", $gname)) {
        die("Only letters and white space allowed in guardian name");
    }

    if (!preg_match("/^[a-zA-Z ]*$/", $cname)) {
        die("Only letters and white space allowed in child name");
    }

    if (!in_array($cage, ['2-3 Year', '3-4 Year', '4-5 Year', '5-6 Year'])) {
        die("Invalid age group selected");
    }

    // Validate datetime format
    if (!DateTime::createFromFormat('Y-m-d\TH:i', $vtime)) {
        die("Invalid date/time format");
    }

    // Prepare SQL statement to prevent SQL injection
    $query = $con->prepare("INSERT INTO tblvisitor(gurdianName, gurdianEmail, childName, childAge, message, visitTime) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssss", $gname, $emailid, $cname, $cage, $message, $vtime);
    
    if($query->execute()){
        echo "<script>alert('Details sent successfully.');</script>";
        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Balvatika APS Basoli</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <!-- Security Headers -->
   
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">

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
                <h1 class="display-2 text-white animated slideInDown mb-4">Appointment</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Appointment</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header End -->


        <!-- Appointment Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded">
                    <div class="row g-0">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="h-100 d-flex flex-column justify-content-center p-5">
                                <h1 class="mb-4">Make Appointment</h1>
                                <form method="post" id="appointmentForm" onsubmit="return validateForm()">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="gname" name="gname" placeholder="Guardian Name" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed" maxlength="50">
                                                <label for="gname">Guardian Name</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control border-0" id="emailid" name="emailid" placeholder="Guardian Email" required maxlength="100">
                                                <label for="gmail">Guardian Email</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="cname" name="cname" placeholder="Child Name" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed" maxlength="50">
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
                                                <label for="cage">Child Age</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="visittime">Visit Time</label>
                                        </div>                        

                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="datetime-local" id="visittime" name="visittime" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control border-0" oninput="sanitizeMessage(this)" placeholder="Leave a message here" id="message" style="height: 100px" name="message" required maxlength="500"></textarea>
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
                                <img class="position-absolute w-100 h-100 rounded" src="img/appointment.jpg" style="object-fit: cover;" alt="Appointment Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Appointment End -->


        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h3 class="text-white mb-4">Get In Touch</h3>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Balvatika APS Basoli, Ward no. 6, Near Army Camp, Dhanore (J&amp;K) Pin-184152</p>
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
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-1.jpg" alt="Classroom photo 1">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-2.jpg" alt="Classroom photo 2">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-3.jpg" alt="Classroom photo 3">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-4.jpg" alt="Classroom photo 4">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-5.jpg" alt="Classroom photo 5">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/classes-6.jpg" alt="Classroom photo 6">
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
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
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
    <!-- Form Validation -->
    <script>
    function validateForm() {
        // Client-side validation
        const gname = document.getElementById('gname').value;
        const email = document.getElementById('emailid').value;
        const cname = document.getElementById('cname').value;
        const agegroup = document.getElementById('agegroup').value;
        const vtime = document.getElementById('visittime').value;
        const message = document.getElementById('message').value;
        
        // Validate guardian name (letters and spaces only)
        if (!/^[A-Za-z ]+$/.test(gname)) {
            alert('Guardian name can only contain letters and spaces');
            return false;
        }
        
        // Validate child name (letters and spaces only)
        if (!/^[A-Za-z ]+$/.test(cname)) {
            alert('Child name can only contain letters and spaces');
            return false;
        }
        
        // Validate email format
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert('Please enter a valid email address');
            return false;
        }
        
        // Validate age group selection
        const validAgeGroups = ['2-3 Year', '3-4 Year', '4-5 Year', '5-6 Year'];
        if (!validAgeGroups.includes(agegroup)) {
            alert('Please select a valid age group');
            return false;
        }
        
        // Validate visit time is in the future
        const now = new Date();
        const visitTime = new Date(vtime);
        if (visitTime <= now) {
            alert('Please select a future date and time for your visit');
            return false;
        }
        
        // Validate message length
        if (message.length > 500) {
            alert('Message cannot exceed 500 characters');
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html>