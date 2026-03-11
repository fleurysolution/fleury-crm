<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><?= esc($drawing['drawing_number']) ?> · <?= esc($drawing['title']) ?></h4>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 small text-muted">
                <li class="breadcrumb-item"><a href="<?= site_url("projects/{$drawing['project_id']}?tab=drawings") ?>" class="text-decoration-none text-muted">Drawings</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($drawing['drawing_number']) ?></li>
              </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark btn-sm" onclick="togglePins()">
                <i class="fa-solid fa-location-dot me-1"></i> Toggle Pins
            </button>
            <button class="btn btn-primary btn-sm" id="addPinBtn">
                <i class="fa-solid fa-plus me-1"></i> Add Pin
            </button>
        </div>
    </div>

    <!-- Drawing Canvas Container -->
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius:15px; background: #f8f9fa;">
        <div class="card-body p-0 position-relative" id="drawingContainer" style="min-height: 80vh; cursor: crosshair;">
            <img id="drawingImage" src="<?= base_url('uploads/drawings/' . $revision['filepath']) ?>" 
                 class="img-fluid w-100" style="display:block;">
            
            <!-- Pins will be rendered here -->
            <div id="pinsOverlay" class="position-absolute top-0 start-0 w-100 h-100 pointer-events-none">
                <?php foreach ($pins as $pin): ?>
                    <div class="drawing-pin" 
                         style="left: <?= $pin['pos_x'] ?>%; top: <?= $pin['pos_y'] ?>%;"
                         data-bs-toggle="tooltip" title="<?= esc($pin['content']) ?>">
                        <i class="fa-solid fa-location-dot text-<?= $pin['pin_type'] === 'rfi' ? 'danger' : 'warning' ?>"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Pin Content -->
<div class="modal fade" id="addPinModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">New Pin Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Type</label>
                    <select id="pinType" class="form-select form-select-sm">
                        <option value="note">General Note</option>
                        <option value="rfi">RFI Link</option>
                        <option value="observation">Observation</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Notes</label>
                    <textarea id="pinContent" class="form-control form-control-sm" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary btn-sm w-100" onclick="savePin()">Save Pin</button>
            </div>
        </div>
    </div>
</div>

<style>
.drawing-pin {
    position: absolute;
    transform: translate(-50%, -100%);
    font-size: 24px;
    cursor: pointer;
    transition: transform 0.2s;
    filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
}
.drawing-pin:hover {
    transform: translate(-50%, -110%) scale(1.2);
}
#drawingImage {
    user-select: none;
    -webkit-user-drag: none;
}
</style>

<script>
let isAddingPin = false;
let pendingPin = { x: 0, y: 0 };

document.getElementById('addPinBtn').addEventListener('click', () => {
    isAddingPin = !isAddingPin;
    document.getElementById('addPinBtn').classList.toggle('btn-primary');
    document.getElementById('addPinBtn').classList.toggle('btn-success');
    document.getElementById('drawingContainer').style.cursor = isAddingPin ? 'crosshair' : 'default';
});

document.getElementById('drawingContainer').addEventListener('click', (e) => {
    if (!isAddingPin) return;
    
    const rect = e.currentTarget.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    
    pendingPin = { x, y };
    const modal = new bootstrap.Modal(document.getElementById('addPinModal'));
    modal.show();
});

function savePin() {
    const data = {
        revision_id: <?= $revision['id'] ?>,
        pos_x: pendingPin.x,
        pos_y: pendingPin.y,
        pin_type: document.getElementById('pinType').value,
        content: document.getElementById('pinContent').value
    };

    fetch('<?= site_url("drawings/addPin/{$drawing['id']}") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}

function togglePins() {
    const overlay = document.getElementById('pinsOverlay');
    overlay.style.display = (overlay.style.display === 'none') ? 'block' : 'none';
}

// Init tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
<?= $this->endSection() ?>
