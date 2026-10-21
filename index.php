<?php 

// Set secure session cookie attributes
session_set_cookie_params(['samesite' => 'Lax']);
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
<meta name="author" content="Rajesh Kumar">
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
                    <img src="img/logo.png" alt="Kider Logo" style="height: 120px; width: auto; margin-right: 6px;">
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


        <!-- Carousel Start -->
        <div class="container-fluid p-0 mb-5">
            <div class="owl-carousel header-carousel position-relative">
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-1.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(0, 0, 0, .2);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-2 text-white animated slideInDown mb-4">Balvatika APS: The Ideal Start for Your Child's Education</h1>
                                    <p class="fs-5 fw-medium text-white mb-4 pb-2">At Balvatika APS School, we nurture young minds with a love for learning in a safe, joyful environment. Our holistic approach focuses on developing essential life skills, creativity, and curiosity. Join us to provide your child with the best foundation for a bright future.</p>
                                    <a href="about.php" class="btn btn-primary rounded-pill py-sm-3 px-sm-5 me-3 animated slideInLeft">Learn More</a>
                                    <a href="classes.php" class="btn btn-dark rounded-pill py-sm-3 px-sm-5 animated slideInRight">Our Classes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-2.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(0, 0, 0, .2);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-2 text-white animated slideInDown mb-4">Where Little Minds Blossom with Big Dreams</h1>
                                    <p class="fs-5 fw-medium text-white mb-4 pb-2">Balvatika APS School is committed to fostering a stimulating and caring environment where every child can thrive. Our experienced educators blend fun with learning to inspire creativity, social development, and a lifelong passion for knowledge. Give your child the perfect start at Balvatika APS.</p>
                                    <a href="about.php" class="btn btn-primary rounded-pill py-sm-3 px-sm-5 me-3 animated slideInLeft">Learn More</a>
                                    <a href="classes.php" class="btn btn-dark rounded-pill py-sm-3 px-sm-5 animated slideInRight">Our Classes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->


        <!-- Facilities Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">School Facilities</h1>
                    <p>We offer a variety of facilities that ensure a holistic learning environment for our students. From safe transportation to nutritious meals, our aim is to provide a well-rounded educational experience that supports your child’s growth and well-being.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="facility-item">
                            <div class="facility-icon bg-primary">
                                <span class="bg-primary"></span>
                                <i class="fa fa-bus-alt fa-3x text-primary"></i>
                                <span class="bg-primary"></span>
                            </div>
                            <div class="facility-text bg-primary">
                                <h3 class="text-primary mb-3">School Bus</h3>
                                <p class="mb-0">Safe and reliable transportation for all students, ensuring timely pick-up and drop-off.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="facility-item">
                            <div class="facility-icon bg-success">
                                <span class="bg-success"></span>
                                <i class="fa fa-futbol fa-3x text-success"></i>
                                <span class="bg-success"></span>
                            </div>
                            <div class="facility-text bg-success">
                                <h3 class="text-success mb-3">Playground</h3>
                                <p class="mb-0">A spacious and safe playground where students engage in physical activities and outdoor fun.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="facility-item">
                            <div class="facility-icon bg-warning">
                                <span class="bg-warning"></span>
                                <i class="fa fa-home fa-3x text-warning"></i>
                                <span class="bg-warning"></span>
                            </div>
                            <div class="facility-text bg-warning">
                                <h3 class="text-warning mb-3">Healthy Canteen</h3>
                                <p class="mb-0">Our canteen offers fresh, nutritious meals, promoting healthy eating habits among students.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="facility-item">
                            <div class="facility-icon bg-info">
                                <span class="bg-info"></span>
                                <i class="fa fa-chalkboard-teacher fa-3x text-info"></i>
                                <span class="bg-info"></span>
                            </div>
                            <div class="facility-text bg-info">
                                <h3 class="text-info mb-3">Positive Learning</h3>
                                <p class="mb-0">Our classrooms are designed to encourage active learning, creativity, and positive interactions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Facilities End -->


        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <h1 class="mb-3">Our Mission and Cultural Initiatives</h1>
                        
                        
                        
                        <p style="text-align:justify;">"Inspiring Curiosity, Celebrating Diversity, Building Foundations for Lifelong Learning."</p>
                        <p style="text-align:justify;"> At Balvatika we believe early childhood is the most critical period for developing a love of learning. Our mission is to create a safe, inclusive, and joyful environment where young minds can thrive, explore, and grow.</p>
                        <p> Our Culture</p>
                        <p style="text-align:justify;">Our culture centers on nurturing each child’s potential through purposeful play, curiosity-driven activities, and a focus on emotional and social development. Our key cultural initiatives include:<br>
                        Play-Based Learning:
                        Learning through play is the heart of our approach. We incorporate games, storytelling, and hands-on activities that make education both enjoyable and meaningful.<br>
                        
                     Diversity and Inclusion: We celebrate the richness of our community's diversity. Through storytelling, music, and celebrations, we introduce children to the beauty of various cultures and traditions.</p>
<!--                        <p> 3. Parental Partnership </p>-->
<!--<p>We view parents as essential partners in their child’s educational journey. From workshops and family events to regular updates, we keep families involved and informed.-->
<!--</p>-->
<!--<p>4. Environmental Awareness</p>-->
<!--<p>Instilling respect for the environment starts young. Through simple eco-friendly practices like recycling projects and nature walks, we teach children about the importance of caring for our planet.-->
<!--</p>-->
<!--<p>5. Social and Emotional Learning</p>-->
<!--<p>We help children develop skills to understand and manage their emotions, communicate effectively, and build empathy. Daily "Feelings Circle" time allows children to express themselves in a supportive space.-->
<!--</p>-->
<!--<p>6. Health and Wellness</p>-->
<!--<p>Promoting healthy habits is a part of our daily routine, from nutritious snacks to physical activities and mindfulness exercises that help children learn the importance of a balanced lifestyle.-->
<!--</p>-->
<!--<p>7. Creativity and Exploration</p>-->
<!--<p>We believe every child is an artist! Through arts and crafts, music, and sensory play, we encourage children to explore and express their creativity freely.</p>-->
<!--                       </p>-->
                        <div class="row g-4 align-items-center">
                            <div class="col-sm-6">
                                <a class="btn btn-primary rounded-pill py-3 px-5" href="about.php">Read More</a>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <!-- <img class="rounded-circle flex-shrink-0" src="" alt="P-img" style="width: 45px; height: 45px;"> -->
                                    <div class="ms-3">
                                        <h6 class="text-primary mb-1"> Mrs. Priya Upadhyay</h6>
                                        <small>Balvatika APS Basoli Principal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 about-img wow fadeInUp" data-wow-delay="0.5s">
                        <div class="row">
                            <div class="col-12 text-center">
                                <img class="img-fluid w-75 rounded-circle bg-light p-3" src="img/about-1.jpg" alt="">
                            </div>
                            <div class="col-6 text-start" style="margin-top: -150px;">
                                <img class="img-fluid w-100 rounded-circle bg-light p-3" src="img/about-2.jpg" alt="">
                            </div>
                            <div class="col-6 text-end" style="margin-top: -150px;">
                                <img class="img-fluid w-100 rounded-circle bg-light p-3" src="img/about-3.jpg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Call To Action Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded">
                    <div class="row g-0">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s" style="min-height: 400px;">
                            <div class="position-relative h-100">
                                <img class="position-absolute w-100 h-100 rounded" src="img/call-to-action.jpg" alt="Principal mam photo">
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                            <div class="h-100 d-flex flex-column justify-content-center p-5">
                                <h1 class="mb-4">From the Principal's Desk</h1>
                                <p class="mb-4" style="text-align:justify;">At Balvatika we are dedicated to fostering a foundation for lifelong learning during these formative early years. Our pre-primary program is thoughtfully structured to provide a secure, stimulating, and supportive environment, where young learners are encouraged to explore, inquire, and grow. We recognize the importance of early childhood education in shaping confident, resilient, and curious individuals.<br>

Our team of highly qualified educators is committed to delivering an enriching curriculum that balances academic readiness with social and emotional development. Through age-appropriate activities and play-based learning, we strive to nurture each child's unique potential and instill a love for learning.<br>

We value the partnership we share with parents and guardians and believe that open communication and collaboration are essential to each child's success. <br>
</p>
<h4>Warm regards,</h4>
<h3>Mrs Priya Upadhyay</h3> 
<h4>(Principal)</h4>
                                <a class="btn btn-primary py-3 px-5" href="principaldesk.php">Read More<i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Call To Action End -->


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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">1</span>
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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">2</span>
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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">3</span>
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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">4</span>
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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">5</span>
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
                                    <span class="bg-primary text-white rounded-pill py-2 px-3" href="">6</span>
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
                                <img class="position-absolute w-100 h-100 rounded" src="img/appointment.jpg" style="object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Appointment End -->


        <!-- Team Start 
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Popular Teachers</h1>
                    <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit
                        eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item position-relative">
                            <img class="img-fluid rounded-circle w-75" src="img/team-1.jpg" alt="">
                            <div class="team-text">
                                <h3>Full Name</h3>
                                <p>Designation</p>
                                <div class="d-flex align-items-center">
                                    <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="team-item position-relative">
                            <img class="img-fluid rounded-circle w-75" src="img/team-2.jpg" alt="">
                            <div class="team-text">
                                <h3>Full Name</h3>
                                <p>Designation</p>
                                <div class="d-flex align-items-center">
                                    <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="team-item position-relative">
                            <img class="img-fluid rounded-circle w-75" src="img/team-3.jpg" alt="">
                            <div class="team-text">
                                <h3>Full Name</h3>
                                <p>Designation</p>
                                <div class="d-flex align-items-center">
                                    <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-square btn-primary  mx-1" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        Team End -->


        <!-- Testimonial Start -->
       <!-- Upcoming Events Start -->
       <div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">School Rules and Regulations</h1>
            <p>To create a safe and fun environment for all our students, please follow these simple rules.</p>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            <div class="testimonial-item bg-light rounded p-5">
                <p class="fs-5">All students must arrive on time. Classes begin promptly at 8:30 AM, and we encourage punctuality to help your child build a routine.</p>
                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                    <div class="ps-3">
                        <h3 class="mb-1">Punctuality</h3>
                        <span>Class Start Time: 8:30 AM</span>
                    </div>
                    <i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-5">
                <p class="fs-5">Parents must inform the school in advance if their child will be absent. A phone call or email is required to ensure the safety of our students.</p>
                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                    <div class="ps-3">
                        <h3 class="mb-1">Absence Notification</h3>
                        <span>Contact School Office: 01922-291640</span>
                    </div>
                    <i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-5">
                <p class="fs-5">All students should wear the appropriate school uniform each day. This ensures equality and helps create a sense of community.</p>
                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                    <div class="ps-3">
                        <h3 class="mb-1">Uniform Policy</h3>
                        <span>Dress Code: Full Uniform</span>
                    </div>
                    <i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-5">
                <p class="fs-5">Healthy snacks are encouraged during snack time. Please avoid bringing sweets or sugary drinks.</p>
                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                    <div class="ps-3">
                        <h3 class="mb-1">Snack Time</h3>
                        <span>Healthy snacks only</span>
                    </div>
                    <i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-5">
                <p class="fs-5">For safety reasons, all personal toys should stay at home. The school provides plenty of toys and activities during playtime.</p>
                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                    <div class="ps-3">
                        <h3 class="mb-1">No Toys from Home</h3>
                        <span>School provides playtime materials</span>
                    </div>
                    <i class="fa fa-quote-right fa-3x text-primary ms-auto d-none d-sm-flex"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Upcoming Events End -->
        <!-- Testimonial End -->


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
    // Remove HTML tags from input in real-time
    textarea.value = textarea.value.replace(/<[^>]*>/g, '');
    
    // Optionally show remaining characters
    const remaining = 500 - textarea.value.length;
    document.getElementById('char-count').textContent = 
        `${remaining} characters remaining`;
}
</script>
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
    <!-- JavaScript Libraries -->
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>