<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Using Font Awesome for icons (CDN for now, can be local later) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .enterprise-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #4338ca;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar container">
        <div class="logo">
            <h2 style="color: var(--primary-color); font-weight: 800; letter-spacing: -0.5px;">BPMS<span style="color: var(--accent-color);">247</span></h2>
        </div>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <a href="#about">About</a>
        </div>
        <div class="auth-buttons">
            <a href="<?= site_url('auth/signin') ?>" class="btn btn-outline" style="margin-right: 0.5rem;">Sign In</a>
            <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary">Get Started</a>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="enterprise-badge">BETA RELEASE v1.0</div>
            <h1 class="hero-title">The Enterprise CRM<br>That Scales With You</h1>
            <p class="hero-subtitle">
                A complete proprietary solution for Team Management, Marketing Automation, Sales, and Accounting. 
                Built for performance, designed for growth.
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem;">
                <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">Start Free Trial</a>
                <a href="#features" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.125rem;">View Features</a>
            </div>
            
            <div style="margin-top: 4rem; position: relative;">
                <div style="background: white; border-radius: var(--radius-lg); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid var(--border-color); padding: 2rem; max-width: 1000px; margin: 0 auto;">
                    <img src="https://via.placeholder.com/1000x500/f1f5f9/94a3b8?text=Premium+Dashboard+Preview" alt="Dashboard Preview" style="width: 100%; border-radius: var(--radius-md);">
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="container" style="padding: 6rem 0;">
        <h2 class="section-title">Everything You Need to Run Your Business</h2>
        
        <div class="feature-grid">
            <!-- 1. Marketing Automation -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-bullhorn"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Marketing Automation</h3>
                <p style="color: var(--text-muted);">Automate your campaigns, track leads, and engage customers with personalized workflows.</p>
            </div>

            <!-- 2. Team Management & RBAC -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Team & RBAC</h3>
                <p style="color: var(--text-muted);">Granular Role-Based Access Control to manage your team's permissions securely.</p>
            </div>

            <!-- 3. Sales & Pipeline -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Sales Intelligence</h3>
                <p style="color: var(--text-muted);">Visualize your pipeline, forecast revenue, and close deals faster with AI-driven insights.</p>
            </div>

            <!-- 4. Inventory & Manufacturing -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-warehouse"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Inventory & Mfg.</h3>
                <p style="color: var(--text-muted);">Track items, manage warehouses, and streamline manufacturing processes seamlessly.</p>
            </div>

            <!-- 5. Accounting & Finance -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Accounting</h3>
                <p style="color: var(--text-muted);">Invoicing, expenses, and financial reports integrated directly into your workflow.</p>
            </div>

            <!-- 6. Helpdesk & Support -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Helpdesk</h3>
                <p style="color: var(--text-muted);">Manage tickets, support your clients, and provide world-class customer service.</p>
            </div>
        </div>
    </section>

    <section class="container" style="padding: 6rem 0; background-color: #f8fafc; border-radius: var(--radius-lg); text-align: center;">
        <h2 class="section-title">White Label & SaaS Ready</h2>
        <p style="max-width: 700px; margin: 0 auto 2rem; color: var(--text-muted);">
            Build your own brand with our fully rebrandable solution. Perfect for agencies and enterprises looking to resell.
        </p>
        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div style="background: white; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 250px;">
                <i class="fa-solid fa-paint-roller" style="font-size: 2rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h4 style="font-weight: 600;">Custom Branding</h4>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 250px;">
                <i class="fa-solid fa-plug" style="font-size: 2rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h4 style="font-weight: 600;">Plugin Manager</h4>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); width: 250px;">
                <i class="fa-solid fa-globe" style="font-size: 2rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h4 style="font-weight: 600;">Multi-Language</h4>
            </div>
        </div>
    </section>

    <div class="footer">
        <div class="container">
            <h2 style="font-weight: 800; letter-spacing: -0.5px; opacity: 0.9;">BPMS247</h2>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Support</a>
                <a href="#">Contact</a>
            </div>
            <p style="margin-top: 2rem; opacity: 0.6; font-size: 0.875rem;">&copy; <?= date('Y') ?> BPMS247. All rights reserved. Beta Version.</p>
        </div>
    </div>

</body>
</html>
