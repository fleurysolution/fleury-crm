<?php
// app/Views/layouts/partials/bell_dropdown.php
// Included once in the header/navbar. Polls /notifications/count every 30s.
$nm           = new \App\Models\NotificationModel();
$unreadCount  = isset($currentUser['id']) ? $nm->unreadCount((int)$currentUser['id']) : 0;
$recentNotifs = isset($currentUser['id']) ? $nm->forUser((int)$currentUser['id'], 8) : [];
?>
<div class="dropdown" id="bellDropdown">
    <button class="btn btn-link position-relative p-0 text-dark me-3" 
            id="bellBtn" data-bs-toggle="dropdown" aria-expanded="false"
            style="font-size:18px;line-height:1;" title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span id="bellBadge"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size:9px;min-width:16px;<?= $unreadCount ? '' : 'display:none' ?>">
            <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
         style="min-width:340px;max-width:400px;border-radius:12px;overflow:hidden;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
            <span class="fw-semibold small">Notifications</span>
            <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none" id="bellReadAll">
                Mark all read
            </button>
        </div>
        <!-- Items -->
        <ul class="list-unstyled mb-0" id="bellList" style="max-height:340px;overflow-y:auto;">
        <?php if (empty($recentNotifs)): ?>
            <li class="text-center text-muted py-4 small">
                <i class="fa-solid fa-bell-slash opacity-50 fa-lg mb-2 d-block"></i>
                No notifications yet
            </li>
        <?php else: ?>
            <?php foreach ($recentNotifs as $n):
                $icoClass = esc($n['icon'] ?? 'fa-bell');
                $col      = esc($n['color'] ?? 'primary');
                $url      = $n['url'] ? esc($n['url']) : site_url('notifications');
                $ago      = (function($dt) {
                    $d = time() - strtotime($dt ?? 'now');
                    if ($d < 60)    return 'just now';
                    if ($d < 3600)  return floor($d/60).'m ago';
                    if ($d < 86400) return floor($d/3600).'h ago';
                    return floor($d/86400).'d ago';
                })($n['created_at'] ?? 'now');
            ?>
            <li class="border-bottom <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                <a href="<?= $url ?>" class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark bell-item"
                   data-id="<?= (int)$n['id'] ?>">
                    <span class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center text-<?= $col ?>"
                          style="width:32px;height:32px;background:rgba(0,0,0,.05);font-size:13px;margin-top:2px;">
                        <i class="fa-solid <?= $icoClass ?>"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="small fw-semibold"><?= esc($n['title']) ?></div>
                        <?php if ($n['body']): ?><div class="text-muted" style="font-size:11px;"><?= esc($n['body']) ?></div><?php endif; ?>
                        <div class="text-muted" style="font-size:10px;"><?= $ago ?></div>
                    </div>
                    <?php if (!$n['is_read']): ?>
                    <span class="flex-shrink-0 rounded-circle bg-primary" style="width:7px;height:7px;margin-top:6px;"></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>
        <!-- Footer -->
        <div class="text-center border-top bg-light py-2">
            <a href="<?= site_url('notifications') ?>" class="small text-primary text-decoration-none fw-semibold">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
(function(){
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Mark item read on click
document.querySelectorAll('.bell-item').forEach(el => {
    el.addEventListener('click', function() {
        const id = this.dataset.id;
        if (!id) return;
        fetch(`<?= site_url('notifications/') ?>${id}/read`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
        }).catch(err => console.warn('Mark read failed:', err));
        // Visual: remove unread dot
        const dot = this.querySelector('.bg-primary.rounded-circle');
        if (dot) dot.remove();
        this.closest('li')?.classList.remove('bg-light');
    });
});

// Mark all read
document.getElementById('bellReadAll')?.addEventListener('click', function(e) {
    e.stopPropagation();
    fetch('<?= site_url('notifications/read-all') ?>', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
    }).then(() => {
        const badge = document.getElementById('bellBadge');
        if (badge) badge.style.display = 'none';
        document.querySelectorAll('.bell-item .bg-primary.rounded-circle').forEach(d => d.remove());
        document.querySelectorAll('#bellList li.bg-light').forEach(li => li.classList.remove('bg-light'));
    }).catch(err => console.warn('Mark all read failed:', err));
});

// Poll unread count every 30s
function pollBell() {
    fetch('<?= site_url('notifications/count') ?>', {headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(d => {
        const badge = document.getElementById('bellBadge');
        if (!badge) return;
        if (d && d.count !== undefined) {
            if (d.count > 0) {
                badge.textContent = d.count > 9 ? '9+' : d.count;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }
    })
    .catch(err => console.warn('Notification poll failed:', err));
}
setInterval(pollBell, 30000);
})();
</script>
