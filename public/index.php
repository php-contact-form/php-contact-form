<?php
    /* AJAX check  */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(isset($_POST)) {
        include dirname(__FILE__) . '/../src/Contact.php';

        // Don't get this from $_POST because JSON
        $formData = file_get_contents('php://input');

        $contact = new Contact();

        print_r(json_encode($contact->saveContact($formData)));
    }
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">

<title>Dealer Inspire Code Challenge</title>

<!-- Bootstrap Core CSS -->
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

<!-- Theme CSS -->
<link href="css/grayscale.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

<!-- Navigation -->
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                Menu <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand page-scroll" href="#page-top">
                <i class="fa fa-play-circle"></i> <span class="light">Dealer</span> Inspire Challenge
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
            <ul class="nav navbar-nav">
                <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                <li class="hidden">
                    <a href="#page-top"></a>
                </li>
                <li>
                    <a class="page-scroll" href="#about">About</a>
                </li>
                <li>
                    <a class="page-scroll" href="#coffee">Coffee</a>
                </li>
                <li>
                    <a class="page-scroll" href="#contact">Contact</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<!-- Intro Header -->
<header class="intro">
    <div class="intro-body">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <h1 class="brand-heading">Challenge</h1>
                    <p class="intro-text">Code Something Awesome.
                        <br>We &lt;3 PHP Developers.</p>
                    <a href="#about" class="btn btn-circle page-scroll">
                        <i class="fa fa-angle-double-down animated"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- About Section -->
<section id="about" class="container content-section text-center">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <h2>About This Challenge</h2>
            <p>We make awesome things at Dealer Inspire.  We'd like you to join us.  That's why we made this page.  Are you ready to join the team?</p>
            <p>To take the code challenge, visit <a href="https://bitbucket.org/dealerinspire/php-contact-form">this Git Repo</a> to clone it and start your work.</p>
        </div>
    </div>
</section>

<!-- Coffee Section -->
<section id="coffee" class="content-section text-center">
    <div class="download-section">
        <div class="container">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Coffee Break?</h2>
                <p>Take a coffee break.  You deserve it.</p>
                <a href="https://www.youtube.com/dealerinspire" class="btn btn-default btn-lg">or Watch YouTube</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="container content-section text-center">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <h2>Contact Guy Smiley</h2>
            <p>Remember Guy Smiley?  Yeah, he wants to hear from you.</p>
            <form class="text-left" data-toggle="validator" role="form">
                <div class="row row-eq-height">
                    <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="name" class="col-sm-4 pull-left control-label">Full Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Joan Ganz Cooney" required data-error="Please provide your name">
                            <div class="col-sm-8 text-right pull-right help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-4 pull-left control-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="jgcooney@email.arizona.edu" required data-error="Please provide a valid email">
                            <div class="col-sm-9 text-right pull-right help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-sm-3 pull-left control-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" placeholder="(215) 702-3566" data-error="Please provide a valid phone number" pattern="(?=.*?\d{3}( |-|.)?\d{4})((?:\+?(?:1)(?:\1|\s*?))?(?:(?:\d{3}\s*?)|(?:\((?:\d{3})\)\s*?))\1?(?:\d{3})\1?(?:\d{4})(?:\s*?(?:#|(?:ext\.?))(?:\d{1,5}))?)\b">
                            <div class="col-sm-12 text-right pull-right help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="message" class="col-sm-4 pull-left control-label">Message</label>
                            <textarea class="form-control" rows="9" name="message" required data-error="Please provide a message"></textarea>
                            <div class="col-sm-12 text-right help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <button class="col-xs-8 col-xs-offset-2 btn btn-default btn-lg" type="submit">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Map Section -->
<div id="map"></div>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p><small>Copyright 2018 Dealer Inspire</small></p>
    </div>
</footer>

<!-- jQuery -->
<script src="vendor/jquery/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Plugin JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

<!-- Google Maps API Key - Use your own API key to enable the map feature. More information on the Google Maps API can be found at https://developers.google.com/maps/ -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD49XtMG4OOkUFeRKid0ltAxXta5I6N1V8"></script>

<!-- Bootstrap Form Validation JavaScript -->
<script src="vendor/validator/validator.min.js"></script>

<!-- Theme JavaScript -->
<script src="js/grayscale.js"></script>

</body>

</html>
<?php }?>
