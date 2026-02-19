
  </main>

  <footer id="footer" class="footer dark-background">
  <div class="container footer-top">
    <div class="row gy-4">

      <!-- Column 1: Contact + Social -->
      <div class="col-lg-3 col-md-6 footer-about">
        <a href="<?php echo base_url(); ?>" class="logo d-flex align-items-center">
          <span class="sitename">BPMS247</span>
        </a>
        <div class="footer-contact pt-3">
          <p>1705 Highway 138 SE</p>
          <p>Conyers, GA 30013</p>
          <p class="mt-3"><strong>Phone:</strong> <span>+1 770 410 8378</span></p>
          <p><strong>Email:</strong> <span>info@fleurysolutions.com</span></p>
        </div>
        <div class="social-links d-flex mt-4">
          <a href="#"><i class="bi bi-twitter-x"></i></a>
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <!-- Column 2: Useful Links -->
      <div class="col-lg-3 col-md-6 footer-links">
        <h4>Useful Links</h4>
        <ul>
          <li><a href="#about">About Us</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="#contact">Contact Us</a></li>
          <!-- <li><a href="https://fleurysolutions.com/page/terms-of-service" target="_blank">Terms of Service</a></li> -->
          <li><a href="https://fleurysolutions.com/front/terms_condition" target="_blank">Terms of Service</a></li>
          <li><a href="https://bpms247.com/front/privacy_policy" target="_blank">Privacy Policy</a></li>
          <!-- <li><a href="https://fleurysolutions.com/page/privacy-policy" target="_blank">Privacy Policy</a></li> -->
        </ul>
      </div>

      <!-- Column 3: Our Services (Linked to Fleury Site) -->
      <div class="col-lg-3 col-md-6 footer-links">
        <h4>Our Services</h4>
        <ul>
          <li><a href="https://fleurysolutions.com/services" target="_blank">Overview</a></li>
          <li><a href="https://fleurysolutions.com/services/web-mobile-app-development" target="_blank">Web & Mobile App Development</a></li>
          <li><a href="https://fleurysolutions.com/services/custom-software-saas-solutions" target="_blank">Custom Software & SaaS Solutions</a></li>
          <li><a href="https://fleurysolutions.com/services/construction-management-general-contracting" target="_blank">Construction Management</a></li>
          <li><a href="https://fleurysolutions.com/services/digital-marketing-branding" target="_blank">Digital Marketing & Branding</a></li>
          <li><a href="https://fleurysolutions.com/services/business-automation-ai-integration" target="_blank">Business Automation & AI</a></li>
        </ul>
      </div>

      <!-- Column 4: Newsletter -->
      <div class="col-lg-3 col-md-6 footer-newsletter">
        <h4>Our Newsletter</h4>
        <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
        <form action="<?php echo base_url(); ?>front/newsletter_subscription" method="post" >
          <div class="newsletter-form">
            <input type="email" name="email" placeholder="Your Email"><input type="submit" value="Subscribe">
          </div>
          <?php 
          if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'success') { ?>
              <!-- <div class="loading">Loading</div> -->
              <div class="error-message"></div>
              <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          <?php } elseif (isset($_REQUEST['response']) && $_REQUEST['response'] == 'fail') { ?>
              <!-- <div class="loading">Loading</div> -->
              <div class="error-message" style="color:red">Some technical problem occurred, try again later.</div>
              <div class="sent-message"></div>
          <?php } ?>
        </form>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p>© Copyright <strong class="px-1 sitename">BPMS247</strong> <?php echo date("Y"); ?> All Rights Reserved</p>
  </div>
</footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/php-email-form/validate.js"></script>
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/aos/aos.js"></script>
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="<?php echo base_url(); ?>/assets/theme/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="<?php echo base_url(); ?>/assets/theme/js/main.js"></script>

</body>

</html>