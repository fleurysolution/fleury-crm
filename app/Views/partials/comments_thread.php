<?php
// app/Views/partials/comments_thread.php
// Reusable comment thread component.
// Usage: include with $entityType, $entityId, $projectId in scope.
//   e.g., include __DIR__ . '/../partials/comments_thread.php';
// Can also be loaded standalone via AJAX.
$et      = $entityType ?? '';
$eid     = (int)($entityId ?? 0);
$pid     = (int)($projectId ?? 0);
$me      = session()->get('user') ?? [];
$meId    = (int)($me['id'] ?? 0);
$meName  = trim((session()->get('user')['first_name'] ?? '') . ' ' . (session()->get('user')['last_name'] ?? '')) ?: 'You';
$meInit  = strtoupper(substr($meName, 0, 1));
?>
<div class="comments-thread mt-2" id="commentsThread_<?= $et ?>_<?= $eid ?>">
    <!-- Thread list -->
    <div id="commentList_<?= $et ?>_<?= $eid ?>" class="mb-3">
        <div class="text-center text-muted py-3 small comments-loading">
            <div class="spinner-border spinner-border-sm"></div> Loading comments…
        </div>
    </div>

    <!-- New comment input -->
    <div class="d-flex gap-2 align-items-start new-comment-area">
        <span class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white flex-shrink-0"
              style="width:30px;height:30px;font-size:12px;"><?= $meInit ?></span>
        <div class="flex-grow-1">
            <textarea id="commentInput_<?= $et ?>_<?= $eid ?>" class="form-control form-control-sm"
                      rows="2" placeholder="Add a comment…"></textarea>
            <div class="d-flex gap-2 mt-1">
                <button class="btn btn-primary btn-sm btn-post-comment"
                        data-et="<?= esc($et) ?>" data-eid="<?= $eid ?>" data-pid="<?= $pid ?>">
                    <i class="fa-solid fa-paper-plane me-1"></i>Post
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const ET   = '<?= addslashes($et) ?>';
    const EID  = <?= $eid ?>;
    const PID  = <?= $pid ?>;
    const ME   = <?= $meId ?>;
    const CSRF_NAME = '<?= csrf_token() ?>';
    let CSRF_HASH   = '<?= csrf_hash() ?>';
    const listEl    = document.getElementById(`commentList_${ET}_${EID}`);

    function timeAgo(dt) {
        if (!dt) return '';
        const d = Math.floor((Date.now() - new Date(dt)) / 1000);
        if (d < 60) return 'just now';
        if (d < 3600) return Math.floor(d/60)+'m ago';
        if (d < 86400) return Math.floor(d/3600)+'h ago';
        return new Date(dt).toLocaleDateString();
    }

    function renderComment(c) {
        const init = (c.author_name || '?').charAt(0).toUpperCase();
        const mine = c.user_id == ME;
        return `<div class="d-flex gap-2 mb-2 comment-item" data-id="${c.id}">
          <span class="rounded-circle bg-${mine?'primary':'secondary'} flex-shrink-0 d-flex align-items-center justify-content-center text-white"
                style="width:28px;height:28px;font-size:11px;">${init}</span>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2">
              <strong class="small">${c.author_name||'System'}</strong>
              <span class="text-muted" style="font-size:10px;">${timeAgo(c.created_at)}</span>
              ${mine ? `<button class="btn btn-link p-0 text-danger btn-del-comment ms-auto" data-id="${c.id}" style="font-size:10px;"><i class="fa-solid fa-trash"></i></button>` : ''}
            </div>
            <div class="small mt-1 comment-body bg-light rounded px-2 py-1" style="border-left:3px solid var(--bs-primary);">${escHtml(c.body)}</div>
          </div>
        </div>`;
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
    }

    function loadComments() {
        fetch(`<?= site_url('comments') ?>?entity_type=${ET}&entity_id=${EID}`, {
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r=>r.json())
        .then(comments => {
            listEl.innerHTML = comments.length
                ? comments.map(renderComment).join('')
                : '<div class="text-muted small px-1">No comments yet. Be the first!</div>';
            bindDeleteBtns();
        });
    }

    function bindDeleteBtns() {
        listEl.querySelectorAll('.btn-del-comment').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                if (!confirm('Delete this comment?')) return;
                fetch(`<?= site_url('comments/') ?>${id}/delete`, {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
                    body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
                }).then(r=>r.json()).then(d=>{
                    if (d.success) this.closest('.comment-item').remove();
                });
            });
        });
    }

    // Post comment
    const postBtn   = document.querySelector(`[data-et="${ET}"][data-eid="${EID}"].btn-post-comment`);
    const inputEl   = document.getElementById(`commentInput_${ET}_${EID}`);

    postBtn?.addEventListener('click', async function() {
        const body = (inputEl.value || '').trim();
        if (!body) return inputEl.focus();
        this.disabled = true;
        const fd = new FormData();
        fd.append('body', body); fd.append('entity_type', ET);
        fd.append('entity_id', EID); fd.append('project_id', PID);
        fd.append(CSRF_NAME, CSRF_HASH);
        const r = await fetch('<?= site_url('comments') ?>', {
            method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
        });
        const d = await r.json();
        this.disabled = false;
        if (d.success) {
            inputEl.value = '';
            listEl.insertAdjacentHTML('beforeend', renderComment(d.comment));
            bindDeleteBtns();
        }
    });

    // Ctrl+Enter to submit
    inputEl?.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') postBtn?.click();
    });

    loadComments();
})();
</script>
