<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Project Meetings</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMeetingModal">
                    <i class="fa-solid fa-plus me-1"></i> Schedule Meeting
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($meetings)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No meetings scheduled yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-primary"><?= esc($meeting['title']) ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width:250px;"><?= esc($meeting['agenda']) ?></small>
                                </td>
                                <td>
                                    <div class="small fw-bold"><?= date('M d, Y', strtotime($meeting['meeting_date'])) ?></div>
                                    <div class="text-muted smallest"><?= esc($meeting['meeting_time']) ?></div>
                                </td>
                                <td><small><?= esc($meeting['location'] ?: 'N/A') ?></small></td>
                                <td>
                                    <?php 
                                        $mStatusClass = match($meeting['status']) {
                                            'scheduled' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default     => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?= $mStatusClass ?>"><?= ucfirst($meeting['status']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewMeeting(<?= $meeting['id'] ?>)">
                                        <i class="fa-solid fa-eye me-1"></i> Details
                                    </button>
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

<!-- Add Meeting Modal -->
<div class="modal fade" id="addMeetingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Schedule New Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("meetings/store/{$project['id']}") ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Meeting Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Weekly Site Coordination">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="meeting_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <input type="time" name="meeting_time" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Location / Link</label>
                            <input type="text" name="location" class="form-control" placeholder="Office A or Zoom Link">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Agenda</label>
                            <textarea name="agenda" class="form-control" rows="4" placeholder="Points to discuss..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewMeeting(id) {
    // For now we just show a message, full detailed view can be expanded later
    alert('Meeting details and minutes logging feature coming soon. ID: ' + id);
}
</script>
