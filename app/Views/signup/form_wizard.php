<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .wizard-card {
            max-width: 600px;
            margin: 60px auto;
            background: white;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
        }
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
        }
        .step-dot.active { background: #4facfe; width: 30px; border-radius: 10px; }
        .form-label { font-weight: 600; color: #475569; margin-bottom: 8px; }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
        }
        .btn-primary:hover { opacity: 0.95; }
        .package-summary {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="wizard-card">
        <div class="text-center mb-5">
            <h2 class="fw-bold"><?= $step === 1 ? 'Personal Details' : 'Company Details' ?></h2>
            <p class="text-muted">Step <?= $step ?> of 2</p>
        </div>

        <div class="step-indicator">
            <div class="step-dot <?= $step === 1 ? 'active' : '' ?>"></div>
            <div class="step-dot <?= $step === 2 ? 'active' : '' ?>"></div>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <div class="package-summary d-flex justify-content-between align-items-center">
                <div>
                    <div class="small text-muted">Selected Plan</div>
                    <div class="fw-bold"><?= esc($package['name']) ?></div>
                </div>
                <div class="fw-bold text-primary">$<?= number_format($package['price'], 2) ?>/mo</div>
            </div>

            <?= form_open('signup/company') ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Business Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required placeholder="name@company.com">
                </div>
                <div class="col-12">
                    <label class="form-label">Create Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <small class="text-muted">Minimum 8 characters</small>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary w-100">Continue to Company Details <i class="fa-solid fa-arrow-right ms-2"></i></button>
                    <div class="text-center mt-3">
                        <a href="<?= site_url('signup') ?>" class="text-muted small text-decoration-none">Change Plan</a>
                    </div>
                </div>
            </div>
            <?= form_close() ?>

        <?php else: ?>
            
            <?= form_open('signup/submit') ?>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= old('company_name') ?>" required placeholder="ABC Infrastructure Pvt Ltd">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Industry</label>
                    <select name="industry" class="form-select form-control" required>
                        <option value="Construction">Construction</option>
                        <option value="Real Estate">Real Estate</option>
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Architecture">Architecture</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Number of Employees</label>
                    <input type="number" name="employee_count" class="form-control" value="<?= old('employee_count', 10) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="<?= old('country', 'USA') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency</label>
                    <select name="currency" class="form-select form-control" required>
                        <option value="USD" <?= old('currency') == 'USD' ? 'selected' : '' ?>>USD</option>
                        <option value="INR" <?= old('currency') == 'INR' ? 'selected' : '' ?>>INR</option>
                        <option value="EUR" <?= old('currency') == 'EUR' ? 'selected' : '' ?>>EUR</option>
                        <option value="GBP" <?= old('currency') == 'GBP' ? 'selected' : '' ?>>GBP</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-select form-control" required>
                        <option value="UTC">UTC</option>
                        <option value="America/New_York">Eastern Time (US & Canada)</option>
                        <option value="Asia/Kolkata">India Standard Time (IST)</option>
                        <option value="Europe/London">London</option>
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary w-100">Proceed to Payment <i class="fa-solid fa-credit-card ms-2"></i></button>
                    <div class="text-center mt-3">
                        <a href="javascript:history.back()" class="text-muted small text-decoration-none">Back</a>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
