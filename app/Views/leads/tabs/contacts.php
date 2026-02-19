<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Contacts</h5>
    <button class="btn btn-sm btn-primary">Add Contact</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Job Title</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">No contacts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm rounded-circle bg-light text-primary me-2">
                                    <span class="avatar-title"><?= strtoupper(substr($contact['first_name'], 0, 1)) ?></span>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= esc($contact['first_name'] . ' ' . $contact['last_name']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($contact['email']) ?></td>
                        <td><?= esc($contact['phone']) ?></td>
                        <td><?= esc($contact['job_title']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
