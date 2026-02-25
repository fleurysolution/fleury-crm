<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-globe text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Localization Settings</h5>
        <small class="text-muted">Timezone, currency and date/time preferences</small>
    </div>
</div>

<?= form_open('settings/save_localization_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="localization">

<div class="settings-section-hdr">Date & Time</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="timezone" class="form-label">Timezone</label>
        <select name="timezone" id="timezone" class="form-select">
            <?php foreach(timezone_identifiers_list() as $tz): ?>
            <option value="<?= $tz ?>" <?= setting('timezone')==$tz ? 'selected':'' ?>><?= $tz ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="date_format" class="form-label">Date Format</label>
        <select name="date_format" id="date_format" class="form-select">
            <option value="Y-m-d"   <?= setting('date_format')=='Y-m-d'   ? 'selected':'' ?>>Y-m-d (2024-12-31)</option>
            <option value="d-m-Y"   <?= setting('date_format')=='d-m-Y'   ? 'selected':'' ?>>d-m-Y (31-12-2024)</option>
            <option value="m/d/Y"   <?= setting('date_format')=='m/d/Y'   ? 'selected':'' ?>>m/d/Y (12/31/2024)</option>
            <option value="d/m/Y"   <?= setting('date_format')=='d/m/Y'   ? 'selected':'' ?>>d/m/Y (31/12/2024)</option>
            <option value="M d, Y"  <?= setting('date_format')=='M d, Y'  ? 'selected':'' ?>>M d, Y (Dec 31, 2024)</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="time_format" class="form-label">Time Format</label>
        <select name="time_format" id="time_format" class="form-select">
            <option value="h:i A" <?= setting('time_format')=='h:i A' ? 'selected':'' ?>>12-hour (02:30 PM)</option>
            <option value="H:i"   <?= setting('time_format')=='H:i'   ? 'selected':'' ?>>24-hour (14:30)</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="first_day_of_week" class="form-label">First Day of Week</label>
        <select name="first_day_of_week" id="first_day_of_week" class="form-select">
            <option value="0" <?= setting('first_day_of_week')=='0' ? 'selected':'' ?>>Sunday</option>
            <option value="1" <?= setting('first_day_of_week')=='1' ? 'selected':'' ?>>Monday</option>
            <option value="6" <?= setting('first_day_of_week')=='6' ? 'selected':'' ?>>Saturday</option>
        </select>
    </div>
</div>

<div class="settings-section-hdr">Currency</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="default_currency" class="form-label">Currency Code</label>
        <input type="text" name="default_currency" id="default_currency" class="form-control"
               value="<?= esc(setting('default_currency','USD')) ?>" placeholder="USD">
        <div class="form-text">ISO 4217 code, e.g. USD, EUR, INR</div>
    </div>
    <div class="col-md-4">
        <label for="currency_symbol" class="form-label">Currency Symbol</label>
        <input type="text" name="currency_symbol" id="currency_symbol" class="form-control"
               value="<?= esc(setting('currency_symbol','$')) ?>" placeholder="$">
    </div>
    <div class="col-md-4">
        <label for="currency_position" class="form-label">Symbol Position</label>
        <select name="currency_position" id="currency_position" class="form-select">
            <option value="left"  <?= setting('currency_position')=='left'  ? 'selected':'' ?>>Left ($100)</option>
            <option value="right" <?= setting('currency_position')=='right' ? 'selected':'' ?>>Right (100$)</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="decimal_separator" class="form-label">Decimal Separator</label>
        <select name="decimal_separator" id="decimal_separator" class="form-select">
            <option value="." <?= setting('decimal_separator')=='.' ? 'selected':'' ?>>Dot ( 1,000.00 )</option>
            <option value="," <?= setting('decimal_separator')==',' ? 'selected':'' ?>>Comma ( 1.000,00 )</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="no_of_decimals" class="form-label">Decimal Places</label>
        <select name="no_of_decimals" id="no_of_decimals" class="form-select">
            <option value="0" <?= setting('no_of_decimals')=='0' ? 'selected':'' ?>>0</option>
            <option value="2" <?= setting('no_of_decimals')=='2' ? 'selected':'' ?>>2</option>
            <option value="3" <?= setting('no_of_decimals')=='3' ? 'selected':'' ?>>3</option>
        </select>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Localization Settings
    </button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
