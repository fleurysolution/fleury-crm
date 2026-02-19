<?php
function render_tree($tree) {
    echo "<ul class='role-tree'>";
    foreach ($tree as $node) {
        echo "<li><strong>" . esc($node['title']) . "</strong>";
        if (!empty($node['children'])) {
            render_tree($node['children']);
        }
        echo "</li>";
    }
    echo "</ul>";
}
?>

<style>
.role-tree { list-style: none; padding-left: 15px; }
.role-tree li { margin-bottom: 6px; }
.role-tree li::before {
    content: "• ";
    color: #007bff;
}
</style>

<div>
    <?php render_tree($tree); ?>
</div>
