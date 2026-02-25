<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Calendar</h1>
            <p class="text-muted small mb-0 mt-1">Tasks, Milestones &amp; Events</p>
        </div>
        <div class="d-flex gap-2">
            <select id="filterProject" class="form-select form-select-sm" style="max-width:200px;">
                <option value="">All Projects</option>
                <?php foreach ($projects as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= esc($p['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventModal">
                <i class="fa-solid fa-plus me-1"></i>New Event
            </button>
        </div>
    </div>
</div>

<div class="content px-4 pt-3 pb-4">
    <!-- Legend -->
    <div class="d-flex gap-3 flex-wrap mb-3 small">
        <span><span class="badge" style="background:#3b82f6;">&nbsp;</span> Events</span>
        <span><span class="badge" style="background:#6366f1;">&nbsp;</span> Tasks (todo)</span>
        <span><span class="badge" style="background:#f59e0b;">&nbsp;</span> Tasks (in progress)</span>
        <span><span class="badge" style="background:#22c55e;">&nbsp;</span> Done</span>
        <span><span class="badge" style="background:#8b5cf6;">&nbsp;</span> Milestones</span>
        <span><span class="badge" style="background:#ef4444;">&nbsp;</span> Overdue</span>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-3">
            <div id="calendar" style="min-height:600px;"></div>
        </div>
    </div>
</div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content" style="border-radius:12px;">
    <div class="modal-header border-0">
        <h6 class="modal-title fw-semibold" id="eventModalTitle">New Event</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <form id="eventForm">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            <input type="hidden" name="event_id" id="eventId" value="">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="evtTitle" class="form-control" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Start</label>
                    <input type="datetime-local" name="start_date" id="evtStart" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">End</label>
                    <input type="datetime-local" name="end_date" id="evtEnd" class="form-control">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" id="evtType" class="form-select">
                        <option value="meeting">Meeting</option>
                        <option value="inspection">Inspection</option>
                        <option value="deadline">Deadline</option>
                        <option value="reminder">Reminder</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">Color</label>
                    <input type="color" name="color" id="evtColor" class="form-control form-control-color" value="#3b82f6">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Project</label>
                <select name="project_id" id="evtProject" class="form-select">
                    <option value="">— No Project —</option>
                    <?php foreach ($projects as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"><?= esc($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Location</label>
                <input type="text" name="location" id="evtLocation" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <textarea name="description" id="evtDesc" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="all_day" id="evtAllDay" value="1">
                <label class="form-check-label small" for="evtAllDay">All Day</label>
            </div>
        </form>
    </div>
    <div class="modal-footer border-0 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnDeleteEvent">
            <i class="fa-solid fa-trash me-1"></i>Delete
        </button>
        <div class="d-flex gap-2 ms-auto">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" id="btnSaveEvent">
                <i class="fa-solid fa-floppy-disk me-1"></i>Save
            </button>
        </div>
    </div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
const CSRF_NAME = '<?= csrf_token() ?>';
let CSRF_HASH   = '<?= csrf_hash() ?>';
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const calEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        height: 'auto',
        editable: true,
        selectable: true,
        nowIndicator: true,
        eventMaxStack: 3,
        dayMaxEvents: 4,

        events: function(info, successCb) {
            const pid = document.getElementById('filterProject').value;
            const url = `<?= site_url('calendar/events') ?>?start=${info.startStr}&end=${info.endStr}` + (pid ? `&project_id=${pid}` : '');
            fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} })
                .then(r => r.json())
                .then(events => successCb(events));
        },

        // Click on a date → open new event modal
        dateClick: function(info) {
            resetModal();
            document.getElementById('evtStart').value = info.dateStr + (info.allDay ? 'T09:00' : '');
            document.getElementById('evtAllDay').checked = info.allDay;
            new bootstrap.Modal('#eventModal').show();
        },

        // Click on an event → edit if it's a custom event
        eventClick: function(info) {
            const e = info.event;
            if (e.extendedProps.source !== 'event') {
                // Tasks & milestones: navigate to their project
                if (e.url) { window.open(e.url, '_self'); info.jsEvent.preventDefault(); }
                return;
            }
            // Edit custom event
            document.getElementById('eventModalTitle').textContent = 'Edit Event';
            document.getElementById('eventId').value = e.extendedProps.raw_id;
            document.getElementById('evtTitle').value = e.title;
            document.getElementById('evtStart').value = e.startStr.substring(0, 16);
            document.getElementById('evtEnd').value = e.endStr ? e.endStr.substring(0, 16) : '';
            document.getElementById('evtType').value = e.extendedProps.type || 'other';
            document.getElementById('evtColor').value = e.backgroundColor || '#3b82f6';
            document.getElementById('evtLocation').value = e.extendedProps.location || '';
            document.getElementById('evtDesc').value = e.extendedProps.description || '';
            document.getElementById('evtAllDay').checked = e.allDay;
            document.getElementById('btnDeleteEvent').classList.remove('d-none');
            new bootstrap.Modal('#eventModal').show();
        },

        // Drag-drop to reschedule custom event
        eventDrop: function(info) {
            const e = info.event;
            if (e.extendedProps.source !== 'event') { info.revert(); return; }
            const fd = new FormData();
            fd.append(CSRF_NAME, CSRF_HASH);
            fd.append('start_date', e.startStr);
            fd.append('end_date', e.endStr || '');
            fd.append('all_day', e.allDay ? '1' : '0');
            fetch(`<?= site_url('calendar/events/') ?>${e.extendedProps.raw_id}/drag`, {
                method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
            });
        },

        eventResize: function(info) {
            const e = info.event;
            if (e.extendedProps.source !== 'event') { info.revert(); return; }
            const fd = new FormData();
            fd.append(CSRF_NAME, CSRF_HASH);
            fd.append('start_date', e.startStr);
            fd.append('end_date', e.endStr || '');
            fd.append('all_day', e.allDay ? '1' : '0');
            fetch(`<?= site_url('calendar/events/') ?>${e.extendedProps.raw_id}/drag`, {
                method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
            });
        },

        // Tooltip on hover
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            let tip = info.event.title;
            if (props.project) tip += `\n📁 ${props.project}`;
            if (props.location) tip += `\n📍 ${props.location}`;
            if (props.description) tip += `\n${props.description}`;
            info.el.title = tip;
        },
    });

    calendar.render();

    // Filter by project
    document.getElementById('filterProject').addEventListener('change', () => calendar.refetchEvents());

    // Reset modal
    function resetModal() {
        document.getElementById('eventModalTitle').textContent = 'New Event';
        document.getElementById('eventId').value = '';
        document.getElementById('eventForm').reset();
        document.getElementById('evtColor').value = '#3b82f6';
        document.getElementById('btnDeleteEvent').classList.add('d-none');
    }

    // Save event
    document.getElementById('btnSaveEvent').addEventListener('click', async function() {
        const form = document.getElementById('eventForm');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        this.disabled = true;
        const evtId = document.getElementById('eventId').value;
        const url   = evtId
            ? `<?= site_url('calendar/events/') ?>${evtId}/update`
            : '<?= site_url('calendar/events') ?>';
        const fd    = new FormData(form);
        const r     = await fetch(url, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} });
        const d     = await r.json();
        this.disabled = false;
        if (d.success) {
            bootstrap.Modal.getInstance('#eventModal')?.hide();
            calendar.refetchEvents();
            resetModal();
        }
    });

    // Delete event
    document.getElementById('btnDeleteEvent').addEventListener('click', async function() {
        const evtId = document.getElementById('eventId').value;
        if (!evtId || !confirm('Delete this event?')) return;
        const fd = new FormData(); fd.append(CSRF_NAME, CSRF_HASH);
        await fetch(`<?= site_url('calendar/events/') ?>${evtId}/delete`, {
            method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
        });
        bootstrap.Modal.getInstance('#eventModal')?.hide();
        calendar.refetchEvents();
        resetModal();
    });

    // Modal reset on close
    document.getElementById('eventModal').addEventListener('hidden.bs.modal', resetModal);
});
</script>

<?= $this->endSection() ?>
