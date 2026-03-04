<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php
// app/Views/activity/index.php — Global Activity Log (admin)
$actionColors = [
    'created' => 'success', 'updated' => 'primary', 'deleted' => 'danger',
    'approved' => 'info', 'rejected' => 'warning', 'submitted' => 'secondary',
    'paid' => 'success', 'marked_paid' => 'success',
];
$entityIcons = [
    'task'        => 'fa-list-check',
    'project'     => 'fa-folder-open',
    'rfi'         => 'fa-circle-question',
    'submittal'   => 'fa-file-arrow-up',
    'punch_list'  => 'fa-clipboard-check',
    'site_diary'  => 'fa-book-open',
    'contract'    => 'fa-file-contract',
    'boq'         => 'fa-table-list',
    'payment_cert'=> 'fa-file-invoice-dollar',
    'invoice'     => 'fa-receipt',
    'expense'     => 'fa-coins',
];

function timeAgo($datetime) {
    if (!$datetime) return '';
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $time);
}
?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">
    <div class="content-header px-4 pt-4 pb-0">
        <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Global Activity Log</h1>
        <p class="text-muted small mb-0 mt-1">Audit trail of all system activities</p>
    </div>

    <div class="content px-4 pt-3 pb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Action</th>
                                <th>Entity</th>
                                <th>Project</th>
                                <th>User</th>
                                <th class="text-end pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feed)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-clock-rotate-left fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">No activity recorded yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($feed as $log): 
                                    $icon  = $entityIcons[$log['entity_type']] ?? 'fa-cube';
                                    $color = $actionColors[$log['action']] ?? 'secondary';
                                    // Make description fallback
                                    $desc = $log['description'] ?: ucfirst($log['action']) . ' ' . str_replace('_', ' ', $log['entity_type']);
                                ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-<?= $color ?>-subtle text-<?= $color ?> d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                    <i class="fa-solid <?= $icon ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark"><?= esc($desc) ?></div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">ID: <?= esc($log['entity_id']) ?> <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> ms-1" style="font-size:0.65rem;"><?= esc(strtoupper($log['action'])) ?></span></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-secondary fw-medium" style="font-size: 0.85rem; text-transform: capitalize;"><?= esc(str_replace('_', ' ', $log['entity_type'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($log['project_title']): ?>
                                                <a href="<?= site_url('projects/view/' . $log['project_id']) ?>" class="text-decoration-none fw-semibold" style="font-size: 0.85rem;"><?= esc($log['project_title']) ?></a>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center text-secondary small">
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;font-size:0.7rem;">
                                                    <?= strtoupper(substr($log['actor_name'] ?: 'S', 0, 1)) ?>
                                                </div>
                                                <?= esc($log['actor_name'] ?: 'System') ?>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4 text-muted" style="font-size: 0.85rem;" title="<?= esc($log['created_at']) ?>">
                                            <?= timeAgo($log['created_at']) ?>
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
</div>
<?= $this->endSection() ?>
