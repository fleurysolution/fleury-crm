<!DOCTYPE html>
<html>
<head>
    <title>Role Hierarchy Tree</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        ul { list-style-type: none; }
        .role-node { margin: 4px 0; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h2>Role Hierarchy Tree</h2>
    <a href="<?= site_url('roles') ?>" class="btn btn-secondary mb-3">Back</a>

    <?php
    function renderTree($roles, $parentId = null) {
        $branch = array_filter($roles, fn($r) => $r->parent_id == $parentId);
        if (!$branch) return;
        echo "<ul>";
        foreach ($branch as $r) {
            echo "<li class='role-node'><strong>{$r->title}</strong>";
            renderTree($roles, $r->id);
            echo "</li>";
        }
        echo "</ul>";
    }
    renderTree($tree);
    ?>
</div>
</body>
</html>
