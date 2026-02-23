<?php
/** @var object $model_info */
/** @var bool|null $full_width */
/** @var mixed $topbar */

$fullWidth = !empty($full_width);

$viewClass        = $fullWidth ? '' : 'view-container';
$pageWrapperClass = $fullWidth ? '' : 'page-wrapper';
$pageId           = 'page-content';

if (isset($topbar) && $topbar === false) {
    $pageWrapperClass = '';
    $pageId           = '';
}

$content = $model_info->content ?? '';
if ($content !== '' && $content === strip_tags($content)) {
    $content = nl2br($content);
}
?>
<div<?= $pageId ? ' id="'.$pageId.'"' : '' ?> class="<?= esc($pageWrapperClass) ?> clearfix">
    <div class="<?= esc($viewClass) ?>">
        <?= $content ?>
    </div>
</div>