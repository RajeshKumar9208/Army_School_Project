<!DOCTYPE html>
<?php
include_once('includes/config.php');

// Fetch images from the database
$query = "SELECT * FROM gallery_images";
$result = mysqli_query($con, $query);
?>

<html lang="en">

<head>
   
    <meta charset="utf-8">
    <title>Balvatika APS Basoli</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

   
    <link rel="stylesheet" type="text/css" href="style1.css">

	<!--  *****   Link To Font Awsome Icons   *****  -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>

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
        
        <!-- Page Header End -->

        <section class="portfolio">
	
            <header class="section-head">
                <h1>Some Of Latest Images</h1>
            </header>
        
            <main class="mainContainer">
                
                <!--  *****  Buttons Section Starts  *****  -->
                <div class="button-group">
                    <button class="button active" data-filter="*">All</button>
                    <button class="button" data-filter=".design">Section 1</button>
                <button class="button" data-filter=".development">Section 2</button>
                    <button class="button" data-filter=".logo">Section 3</button>
                </div>
                <!--  *****  Buttons Section Ends  *****  -->
        
                <!--  *****  Gallery Section Starts  *****  -->
                <div class="gallery">
                    
                    <!--  *****  Card 1 Starts  *****  -->
                    <div class="item design">
                        <figure><img src="img/1.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Rhyme Competition</h3></figcaption>
                        </figure>
                        
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <!--  *****  Card 1 Ends  *****  -->
        
                    <!--  *****  Card 2 Starts  *****  -->
                    <div class="item design">
                         <figure><img src="img/2.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Rhyme Competition</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <!--  *****  Card 2 Ends  *****  -->
        
                    <!--  *****  Card 3 Starts  *****  -->
                    <div class="item design">
                       <figure><img src="img/3.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Rhyme Competition</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                        <figure><img src="img/4.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                          <figure><img src="img/5.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                          <figure><img src="img/6.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                        <figure><img src="img/7.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                        <figure><img src="img/8.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <div class="item design">
                         <figure><img src="img/9.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                           
                        </div>
                    </div>
                    <!--  *****  Card 3 Ends  *****  -->
        
                    <!--  *****  Card 4 Starts  *****  -->
                    <div class="item development">
                       <figure><img src="img/10.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <!--  *****  Card 4 Ends  *****  -->
        
                    <!--  *****  Card 5 Starts  *****  -->
                    <div class="item development">
                        <figure><img src="img/11.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <!--  *****  Card 5 Ends  *****  -->
        
                    <!--  *****  Card 6 Starts  *****  -->
                    <div class="item development">
                       <figure><img src="img/12.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Rhyme Competition</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                       <figure><img src="img/13.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Independence Day</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                        <figure><img src="img/14.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Rhyme Competition</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                      <figure><img src="img/15.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Seed Germination</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                       <figure><img src="img/16.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Seed Germination</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                       <figure><img src="img/17.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Van Mahotsav</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item development">
                       <figure><img src="img/18.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Van Mahotsav</h3></figcaption>
                        </figure>
                        <div class="overlay">
                            
                        </div>
                    </div>
                    <div class="item logo">
                      <figure><img src="img/19.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Van Mahotsav</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                 
                  <div class="item logo">
                      <figure><img src="img/20.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Van Mahotsav</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                   <div class="item logo">
                      <figure><img src="img/21.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Krishan Janmashtami</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                     <div class="item logo">
                      <figure><img src="img/22.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Seed Germination</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                     <div class="item logo">
                      <figure><img src="img/23.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Colouring Competition</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                     <div class="item logo">
                      <figure><img src="img/24.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Krishan Janmashtami</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                     <div class="item logo">
                      <figure><img src="img/25.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Van Mahotsav</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                     <div class="item logo">
                      <figure><img src="img/26.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Colour Sorting</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                 
                    <div class="item logo">
                      <figure><img src="img/27.jpg"><br>
                        <figcaption style="text-align:center;"><h3>Krishan Janmashtami</h3></figcaption>
                        </figure>
                        <div class="overlay">
                          
                        </div>
                    </div>
                </div> 
                <!--  *****  Gallery Section Ends  *****  -->
        
            </main>
            <main class="mainContainer">
    <div class="button-group">
        <button class="button active" data-filter="*">Others</button>
    
    </div>

    <div class="gallery">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="item design">
                <figure>
                    <img src="admin/img/<?php echo $row['image']; ?>"><br>
                    <figcaption style="text-align:center;">
                        <h3><?php echo $row['name']; ?></h3>
                    </figcaption>
                </figure>
            </div>
        <?php endwhile; ?>
    </div>
</main>




        </section>
        
        
        <!--   *****   JQuery Link   *****   -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        
        <!--   *****   Isotope Filter Link   *****  -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js"></script>
        
        <script type="text/javascript">
            var $galleryContainer = $('.gallery').isotope({
                itemSelector: '.item',
                  layoutMode: 'fitRows'
            })
        
            $('.button-group .button').on('click', function(){
                $('.button-group .button').removeClass('active');
                $(this).addClass('active');
        
                var value = $(this).attr('data-filter');
                $galleryContainer.isotope({
                    filter: value 
                })
            })
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