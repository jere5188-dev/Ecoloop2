<?php
/**
 * Component: section-head
 *
 * Heading row used above a grid of cards.
 *
 * Props:
 *   title      string  heading text                      (required)
 *   sub        string  supporting caption
 *   eyebrow    string  small uppercase kicker
 *   actionText string  right-aligned link label
 *   actionHref string  right-aligned link target
 *   level      int     heading level 2–4                 (default 2)
 */

$level = min(4, max(2, (int) ($props['level'] ?? 2)));
$tag = 'h' . $level;
$actionText = (string) ($props['actionText'] ?? '');
$actionHref = (string) ($props['actionHref'] ?? '');
$eyebrow = (string) ($props['eyebrow'] ?? '');
$sub = (string) ($props['sub'] ?? '');
?>
<div class="section-head">
    <div class="u-grow">
        <?php if ($eyebrow !== ''): ?><p class="t-eyebrow"><?= e($eyebrow) ?></p><?php endif; ?>
        <<?= $tag ?> class="section-head__title"><?= e((string) ($props['title'] ?? '')) ?></<?= $tag ?>>
        <?php if ($sub !== ''): ?><p class="section-head__sub"><?= e($sub) ?></p><?php endif; ?>
    </div>
    <?php if ($actionText !== '' && $actionHref !== ''): ?>
        <a class="link-action section-head__action" href="<?= e(url($actionHref)) ?>">
            <?= e($actionText) ?><?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
        </a>
    <?php endif; ?>
</div>
