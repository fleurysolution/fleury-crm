<?php
// app/Views/projects/tabs/activity_inline.php
// Project Activity Feed — live loaded via AJAX inside the project workspace
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="input-group" style="max-width:260px;">
        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
        <input type="text" id="actInlineSearch" class="form-control border-start-0" placeholder="Filter…">
    </div>
    <a href="<?= site_url('activity') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-clock-rotate-left me-1"></i>Global Log
    </a>
</div>

<div id="actInlineContainer">
    <div class="text-center text-muted py-4" id="actInlineLoading">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        Loading activity…
    </div>
    <div id="actInlineList" class="d-none"></div>
</div>

<script>
(function() {
    const projectId = <?= (int)($project['id'] ?? 0) ?>;
    const container = document.getElementById('actInlineList');
    const loading   = document.getElementById('actInlineLoading');

    const actionColors = {
        created:'success', updated:'primary', deleted:'danger',
        approved:'info', rejected:'warning', submitted:'secondary',
        paid:'success', marked_paid:'success',
    };
    const entityIcons = {
        task:'fa-list-check', project:'fa-folder-open', rfi:'fa-circle-question',
        submittal:'fa-file-arrow-up', punch_list:'fa-clipboard-check',
        site_diary:'fa-book-open', contract:'fa-file-contract',
        boq:'fa-table-list', payment_cert:'fa-file-invoice-dollar',
        invoice:'fa-receipt', expense:'fa-coins',
    };

    function timeAgo(dt) {
        if (!dt) return '';
        const diff = Math.floor((Date.now() - new Date(dt).getTime()) / 1000);
        if (diff < 60) return 'just now';
        if (diff < 3600) return Math.floor(diff/60)+'m ago';
        if (diff < 86400) return Math.floor(diff/3600)+'h ago';
        return Math.floor(diff/86400)+'d ago';
    }

    function renderFeed(feed) {
        if (!feed.length) {
            container.innerHTML = '<div class="text-center text-muted py-4"><i class="fa-solid fa-clock-rotate-left fa-2x opacity-40 mb-2"></i><p>No activity recorded yet.</p></div>';
            container.classList.remove('d-none');
            return;
        }

        let html = '<div class="timeline">';
        feed.forEach(row => {
            const col  = actionColors[row.action]  || 'secondary';
            const icon = entityIcons[row.entity_type] || 'fa-circle-dot';
            const entity = (row.entity_type||'').replace(/_/g,' ');
            const initials = (row.actor_name || '?').charAt(0).toUpperCase();
            html += `
            <div class="d-flex gap-3 py-2 border-bottom act-row">
                <div class="flex-shrink-0" style="width:32px;">
                    ${row.actor_avatar
                        ? `<img src="${row.actor_avatar}" class="rounded-circle" width="32" height="32" alt="">`
                        : `<span class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                               style="width:32px;height:32px;font-size:12px;">${initials}</span>`}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${row.actor_name || 'System'}</strong>
                            <span class="badge bg-${col}-subtle text-${col} ms-1 text-capitalize">${row.action}</span>
                            <span class="text-muted small ms-1"><i class="fa-solid ${icon}"></i> ${entity} #${row.entity_id}</span>
                        </div>
                        <span class="text-muted flex-shrink-0" style="font-size:11px;">${timeAgo(row.created_at)}</span>
                    </div>
                    ${row.description ? `<div class="small text-muted mt-1">${row.description}</div>` : ''}
                </div>
            </div>`;
        });
        html += '</div>';
        container.innerHTML = html;
        container.classList.remove('d-none');
    }

    // Fetch feed
    fetch(`<?= site_url('projects/') ?>${projectId}/activity`, {
        headers: {'X-Requested-With':'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        loading.classList.add('d-none');
        renderFeed(data);
    })
    .catch(() => {
        loading.innerHTML = '<span class="text-danger">Failed to load activity.</span>';
    });

    // Client-side search
    document.getElementById('actInlineSearch')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#actInlineList .act-row').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
})();
</script>
