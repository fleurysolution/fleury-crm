<style type="text/css">
    /*--------------------------------------------------------------
# Pricing Section
--------------------------------------------------------------*/
.pricing .pricing-item {
  background-color: color-mix(in srgb, var(--accent-color), transparent 96%);
  padding: 40px 40px;
  height: 100%;
  border-radius: 15px;
}

.pricing h3 {
  font-weight: 600;
  margin-bottom: 15px;
  font-size: 20px;
}

.pricing h4 {
  color: var(--accent-color);
  font-size: 48px;
  font-weight: 700;
  font-family: var(--heading-font);
  margin-bottom: 0;
}

.pricing h4 sup {
  font-size: 28px;
}

.pricing h4 span {
  color: color-mix(in srgb, var(--default-color), transparent 50%);
  font-size: 18px;
  font-weight: 500;
}

.pricing .description {
  font-size: 14px;
}

.pricing .cta-btn {
  border: 1px solid var(--default-color);
  color: var(--default-color);
  display: block;
  text-align: center;
  padding: 10px 35px;
  border-radius: 5px;
  font-size: 16px;
  font-weight: 500;
  font-family: var(--heading-font);
  transition: 0.3s;
  margin-top: 20px;
  margin-bottom: 6px;
}

.pricing .cta-btn:hover {
  background: var(--accent-color);
  color: var(--contrast-color);
  border-color: var(--accent-color);
}

.pricing ul {
  padding: 0;
  list-style: none;
  color: color-mix(in srgb, var(--default-color), transparent 30%);
  text-align: left;
  line-height: 20px;
}

.pricing ul li {
  padding: 10px 0;
  display: flex;
  align-items: center;
}

.pricing ul li:last-child {
  padding-bottom: 0;
}

.pricing ul i {
  color: #059652;
  font-size: 24px;
  padding-right: 3px;
}

.pricing ul .na {
  color: color-mix(in srgb, var(--default-color), transparent 60%);
}

.pricing ul .na i {
  color: color-mix(in srgb, var(--default-color), transparent 60%);
}

.pricing ul .na span {
  text-decoration: line-through;
}

.pricing .featured {
  position: relative;
}

.pricing .featured .popular {
  position: absolute;
  top: 15px;
  right: 15px;
  background-color: var(--accent-color);
  color: var(--contrast-color);
  padding: 4px 15px 6px 15px;
  margin: 0;
  border-radius: 5px;
  font-size: 14px;
  font-weight: 500;
}

.pricing .featured .cta-btn {
  background: var(--accent-color);
  color: var(--contrast-color);
  border-color: var(--accent-color);
}

@media (max-width: 992px) {
  .pricing .box {
    max-width: 60%;
    margin: 0 auto 30px auto;
  }
}

@media (max-width: 767px) {
  .pricing .box {
    max-width: 80%;
    margin: 0 auto 30px auto;
  }
}

@media (max-width: 420px) {
  .pricing .box {
    max-width: 100%;
    margin: 0 auto 30px auto;
  }
}
.pricing .cta-btn {
    color: var(--default-color);
    display: block;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
    font-family: var(--heading-font);
    margin-top: 20px;
    margin-bottom: 6px;
    border: 1px solid var(--default-color);
    padding: 10px 35px;
    border-radius: 5px;
    transition: 0.3s;
}
.pricing .cta-btn:hover {
    color: var(--contrast-color);
    background: var(--accent-color);
    border-color: var(--accent-color);
}
.pricing .pricing-item {
    border: 2px solid #346EB6;
}
</style>
 <!-- Pricing Section -->
    <section id="pricing" class="pricing section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Pricing</h2>
        <div><span>Check Our</span> <span class="description-title">Pricing</span></div>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">
       <?php  foreach ($priceList as $price): 
      
       ?>
            <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="pricing-item">
                    <h3><?php echo htmlspecialchars($price->package_name); ?></h3>
                    <h4>
                        <sup>$</sup><?php echo htmlspecialchars($price->price); ?>
                        <span> / <?php echo htmlspecialchars($price->duration); ?></span>
                    </h4>
                    <a href="#" class="cta-btn pay-now" data-package-id="<?php echo htmlspecialchars($price->id); ?>">
                            <?php echo htmlspecialchars($price->button_text); ?>
                        </a>

                    <?php //echo $price->description ?>
                </div>
            </div>
        <?php endforeach; ?>


          
        </div>

      </div>

    </section><!-- /Pricing Section -->


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const payButtons = document.querySelectorAll('.pay-now');

        payButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                const packageId = this.dataset.packageId;

                // Check if the user is logged in via AJAX
                fetch('/check-login', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.isLoggedIn) {
                        window.location.href = `/start-payment/${packageId}`;
                    } else {
                         const loginUrl = `/front/register/${packageId}`;
                        window.location.href = loginUrl;
                    }
                })
                .catch(error => {
                    console.error('Error checking login status:', error);
                });
            });
        });
    });
</script>
