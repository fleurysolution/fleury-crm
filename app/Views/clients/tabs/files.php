<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Files</h5>
    <button class="btn btn-sm btn-primary">Upload File</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Uploaded By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($files)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">No files found.</td></tr>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td><i class="fa-solid fa-file me-2 text-muted"></i> <?= esc($file['file_name']) ?></td>
                        <td><?= esc($file['file_size']) ?> MB</td>
                       <td>Me</td>
                        <td><?= esc($file['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
