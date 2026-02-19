<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="<?= site_url('leads') ?>" style="color: var(--text-muted); font-size: 1.25rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h3 style="margin: 0; color: var(--primary-color);">Create New Lead</h3>
    </div>

    <div class="card">
        <?= form_open('leads/store') ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            <!-- Basic Info -->
             <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Type</label>
                <div style="display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="radio" name="type" value="organization" checked> Organization
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="radio" name="type" value="person"> Person
                    </label>
                </div>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Title / Lead Name <span style="color: var(--error-color)">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Website Inquiry - Acme Corp" value="<?= old('title') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Company Name <span style="color: var(--error-color)">*</span></label>
                <input type="text" name="company_name" class="form-control" placeholder="Acme Corp" value="<?= old('company_name') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Primary Contact Name</label>
                <input type="text" name="contact_name" class="form-control" placeholder="John Doe" value="<?= old('contact_name') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
            </div>
            
             <div class="form-group">
                <label class="form-label">Website</label>
                <input type="url" name="website" class="form-control" placeholder="https://" value="<?= old('website') ?>">
            </div>

            <!-- Address -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= old('address') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="<?= old('city') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control" value="<?= old('state') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip" class="form-control" value="<?= old('zip') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control" value="<?= old('country') ?>">
            </div>

            <!-- Financials -->
            <div class="form-group">
                <label class="form-label">VAT Number</label>
                <input type="text" name="vat_number" class="form-control" value="<?= old('vat_number') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="<?= old('gst_number') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" placeholder="USD" value="<?= old('currency', 'USD') ?>">
            </div>
            
             <div class="form-group">
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control" placeholder="$" value="<?= old('currency_symbol', '$') ?>">
            </div>

            <!-- Meta -->
             <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="proposal">Proposal</option>
                    <option value="won">Won</option>
                    <option value="lost">Lost</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Source</label>
                <select name="source" class="form-control">
                    <option value="website">Website</option>
                    <option value="referral">Referral</option>
                    <option value="cold_call">Cold Call</option>
                    <option value="campaign">Campaign</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
             <div class="form-group">
                <label class="form-label">Owner</label>
                <select name="assigned_to" class="form-control">
                    <option value="">-- Unassigned --</option>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Estimated Value ($)</label>
                <input type="number" name="value" class="form-control" step="0.01" value="<?= old('value') ?>">
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Labels</label>
                <input type="text" name="labels" class="form-control" placeholder="Comma separated labels (e.g. VIP, Urgent)" value="<?= old('labels') ?>">
            </div>

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="<?= site_url('leads') ?>" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Lead</button>
        </div>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
