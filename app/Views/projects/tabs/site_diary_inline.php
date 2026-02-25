<?php
// app/Views/projects/tabs/site_diary_inline.php
$diaryModel = new \App\Models\SiteDiaryModel();
$entries    = $diaryModel->forProject($project['id'], 30);
$statusColors = ['draft'=>'secondary','submitted'=>'warning','approved'=>'success'];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="text-muted small"><?= count($entries) ?> entries (latest 30)</div>
    <a href="<?= site_url("projects/{$project['id']}/site-diary/create") ?>" class="btn btn-sm btn-primary">
        <i class="fa-solid fa-plus me-1"></i>Today's Entry
    </a>
</div>

<?php if (empty($entries)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-book-open fa-2x mb-2 opacity-25 d-block"></i>
    No site diary entries yet. <a href="<?= site_url("projects/{$project['id']}/site-diary/create") ?>">Start today's entry →</a>
</div>
<?php else: ?>
<div class="list-group list-group-flush border-0">
<?php foreach ($entries as $e):
    $entryDate = date('D, d M Y', strtotime($e['entry_date']));
?>
<a href="<?= site_url("projects/{$project['id']}/site-diary/{$e['id']}") ?>"
   class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 border-bottom py-3">
    <div class="text-center" style="min-width:55px;">
        <div class="fw-bold text-primary fs-5"><?= date('d', strtotime($e['entry_date'])) ?></div>
        <div class="text-muted small"><?= date('M Y', strtotime($e['entry_date'])) ?></div>
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold"><?= $entryDate ?></span>
            <span class="badge bg-<?= $statusColors[$e['status']] ?>-subtle text-<?= $statusColors[$e['status']] ?>"><?= ucfirst($e['status']) ?></span>
        </div>
        <div class="text-muted small mt-1 d-flex gap-3">
            <?php if ($e['weather']): ?>
            <span><i class="fa-solid fa-cloud-sun me-1"></i><?= esc($e['weather']) ?> <?= esc($e['temperature'] ?? '') ?></span>
            <?php endif; ?>
            <span><i class="fa-solid fa-people-group me-1"></i><?= $e['manpower_count'] ?> workers</span>
            <?php if ($e['working_hours'] > 0): ?>
            <span><i class="fa-solid fa-clock me-1"></i><?= $e['working_hours'] ?>h</span>
            <?php endif; ?>
        </div>
        <?php if ($e['notes']): ?>
        <div class="text-muted mt-1" style="font-size:.75rem;"><?= esc(substr($e['notes'],0,100)) ?>…</div>
        <?php endif; ?>
    </div>
    <div class="text-muted small"><?= esc($e['creator_name'] ?? '') ?></div>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>
