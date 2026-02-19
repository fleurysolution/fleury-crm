<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Approval Settings</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#approvalFormSection">
                + Add New
            </button>
        </div>

        <div class="card-body">
            <div id="approvalFormSection" class="collapse">
                <?= view('approval_settings/_form', [
                    'modules' => $modules,
                    'roles' => $roles,
                    'users' => $users
                ]) ?>
            </div>

            <hr>

            <div id="approvalTable">
                <?= view('approval_settings/_table', ['settings' => $settings]) ?>
            </div>
        </div>
    </div>
</div>
