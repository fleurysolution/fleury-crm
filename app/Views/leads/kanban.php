<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">Leads Kanban</h3>
        <p style="color: var(--text-muted);">Drag and drop to move leads.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="<?= site_url('leads') ?>" class="btn btn-outline">
            <i class="fa-solid fa-list" style="margin-right: 0.5rem;"></i> List View
        </a>
        <a href="<?= site_url('leads/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i> New Lead
        </a>
    </div>
</div>

<div style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 2rem;">
    <?php 
    $columns = [
        'new' => ['label' => 'New', 'color' => '#3b82f6'],
        'contacted' => ['label' => 'Contacted', 'color' => '#f59e0b'],
        'qualified' => ['label' => 'Qualified', 'color' => '#10b981'],
        'proposal' => ['label' => 'Proposal', 'color' => '#8b5cf6'],
        'won' => ['label' => 'Won', 'color' => '#14b8a6'],
        'lost' => ['label' => 'Lost', 'color' => '#ef4444'],
    ];
    ?>

    <?php foreach ($columns as $key => $col): ?>
    <div style="min-width: 280px; width: 280px; background: #f8fafc; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; flex-direction: column;">
        
        <!-- Column Header -->
        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: white; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <div style="font-weight: 600; color: var(--primary-color);">
                <span style="display: inline-block; width: 10px; height: 10px; background: <?= $col['color'] ?>; border-radius: 50%; margin-right: 0.5rem;"></span>
                <?= $col['label'] ?>
            </div>
            <span style="background: #f1f5f9; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">
                <?= count($stages[$key]) ?>
            </span>
        </div>

        <!-- Cards Container -->
        <div style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($stages[$key] as $lead): ?>
            <div class="card" style="padding: 1rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); cursor: grab; background: white;">
                <div style="font-weight: 600; margin-bottom: 0.25rem; color: var(--text-main);"><?= esc($lead['title']) ?></div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem;"><?= esc($lead['company_name']) ?></div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem;">
                    <div style="font-weight: 600; color: var(--text-main);">$<?= number_format($lead['value'], 0) ?></div>
                    <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.75rem;">
                         <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($stages[$key])): ?>
                <div style="text-align: center; color: var(--text-muted); font-size: 0.875rem; font-style: italic; padding: 2rem 0;">
                    Empty
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
