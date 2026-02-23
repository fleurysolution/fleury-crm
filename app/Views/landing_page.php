<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
        <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
            <link href="<?= base_url('assets/vendor/fontawesome/fonts.css?family=Inter:wght@400;500;600;700;800&display=swap') ?>" rel="stylesheet">
            <link href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>" rel="stylesheet">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->

 
</head>
<body>

    <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-blur sticky-top">
    <div class="container py-2">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <span class="brand-mark fs-4">BPMS<span>247</span></span>
        <span class="badge text-bg-secondary bg-opacity-25 border border-white border-opacity-10 fw-semibold d-none d-sm-inline">Enterprise CRM</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="#platform">Platform</a></li>
          <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        </ul>

        <div class="d-flex gap-2">
          <a href="<?= site_url('auth/signin') ?>" class="btn btn-outline-light">Sign In</a>
          <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary fw-semibold">
            Get Started <i class="fa-solid fa-arrow-right ms-2"></i>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="badge-soft mb-3">
            <i class="fa-solid fa-flask"></i>
            BETA RELEASE v1.0
          </div>

          <h1 class="display-4 mb-3">
            The Enterprise CRM<br class="d-none d-md-block">
            that scales <span class="text-accent">with you</span>.
          </h1>

          <p class="lead mb-4">
            A complete proprietary suite for Team Management, Marketing Automation, Sales, Inventory, and Accounting —
            engineered for performance and built for growth.
          </p>

          <div class="d-flex flex-column flex-sm-row gap-3">
            <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary btn-lg px-4 fw-semibold">
              Start Free Trial <i class="fa-solid fa-rocket ms-2"></i>
            </a>
            <a href="#features" class="btn btn-outline-light btn-lg px-4">
              View Features
            </a>
          </div>

          <div class="d-flex flex-wrap gap-3 mt-4 small muted">
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-shield-halved"></i> RBAC + Audit-friendly
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-gauge-high"></i> Fast & lightweight
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-wand-magic-sparkles"></i> White-label ready
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="glass p-3 p-md-4 shadow-soft">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#ef4444;"></span>
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#f59e0b;"></span>
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#22c55e;"></span>
              </div>
              <span class="small muted">Premium Dashboard Preview</span>
            </div>
            <img
              src="https://via.placeholder.com/1100x650/0b1220/94a3b8?text=BPMS247+Dashboard+Preview"
              alt="Dashboard Preview"
              class="img-fluid rounded-4 border"
              style="border-color: rgba(148,163,184,.18) !important;"
            />
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Features -->
  <section id="features" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title display-6 mb-2">Everything you need to run your business</h2>
        <p class="section-sub mx-auto">
          Unified modules that work together — no syncing, no fragmentation, no duct-tape integrations.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-bullhorn"></i></div>
            <h5 class="mb-2">Marketing Automation</h5>
            <p class="mb-0 muted">Automate campaigns, track leads, and engage customers with personalized workflows.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-users-gear"></i></div>
            <h5 class="mb-2">Team & RBAC</h5>
            <p class="mb-0 muted">Granular role-based access control to manage permissions securely at scale.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-chart-line"></i></div>
            <h5 class="mb-2">Sales Intelligence</h5>
            <p class="mb-0 muted">Visualize pipeline, forecast revenue, and close deals faster with insight-led reporting.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-warehouse"></i></div>
            <h5 class="mb-2">Inventory & Manufacturing</h5>
            <p class="mb-0 muted">Track items, manage warehouses, and streamline manufacturing processes end-to-end.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <h5 class="mb-2">Accounting</h5>
            <p class="mb-0 muted">Invoicing, expenses, and reporting integrated directly into your operating workflow.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="icon-pill"><i class="fa-solid fa-headset"></i></div>
            <h5 class="mb-2">Helpdesk</h5>
            <p class="mb-0 muted">Manage tickets, SLAs, and customer support with a clean, fast service desk.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Platform / White Label -->
  <section id="platform" class="section">
    <div class="container">
      <div class="highlight p-4 p-md-5">
        <div class="row align-items-center g-4">
          <div class="col-lg-6">
            <h2 class="section-title mb-2">White Label & SaaS Ready</h2>
            <p class="muted mb-4">
              Build your own brand with a fully rebrandable solution. Ideal for agencies and enterprises looking to resell
              and deliver value under their identity.
            </p>

            <div class="row g-3">
              <div class="col-md-4">
                <div class="feature-card h-100">
                  <div class="icon-pill"><i class="fa-solid fa-paint-roller"></i></div>
                  <h6 class="fw-bold mb-1">Custom Branding</h6>
                  <p class="muted mb-0 small">Theme, logo, colors & login screens.</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="feature-card h-100">
                  <div class="icon-pill"><i class="fa-solid fa-plug"></i></div>
                  <h6 class="fw-bold mb-1">Plugin Manager</h6>
                  <p class="muted mb-0 small">Extend modules and add-ons cleanly.</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="feature-card h-100">
                  <div class="icon-pill"><i class="fa-solid fa-globe"></i></div>
                  <h6 class="fw-bold mb-1">Multi-Language</h6>
                  <p class="muted mb-0 small">Localize UI and user experiences.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="glass p-4 h-100">
              <h5 class="fw-bold mb-3">Why teams choose BPMS247</h5>
              <ul class="list-unstyled mb-0">
                <li class="d-flex gap-3 mb-3">
                  <div class="icon-pill" style="width:40px;height:40px;border-radius:12px;"><i class="fa-solid fa-bolt"></i></div>
                  <div>
                    <div class="fw-semibold">One suite, one source of truth</div>
                    <div class="muted small">Sales, finance, inventory, support — connected from day one.</div>
                  </div>
                </li>
                <li class="d-flex gap-3 mb-3">
                  <div class="icon-pill" style="width:40px;height:40px;border-radius:12px;"><i class="fa-solid fa-lock"></i></div>
                  <div>
                    <div class="fw-semibold">Enterprise-grade controls</div>
                    <div class="muted small">RBAC, permissions, and scalable team structures.</div>
                  </div>
                </li>
                <li class="d-flex gap-3">
                  <div class="icon-pill" style="width:40px;height:40px;border-radius:12px;"><i class="fa-solid fa-layer-group"></i></div>
                  <div>
                    <div class="fw-semibold">Modular and extensible</div>
                    <div class="muted small">Deploy what you need now, expand when you’re ready.</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Pricing (simple, optional) -->
  <section id="pricing" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title display-6 mb-2">Pricing built for growth</h2>
        <p class="section-sub mx-auto">Start lean, upgrade when you scale. (Replace with your real pricing.)</p>
      </div>

      <div class="row g-4 align-items-stretch">
        <div class="col-md-6 col-lg-4">
          <div class="feature-card h-100">
            <h6 class="text-uppercase small muted mb-2">Starter</h6>
            <div class="d-flex align-items-end gap-2 mb-3">
              <div class="display-6 fw-bold">₹0</div>
              <div class="muted mb-2">/ trial</div>
            </div>
            <ul class="muted small">
              <li>Core CRM + Pipeline</li>
              <li>Basic Marketing</li>
              <li>1 Workspace</li>
            </ul>
            <a href="<?= site_url('auth/signin') ?>" class="btn btn-outline-light w-100 mt-2">Start Trial</a>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card h-100" style="border-color: rgba(79,70,229,.45);">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="text-uppercase small muted mb-2">Pro</h6>
              <span class="badge text-bg-primary" style="background: rgba(79,70,229,.25) !important; border:1px solid rgba(79,70,229,.35);">Popular</span>
            </div>
            <div class="d-flex align-items-end gap-2 mb-3">
              <div class="display-6 fw-bold">₹999</div>
              <div class="muted mb-2">/ month</div>
            </div>
            <ul class="muted small">
              <li>Automation + Workflows</li>
              <li>Inventory Module</li>
              <li>Team RBAC</li>
            </ul>
            <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary w-100 mt-2 fw-semibold">Get Started</a>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="feature-card h-100">
            <h6 class="text-uppercase small muted mb-2">Enterprise</h6>
            <div class="d-flex align-items-end gap-2 mb-3">
              <div class="display-6 fw-bold">Custom</div>
            </div>
            <ul class="muted small">
              <li>White-label SaaS</li>
              <li>Accounting + Advanced Reports</li>
              <li>Dedicated Support</li>
            </ul>
            <a href="#about" class="btn btn-outline-light w-100 mt-2">Talk to Sales</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About / CTA -->
  <section id="about" class="section">
    <div class="container">
      <div class="glass p-4 p-md-5 text-center">
        <h2 class="section-title mb-2">Ready to launch BPMS247 in your organization?</h2>
        <p class="muted mx-auto mb-4" style="max-width: 55rem;">
          Start with a free trial or sign in to explore modules. Upgrade anytime as your team and operations scale.
        </p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
          <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary btn-lg px-4 fw-semibold">
            Start Free Trial <i class="fa-solid fa-arrow-right ms-2"></i>
          </a>
          <a href="<?= site_url('auth/signin') ?>" class="btn btn-outline-light btn-lg px-4">
            Sign In
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-5">
    <div class="container">
      <div class="row g-4 align-items-center">
        <div class="col-md-4">
          <div class="brand-mark fs-4">BPMS<span>247</span></div>
          <div class="muted small mt-2">Enterprise CRM Suite • Beta Version</div>
        </div>
        <div class="col-md-8">
          <div class="d-flex flex-wrap justify-content-md-end gap-3">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Support</a>
            <a href="#">Contact</a>
          </div>
        </div>
      </div>

      <div class="muted small mt-4">
        &copy; <?= date('Y') ?> BPMS247. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
