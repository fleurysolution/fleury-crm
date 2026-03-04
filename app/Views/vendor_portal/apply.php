<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Vendor Registration') ?> · BPMS247</title>

    <!-- Bootstrap 5 CSS – local -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome 6 – local -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: #495057;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            margin: 2rem auto;
            border: 1px solid #e9ecef;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .auth-logo span {
            color: #3498db;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="auth-card mx-auto">
        <div class="auth-logo">
            BPMS<span>247 Vendor</span>
        </div>
        <h3 class="mb-4 text-center">Subcontractor & Vendor Application</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= site_url('vendor/apply') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name *</label>
                <input type="text" name="company_name" id="company_name" class="form-control" value="<?= old('company_name') ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contact_name" class="form-label">Primary Contact *</label>
                    <input type="text" name="contact_name" id="contact_name" class="form-control" value="<?= old('contact_name') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="trade_type" class="form-label">Trade Type</label>
                    <input type="text" name="trade_type" id="trade_type" class="form-control" value="<?= old('trade_type') ?>" placeholder="e.g. Plumbing, Electrical">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= old('phone') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="tax_id" class="form-label">Tax ID (EIN/SSN)</label>
                <input type="text" name="tax_id" id="tax_id" class="form-control" value="<?= old('tax_id') ?>">
            </div>

            <div class="mb-3">
                <label for="w9_file" class="form-label">W9 Form (PDF/Image, max 5MB)</label>
                <input type="file" name="w9_file" id="w9_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="mb-4">
                <label for="insurance_file" class="form-label">Certificate of Insurance (PDF/Image, max 5MB)</label>
                <input type="file" name="insurance_file" id="insurance_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
            </div>
            
            <div class="text-center mt-3">
                 <a href="<?= site_url('auth/signin') ?>">Already a vendor? Sign in.</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
