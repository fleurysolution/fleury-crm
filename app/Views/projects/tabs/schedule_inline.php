<?php
// app/Views/projects/tabs/schedule_inline.php
// Project-level schedule tab — shows upcoming deadlines and events for this project
$projectId = (int)($project['id'] ?? 0);
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h6 class="fw-semibold mb-0"><i class="fa-solid fa-calendar-day me-2 text-primary"></i>Schedule &amp; Deadlines</h6>
    <a href="<?= site_url('calendar') ?>?project_id=<?= $projectId ?>" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-calendar-days me-1"></i>Open Calendar
    </a>
</div>

<div class="row g-3 mb-3">
    <!-- Upcoming Tasks -->
    <div class="col-lg-6">
        <div class="card border h-100" style="border-radius:10px;">
            <div class="card-header bg-transparent py-2">
                <strong class="small">📋 Upcoming Task Deadlines</strong>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small" id="upcomingTasks">
                    <div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm"></div></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Upcoming Milestones -->
    <div class="col-lg-6">
        <div class="card border h-100" style="border-radius:10px;">
            <div class="card-header bg-transparent py-2">
                <strong class="small">🏁 Upcoming Milestones</strong>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small" id="upcomingMilestones">
                    <div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mini calendar -->
<div class="card border" style="border-radius:10px;">
    <div class="card-header bg-transparent py-2">
        <strong class="small">📅 Project Calendar</strong>
    </div>
    <div class="card-body p-2">
        <div id="miniCalendar" style="min-height:350px;"></div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
(function(){
    const PID = <?= $projectId ?>;
    const now = new Date().toISOString().split('T')[0];

    // Load upcoming tasks
    fetch(`<?= site_url('calendar/events') ?>?start=${now}&end=2099-12-31&project_id=${PID}`, {
        headers:{'X-Requested-With':'XMLHttpRequest'}
    }).then(r=>r.json()).then(items => {
        const tasks = items.filter(i=>i.source==='task').slice(0,8);
        const miles = items.filter(i=>i.source==='milestone').slice(0,8);

        const tEl = document.getElementById('upcomingTasks');
        tEl.innerHTML = tasks.length ? tasks.map(t => `
            <div class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                <span>${t.title}</span>
                <span class="badge rounded-pill" style="background:${t.color};font-size:10px;">${t.start?.split('T')[0] || t.start}</span>
            </div>`).join('') : '<div class="text-muted text-center py-3 small">No upcoming task deadlines</div>';

        const mEl = document.getElementById('upcomingMilestones');
        mEl.innerHTML = miles.length ? miles.map(m => `
            <div class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                <span>${m.title}</span>
                <span class="badge rounded-pill" style="background:${m.color};font-size:10px;">${m.start?.split('T')[0] || m.start}</span>
            </div>`).join('') : '<div class="text-muted text-center py-3 small">No upcoming milestones</div>';
    });

    // Mini calendar
    const calEl = document.getElementById('miniCalendar');
    const cal   = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        height: 360,
        headerToolbar: { left:'prev,next', center:'title', right:'' },
        events: function(info, cb) {
            fetch(`<?= site_url('calendar/events') ?>?start=${info.startStr}&end=${info.endStr}&project_id=${PID}`,
                {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r=>r.json()).then(cb);
        },
        eventDidMount: function(info) { info.el.title = info.event.title; },
    });
    cal.render();
})();
</script>
