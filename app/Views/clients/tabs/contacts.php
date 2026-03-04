<div class="card border-0 shadow-none">
    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Contacts</h5>
        <button class="btn btn-primary btn-sm" onclick="openModal('<?= site_url('clients/contact_modal_form') ?>', {client_id: <?= $client_id ?>})">
            <i class="fa-solid fa-plus me-1"></i> New Contact
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Job Title</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contacts)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-user fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No contacts found for this client.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: 600;">
                                        <?= strtoupper(substr($contact['name'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('clients/contact_profile/' . $contact['id']) ?>" class="text-dark fw-bold text-decoration-none">
                                            <?= esc($contact['name'] ?? 'Unknown') ?>
                                        </a>
                                        <?php if (!empty($contact['is_primary'])): ?>
                                            <span class="badge bg-info-subtle text-info ms-2" style="font-size: 0.65rem;">Primary</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($contact['job_title'] ?? '-') ?></td>
                            <td><a href="mailto:<?= esc($contact['email']) ?>" class="text-muted text-decoration-none"><?= esc($contact['email']) ?></a></td>
                            <td><?= esc($contact['phone'] ?? '-') ?></td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-light text-muted border-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="<?= site_url('clients/contact_profile/' . $contact['id']) ?>"><i class="fa-regular fa-eye me-2 text-muted"></i> View</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="openModal('<?= site_url('clients/contact_modal_form') ?>', {id: <?= $contact['id'] ?>})"><i class="fa-solid fa-pen me-2 text-muted"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteItem('<?= site_url('clients/delete_contact') ?>', <?= $contact['id'] ?>)"><i class="fa-solid fa-trash me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Simple helper for modals if not already global
function openModal(url, data = {}) {
    // Implementation depends on your modal system
    // For now, assuming a global modal function or we can implement a basic one
    console.log('Open modal:', url, data);
    // You likely have a global 'app_modal' or similar from legacy or new system
    // If not, we should implement a generic modal handler.
}
</script>
