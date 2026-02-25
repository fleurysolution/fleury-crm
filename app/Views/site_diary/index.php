<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Site Diary</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small text-muted">
                    <li class="breadcrumb-item"><a href="<?= site_url('projects') ?>" class="text-decoration-none text-muted">Projects</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url("projects/{$project['id']}") ?>" class="text-decoration-none text-muted"><?= esc($project['title']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site Diary</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url("projects/{$project['id']}/site-diary/create") ?>" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
            <i class="fa-solid fa-plus"></i> New Daily Entry
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4" style="width:140px;">Date</th>
                            <th style="width:120px;">Weather</th>
                            <th style="width:120px;">Manpower</th>
                            <th>Recent Notes</th>
                            <th style="width:120px;">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-book-open fa-2x mb-3 opacity-25"></i>
                                <p class="mb-0">No site diary entries found for this project.</p>
                                <a href="<?= site_url("projects/{$project['id']}/site-diary/create") ?>" class="btn btn-sm btn-link mt-2">Create the first entry</a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($entries as $e): 
                                $statusCol = ['draft'=>'secondary','submitted'=>'warning','approved'=>'success'][$e['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($e['entry_date'])) ?></div>
                                    <div class="text-muted extra-small"><?= date('l', strtotime($e['entry_date'])) ?></div>
                                </td>
                                <td>
                                    <span class="text-muted small"><i class="fa-solid fa-cloud-sun me-1 opacity-50"></i><?= esc($e['weather'] ?: '—') ?></span>
                                </td>
                                <td>
                                    <span class="text-muted small"><i class="fa-solid fa-people-group me-1 opacity-50"></i><?= $e['manpower_count'] ?></span>
                                </td>
                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 300px;">
                                        <?= esc($e['notes'] ?: 'No overall notes recorded.') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusCol ?>-subtle text-<?= $statusCol ?> px-3 py-1 rounded-pill small fw-semibold">
                                        <?= ucfirst($e['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= site_url("projects/{$project['id']}/site-diary/{$e['id']}") ?>" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.extra-small { font-size: 0.7rem; }
</style>
<?= $this->endSection() ?>
