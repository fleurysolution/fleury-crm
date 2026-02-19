<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Events</h5>
    <button class="btn btn-sm btn-primary">New Event</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Title</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($events)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">No events found.</td></tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td class="fw-bold"><?= esc($event['title']) ?></td>
                        <td><?= esc($event['start_date']) ?></td>
                        <td><?= esc($event['end_date']) ?></td>
                         <td><?= esc($event['location'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
