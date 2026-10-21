<?php
include_once('includes/config.php');
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security error: Invalid request");
    }

    // Get and sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = htmlspecialchars(strip_tags($_POST['message']), ENT_QUOTES, 'UTF-8');

    // Validate inputs
    $errors = [];
    
    if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $name)) {
        $errors[] = "Invalid name format (letters and spaces only, 3-50 characters)";
    }
    if (preg_match('/^[a-zA-Z\s]{3,50}$/', $message)) {
    $errors[] = "HTML tags are not allowed in the message";
    } 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (strlen($subject) < 3 || strlen($subject) > 100) {
        $errors[] = "Subject must be 3-100 characters";
    }
    
    if (strlen($message) < 3 || strlen($message) > 500) {
        $errors[] = "Message must be 3-500 characters";
    }

    if (empty($errors)) {
        // Prepared statement
        $stmt = $con->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            
           $_SESSION['form_success'] = true;
            header("Location: contact.php");
            exit();
        } else {
            $errors[] = "Database error. Please try again.";
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Balvatika APS Basoli</title>
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
                <h1 class="display-2 text-white animated slideInDown mb-4">Contact Us</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header End -->

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
        <!-- Contact Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Get In Touch</h1>
                    <p>If you have any questions, need more information, or just want to say hello, feel free to reach out to us. We’re always here to help and would love to hear from you!</p>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.1s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 75px; height: 75px;">
                            <i class="fa fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <h6>Balvatika APS Basoli, Ward no. 6, Near Army Camp, Dhanore (J&K) Pin-184152</h6>
                    </div>
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.3s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 75px; height: 75px;">
                            <i class="fa fa-envelope-open fa-2x text-primary"></i>
                        </div>
                        <h6>https://balvatikaapsbasoli.org/</h6>
                    </div>
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.5s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 75px; height: 75px;">
                            <i class="fa fa-phone-alt fa-2x text-primary"></i>
                        </div>
                        <h6>01922-291640</h6>
                    </div>
                </div>
                <div class="bg-light rounded">
    <div class="row g-0">
        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
            <div class="h-100 d-flex flex-column justify-content-center p-5">
                 <form action="contact.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="name" name="name" 
                                               pattern="[A-Za-z\s]{3,50}" 
                                               title="Only letters and spaces (3-50 characters)" 
                                               required>
                                        <label for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control border-0" id="email" name="email" 
                                               required>
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="subject" name="subject" 
                                               pattern="[\p{L}\p{N}\s\.,!?@#\-\(\)]{3,100}" 
                                               title="Letters, numbers, spaces and basic punctuation only" 
                                               required>
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea type="text" class="form-control border-0" id="message" name="message" oninput="sanitizeMessage(this)" style="height: 100px" maxlength="500" pattern="[\p{L}\p{N}\s\.,!?@#\-\(\)]{3,100}" title="Letters, numbers, spaces and basic punctuation only" required></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                                </div>
                            </div>
                        </form>
            </div>
        </div>


                  
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s" style="min-height: 400px;">
                            <div class="position-relative h-100">
                                <!--<iframe class="position-relative rounded w-100 h-100"-->
                                <!--src="https://maps.google.com/maps?width=802&amp;height=480&amp;hl=en&amp;q=9JVM+J4M, Basantpur, Jammu and Kashmir 184152&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"-->
                                <!--frameborder="0" style="min-height: 400px; border:0;" allowfullscreen="" aria-hidden="false"-->
                                <!--tabindex="0"></iframe>-->
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2811.833110959639!2d75.6327913!3d32.3940834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391c7daaa2997ee7%3A0x58be824d5e2ecd7a!2sUsman%20Enclave!5e1!3m2!1sen!2sin!4v1729326930894!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


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
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
<script>
function sanitizeMessage(textarea) {
    // More comprehensive sanitization
    textarea.value = textarea.value
        .replace(/</g, '')  // Convert < to HTML entity
        .replace(/>/g, '')  // Convert > to HTML entity
        .replace(/&/g, ''); // Convert & to HTML entity
    
    // Update character count
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