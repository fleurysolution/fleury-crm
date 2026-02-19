<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Notes</h5>
    <button class="btn btn-sm btn-primary">Add Note</button>
</div>

<div class="row">
     <?php if (empty($notes)): ?>
        <div class="col-12 text-center py-3 text-muted">No notes found.</div>
    <?php else: ?>
        <?php foreach ($notes as $note): ?>
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title fw-bold"><?= esc($note['title']) ?></h6>
                    <p class="card-text text-muted small"><?= esc($note['description']) ?></p>
                     <p class="card-text text-muted small"><i class="fa-solid fa-clock me-1"></i> <?= esc($note['created_at']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
