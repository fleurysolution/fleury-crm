<?php
// app/Views/projects/tabs/overview_inline.php
// Included inside show.php — has access to $project, $stats, $phases, $milestones, $members
?>
<div class="row g-4">
    <!-- Description -->
    <?php if ($project['description']): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-2"><i class="fa-solid fa-align-left me-2 text-primary"></i>Description</h6>
                <p class="text-muted mb-0"><?= nl2br(esc($project['description'])) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Project details -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Project Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7"><?= ucfirst(str_replace('_',' ',$project['status'])) ?></dd>
                    <dt class="col-5 text-muted">Priority</dt>
                    <dd class="col-7"><?= ucfirst($project['priority']) ?></dd>
                    <dt class="col-5 text-muted">Client</dt>
                    <dd class="col-7"><?= esc($project['client_name'] ?? '—') ?></dd>
                    <dt class="col-5 text-muted">PM</dt>
                    <dd class="col-7"><?= esc($project['pm_name'] ?? '—') ?></dd>
                    <dt class="col-5 text-muted">Start</dt>
                    <dd class="col-7"><?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—' ?></dd>
                    <dt class="col-5 text-muted">End</dt>
                    <dd class="col-7"><?= $project['end_date'] ? date('d M Y', strtotime($project['end_date'])) : '—' ?></dd>
                    <dt class="col-5 text-muted">Budget</dt>
                    <dd class="col-7"><?= $project['budget'] ? number_format($project['budget'],2).' '.$project['currency'] : '—' ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Upcoming milestones -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0"><i class="fa-solid fa-flag me-2 text-warning"></i>Milestones</h6>
                    <a href="?tab=milestones" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <?php if (empty($milestones)): ?>
                <p class="text-muted small">No milestones defined.</p>
                <?php else: foreach (array_slice($milestones,0,5) as $ms):
                    $msColor = ['pending'=>'warning','achieved'=>'success','missed'=>'danger'][$ms['status']] ?? 'secondary'; ?>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-flag text-<?= $msColor ?>"></i>
                    <div class="flex-grow-1 small">
                        <span class="fw-semibold"><?= esc($ms['title']) ?></span>
                        <?php if ($ms['due_date']): ?>
                        <span class="text-muted ms-2"><?= date('d M', strtotime($ms['due_date'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="badge bg-<?= $msColor ?>-subtle text-<?= $msColor ?>"><?= ucfirst($ms['status']) ?></span>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- Phases progress -->
    <?php if (!empty($phases)): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-bars-progress me-2 text-primary"></i>WBS Phases</h6>
                <div class="d-flex flex-wrap gap-2">
                <?php foreach ($phases as $ph): ?>
                    <span class="badge rounded-pill px-3 py-2" style="background:<?= esc($ph['color']) ?>20;color:<?= esc($ph['color']) ?>;border:1px solid <?= esc($ph['color']) ?>40;">
                        <?= esc($ph['title']) ?>
                    </span>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
