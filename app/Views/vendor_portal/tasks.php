<?= $this->extend('layouts/vendor_dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Assigned Tasks</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><strong><?= esc($task['title']) ?></strong></td>
                                        <td>
                                            <?php if ($task['priority'] === 'high'): ?>
                                                <span class="badge bg-danger">High</span>
                                            <?php elseif ($task['priority'] === 'medium'): ?>
                                                <span class="badge bg-warning text-dark">Medium</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Low</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($task['status'] === 'done'): ?>
                                                <span class="badge bg-success">Done</span>
                                            <?php elseif ($task['status'] === 'in_progress'): ?>
                                                <span class="badge bg-info">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">To Do</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $task['due_date'] ? date('M j, Y', strtotime((string)$task['due_date'])) : '<span class="text-muted">No Date</span>' ?></td>
                                        <td>
                                            <!-- Basic task status toggle form -->
                                            <?php if ($task['status'] !== 'done'): ?>
                                            <form action="<?= site_url('tasks/' . $task['id'] . '/move') ?>" method="post" style="display:inline-block">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="status" value="done">
                                                <button type="submit" class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Mark Done</button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No Assigned Tasks.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
