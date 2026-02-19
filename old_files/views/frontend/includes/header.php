

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title> Simplify Customer Management with Our Cloud CRM BPMS 247</title>
  <meta name="description" content="Discover our powerful Cloud CRM solution 'BPMS247' designed to streamline customer management, boost sales, and improve team productivity. Scalable, easy-to-use, and perfect for growing businesses. Try it today!">
  <meta name="keywords" content="BPMS247, bpms247, Cloud CRM Solution, Best CRM for Small Businesses, Customer Relationship Management Software, Online CRM System, CRM for Sales Teams, Cloud-based CRM Software, Scalable CRM for Businesses, Affordable CRM Solutions">

  <!-- Favicons -->
  <link href="<?php echo base_url(); ?>/assets/theme/img/favicon.png" rel="icon">
  <link href="<?php echo base_url(); ?>/assets/theme/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url(); ?>/assets/theme/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>/assets/theme/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>/assets/theme/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>/assets/theme/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>/assets/theme/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
<!-- Google Consent Mode (Default Denied) -->
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  
  gtag('consent', 'default', {
    'ad_storage': 'denied',
    'analytics_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied'
  });
</script>

<!-- Google Analytics GA4 – G-23ER5G4RE0 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-23ER5G4RE0"></script>
<script>
  gtag('js', new Date());
  gtag('config', 'G-23ER5G4RE0');
</script>
<!-- Consent Update: Trigger on user cookie acceptance -->
<script>
  // Example: trigger only after user accepts cookies
  function onConsentGiven() {
    gtag('consent', 'update', {
      'ad_storage': 'granted',
      'analytics_storage': 'granted',
      'ad_user_data': 'granted',
      'ad_personalization': 'granted'
    });
  }
</script>

<!-- Google reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=6LfOMVAqAAAAACSYYbiwzVutmcreDTI7hXAkpI5N"></script>
<script>
  grecaptcha.ready(function() {
    grecaptcha.execute('6LfOMVAqAAAAACSYYbiwzVutmcreDTI7hXAkpI5N', {action: 'homepage'}).then(function(token) {
       // optionally send the token to the server
    });
  });
</script>
  <!-- Main CSS File -->
  <link href="<?php echo base_url(); ?>/assets/theme/css/main.css" rel="stylesheet">
<style type="text/css">
  
</style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="<?php echo base_url(); ?>" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="<?php echo base_url(); ?>/assets/theme/img/logo.png" alt=""> -->
        <h1 class="sitename">BPMS247</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="<?php echo base_url(); ?>#hero" class="active">Home</a></li>
          <li><a href="<?php echo base_url(); ?>#about">About</a></li>
          <li><a href="<?php echo base_url(); ?>#features">Features</a></li>
          <li><a href="<?php echo base_url(); ?>#pricing">Pricing</a></li>
          <li><a href="<?php echo base_url('front/appointments'); ?>">Book Your Appointment </a></li>
          <!--<li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>-->
          <li><a href="https://fleurysolutions.com/page/book-appointment" target="_blank">Contact</a></li>
          <?php if (session()->has('user_id')): ?>
            <li><a href="<?= base_url('dashboard'); ?>">Dashboard</a></li>
            <li> <a href="<?= base_url('signin/sign_out'); ?>">Logout</a></li>
<?php else: ?>
  <li>  <a href="<?= base_url('signin'); ?>">Login</a></li>
<?php endif; ?>
           <!-- <li><a href="<?= base_url('signin');?>">Sign In</a></li> -->
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">
