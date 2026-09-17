<?php
/**
 * Component: empty-state
 *
 * Props:
 *   icon       string  icon key           (default 'recycle')
 *   title      string  headline           (required)
 *   desc       string  supporting text
 *   actionText string  button label
 *   actionHref string  button target
 */

$actionText = (string) ($props['actionText'] ?? '');
$actionHref = (string) ($props['actionHref'] ?? '');
$desc = (string) ($props['desc'] ?? '');
?>
<div class="empty-state">
    <span class="empty-state__icon">
        <?php component('icon', ['name' => (string) ($props['icon'] ?? 'recycle'), 'size' => 'xl']); ?>
    </span>
    <h3 class="t-h4"><?= e((string) ($props['title'] ?? '')) ?></h3>
    <?php if ($desc !== ''): ?><p class="t-body t-muted"><?= e($desc) ?></p><?php endif; ?>
    <?php if ($actionText !== '' && $actionHref !== ''): ?>
        <a class="btn btn--primary btn--sm" href="<?= e(url($actionHref)) ?>">
            <?= e($actionText) ?><?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
        </a>
    <?php endif; ?>
</div>
