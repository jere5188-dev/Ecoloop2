<?php
/**
 * Component: icon
 *
 * Minimal, line-based, nature-inspired iconography on a 24px grid
 * (styleguide 06). Rendered as inline SVG so icons inherit `currentColor`
 * and cost zero HTTP requests (requirements R7.8, N3.3).
 *
 * Props:
 *   name  string  icon key (required)
 *   size  string  '' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'   default 'md'
 *   class string  extra classes
 *   label string  accessible name; when omitted the icon is aria-hidden
 *   fill  bool    use fill rendering instead of stroke (default false)
 */

$name = (string) ($props['name'] ?? 'leaf');
$size = (string) ($props['size'] ?? 'md');
$extra = (string) ($props['class'] ?? '');
$label = (string) ($props['label'] ?? '');
$stroke = !($props['fill'] ?? false);

$paths = [
    /* --- Navigation ------------------------------------------------------ */
    'home'      => '<path d="M4 10.4 12 4l8 6.4V19a1 1 0 0 1-1 1h-4.5v-5h-5v5H5a1 1 0 0 1-1-1z"/>',
    'passbook'  => '<rect x="4" y="4" width="16" height="16" rx="2.5"/><path d="M8 4v16"/><path d="M11.5 9h5M11.5 13h5"/>',
    'recycle'   => '<path d="M8.4 6.7 10 4h4l1.4 2.4"/><path d="m17.6 9.6 2 3.4-2.3 1.3"/><path d="m6.4 9.6-2 3.4 2.3 1.3"/><path d="M9 20h6l1.5-2.6"/><path d="m7.5 17.4L9 20"/><path d="M12 8.5 13.6 11h-3.2z"/>',
    'map'       => '<path d="M9 4 4 6v14l5-2 6 2 5-2V4l-5 2z"/><path d="M9 4v14M15 6v14"/>',
    'trophy'    => '<path d="M8 4h8v5a4 4 0 0 1-8 0z"/><path d="M8 5.5H5.5V7a3 3 0 0 0 3 3"/><path d="M16 5.5h2.5V7a3 3 0 0 1-3 3"/><path d="M12 13v3"/><path d="M8.5 20h7l-.7-3.2H9.2z"/>',
    'profile'   => '<circle cx="12" cy="8.5" r="3.5"/><path d="M4.8 20a7.4 7.4 0 0 1 14.4 0"/>',
    'leaf'      => '<path d="M19.5 4.5c0 8-5 13.5-12.5 14.5C7 11 12 5.5 19.5 4.5Z"/><path d="M7 19c1.8-5 4.8-8.4 8.6-10.4"/>',
    'camera'    => '<path d="M4 8.8A1.8 1.8 0 0 1 5.8 7h1.9l1.2-2h6.2l1.2 2h1.9A1.8 1.8 0 0 1 20 8.8v8.4A1.8 1.8 0 0 1 18.2 19H5.8A1.8 1.8 0 0 1 4 17.2z"/><circle cx="12" cy="13" r="3.2"/>',
    'calendar'  => '<rect x="4" y="5.5" width="16" height="14.5" rx="2.2"/><path d="M4 10h16"/><path d="M8.5 3.5v4M15.5 3.5v4"/><path d="M8 14h2.5M13.5 14H16M8 17h2.5"/>',
    'location'  => '<path d="M12 21c4-4.4 6-7.6 6-10.4A6 6 0 0 0 6 10.6C6 13.4 8 16.6 12 21Z"/><circle cx="12" cy="10.4" r="2.4"/>',
    'settings'  => '<circle cx="12" cy="12" r="2.8"/><path d="m19.3 14.4-.6 1.4 1 1.7-1.5 1.5-1.7-1-1.4.6-.6 1.9h-2.1l-.6-1.9-1.4-.6-1.7 1L5.3 17.5l1-1.7-.6-1.4-1.9-.6v-2.1l1.9-.6.6-1.4-1-1.7 1.5-1.5 1.7 1 1.4-.6.6-1.9h2.1l.6 1.9 1.4.6 1.7-1 1.5 1.5-1 1.7.6 1.4 1.9.6v2.1z"/>',
    'menu'      => '<path d="M4 7h16M4 12h16M4 17h11"/>',
    'search'    => '<circle cx="11" cy="11" r="6"/><path d="m20 20-4.7-4.7"/>',
    'bell'      => '<path d="M6.5 10.5a5.5 5.5 0 0 1 11 0c0 3.4.7 5 1.5 6h-14c.8-1 1.5-2.6 1.5-6Z"/><path d="M10 19.5a2.2 2.2 0 0 0 4 0"/>',
    'truck'     => '<path d="M3 7.5h10.5V16H3z"/><path d="M13.5 10.5H17l3 3V16h-6.5z"/><circle cx="7" cy="17.8" r="1.9"/><circle cx="16.5" cy="17.8" r="1.9"/>',
    'building'  => '<path d="M5 20V5.5A1.5 1.5 0 0 1 6.5 4h7A1.5 1.5 0 0 1 15 5.5V20"/><path d="M15 10h3.2A1.8 1.8 0 0 1 20 11.8V20"/><path d="M8 8h4M8 11.5h4M8 15h4M3.5 20h17"/>',
    'users'     => '<circle cx="9.5" cy="8.5" r="3"/><path d="M3.8 19a5.9 5.9 0 0 1 11.4 0"/><path d="M15.5 6.2a3 3 0 0 1 0 5.9"/><path d="M17 14.6a5.9 5.9 0 0 1 3.2 3.4"/>',

    /* --- Materials ------------------------------------------------------- */
    'bottle'    => '<path d="M10 3.5h4v2.2c0 1 .4 1.6 1 2.3.7.8 1 1.6 1 2.7v8.1A1.2 1.2 0 0 1 14.8 20H9.2A1.2 1.2 0 0 1 8 18.8v-8.1c0-1.1.3-1.9 1-2.7.6-.7 1-1.3 1-2.3z"/><path d="M8.2 13h7.6"/>',
    'paper'     => '<path d="M6 3.8h7.5L18 8.3V20a.9.9 0 0 1-.9.9H6.9A.9.9 0 0 1 6 20z"/><path d="M13.3 3.8v4.6H18"/><path d="M9 13h6M9 16.4h4"/>',
    'cardboard' => '<path d="M3.6 8.4 12 5l8.4 3.4V17L12 20.4 3.6 17z"/><path d="M3.6 8.4 12 11.8l8.4-3.4M12 11.8v8.6"/>',
    'can'       => '<rect x="8" y="4" width="8" height="16" rx="2.4"/><path d="M8.3 7.6h7.4M8.3 16.4h7.4"/><path d="M11 4.2v3"/>',

    /* --- Metrics & data -------------------------------------------------- */
    'chart'     => '<path d="M4 20h16"/><path d="M7 20v-6M12 20V7M17 20v-9"/>',
    'weight'    => '<path d="M5 20 6.8 9.4h10.4L19 20z"/><path d="M9.4 9.4a2.6 2.6 0 1 1 5.2 0"/><path d="M9.5 14.6h5"/>',
    'coin'      => '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M14.3 9.6a2.6 2.6 0 0 0-4.6 1.4c0 2.5 4.6 1 4.6 3.4a2.6 2.6 0 0 1-4.6-1.4"/>',
    'drop'      => '<path d="M12 3.8c3 3.6 5 6.2 5 8.7a5 5 0 0 1-10 0c0-2.5 2-5.1 5-8.7Z"/>',
    'bolt'      => '<path d="M13.4 3.5 6.8 13.4h4.3l-.9 7.1 6.6-9.9h-4.3z"/>',
    'tree'      => '<path d="M12 3.6l4.6 6.2h-2.4l3.4 4.8H6.4l3.4-4.8H7.4z"/><path d="M12 14.6V20.4"/><path d="M9.4 20.4h5.2"/>',
    'sprout'    => '<path d="M12 20.4v-6.6"/><path d="M12 13.8C8.7 13.8 6.6 12 6.6 8.6c3.4 0 5.4 1.8 5.4 5.2Z"/><path d="M12 13.8c0-3.4 2-5.2 5.4-5.2 0 3.4-2.1 5.2-5.4 5.2Z"/><path d="M8.6 20.4h6.8"/>',
    'scan'      => '<path d="M4 8.6V6.4A2.4 2.4 0 0 1 6.4 4h2.2"/><path d="M15.4 4h2.2A2.4 2.4 0 0 1 20 6.4v2.2"/><path d="M20 15.4v2.2a2.4 2.4 0 0 1-2.4 2.4h-2.2"/><path d="M8.6 20H6.4A2.4 2.4 0 0 1 4 17.6v-2.2"/><path d="M4.8 12h14.4"/>',
    'target'    => '<circle cx="12" cy="12" r="7.6"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1.1" fill="currentColor" stroke="none"/>',
    'flame'     => '<path d="M12 20.4c3.1 0 5.4-2.1 5.4-5.1 0-4.1-4-5.4-3.2-11.7-3 1.4-4.6 4-4.6 6.2 0 1.5-1 1.9-1.7 1.1-.7-.8-.9-1.6-.9-2.4-1 1.4-1.4 3.2-1.4 4.8 0 4.1 2.6 7.1 6.4 7.1Z"/>',

    /* --- Gamification ---------------------------------------------------- */
    'medal'     => '<circle cx="12" cy="14.6" r="5"/><path d="m8.6 10.2L6.4 3.8h11.2l-2.2 6.4"/><path d="m12 12.4 1 2h2.1l-1.7 1.4.6 2.1-2-1.2-2 1.2.6-2.1-1.7-1.4H11z" stroke-width="1.2"/>',
    'gift'      => '<rect x="4" y="9" width="16" height="11" rx="1.8"/><path d="M4 13h16"/><path d="M12 9v11"/><path d="M12 9C10.6 6.6 9.4 5.5 8 5.5A2 2 0 0 0 8 9z"/><path d="M12 9c1.4-2.4 2.6-3.5 4-3.5A2 2 0 0 1 16 9z"/>',
    'voucher'   => '<path d="M3.6 8.4A1.4 1.4 0 0 1 5 7h14a1.4 1.4 0 0 1 1.4 1.4v1.9a1.9 1.9 0 0 0 0 3.4v1.9A1.4 1.4 0 0 1 19 17H5a1.4 1.4 0 0 1-1.4-1.4v-1.9a1.9 1.9 0 0 0 0-3.4z"/><path d="M9 10.6v2.8"/>',
    'shirt'     => '<path d="M9 4 5 6.2l1.4 3.6 1.6-.6V20h8V9.2l1.6.6L19 6.2 15 4a3 3 0 0 1-6 0Z"/>',
    'certificate' => '<rect x="4" y="4" width="16" height="12" rx="1.8"/><path d="M8 8h8M8 11.4h4.5"/><circle cx="16" cy="17.4" r="2.6"/><path d="m14.4 19.4-.6 2.2 2.2-1.1 2.2 1.1-.6-2.2"/>',
    'sparkle'   => '<path d="M12 3.6l1.7 4.7 4.7 1.7-4.7 1.7L12 16.4l-1.7-4.7L5.6 10l4.7-1.7z"/><path d="M18.4 16.2l.7 1.7 1.7.7-1.7.7-.7 1.7-.7-1.7-1.7-.7 1.7-.7z"/>',
    'clipboard' => '<rect x="5.5" y="5" width="13" height="15" rx="1.9"/><path d="M9.4 5V3.6h5.2V5"/><path d="M9 10.5h6M9 14h4"/>',
    'clock'     => '<circle cx="12" cy="12" r="8"/><path d="M12 7.4V12l3.2 2"/>',

    /* --- Sorting steps --------------------------------------------------- */
    'empty'     => '<path d="M8 4.5h8l-1 5.2a4 4 0 0 1-1.4 2.3v7.5h-3.2v-7.5A4 4 0 0 1 9 9.7z"/><path d="M17.6 17.4c1.4 0 2.4-1 2.4-2.2 0-1.6-1.6-2.2-1.3-4.6"/>',
    /* "Lepas" — a bottle cap lifted clear of the bottle, with motion marks. */
    'unlink'    => '<rect x="8.4" y="2.8" width="7.2" height="3.4" rx="1.2"/><path d="M9.6 10h4.8v2.4c0 1 .3 1.5.9 2.1.6.7 1 1.4 1 2.4v2.4a1.2 1.2 0 0 1-1.2 1.2H9.9a1.2 1.2 0 0 1-1.2-1.2v-2.4c0-1 .4-1.7 1-2.4.6-.6.9-1.1.9-2.1z"/><path d="m5.6 7.4-1.8-1.6M18.4 7.4l1.8-1.6"/>',
    'compress'  => '<path d="M6 4.4h12"/><path d="M6 19.6h12"/><path d="m9 8.4 3 3 3-3"/><path d="m9 15.6 3-3 3 3"/>',

    /* --- Status & controls ---------------------------------------------- */
    'check'     => '<path d="m5 12.8 4.4 4.4L19 7.6"/>',
    'check-circle' => '<circle cx="12" cy="12" r="8"/><path d="m8.4 12.2 2.6 2.6 4.6-5"/>',
    'x'         => '<path d="M6.4 6.4l11.2 11.2M17.6 6.4 6.4 17.6"/>',
    'plus'      => '<path d="M12 5.6v12.8M5.6 12h12.8"/>',
    'minus'     => '<path d="M5.6 12h12.8"/>',
    'lock'      => '<rect x="5.5" y="10.5" width="13" height="9.5" rx="2"/><path d="M8.4 10.5V8.2a3.6 3.6 0 0 1 7.2 0v2.3"/><path d="M12 14.2v2.4"/>',
    'info'      => '<circle cx="12" cy="12" r="8"/><path d="M12 11v5.4"/><circle cx="12" cy="8.2" r="0.9" fill="currentColor" stroke="none"/>',
    'alert'     => '<path d="M12 4.4 20.4 19H3.6z"/><path d="M12 9.6v4.2"/><circle cx="12" cy="16.4" r="0.9" fill="currentColor" stroke="none"/>',
    'filter'    => '<path d="M4.4 6.4h15.2M7 12h10M10 17.6h4"/>',
    'arrow-right'  => '<path d="M4.8 12h14.4"/><path d="m13.6 6.4 5.6 5.6-5.6 5.6"/>',
    'arrow-left'   => '<path d="M19.2 12H4.8"/><path d="m10.4 6.4-5.6 5.6 5.6 5.6"/>',
    'arrow-up'     => '<path d="M12 19.2V4.8"/><path d="m6.4 10.4 5.6-5.6 5.6 5.6"/>',
    'chevron-right'=> '<path d="m9.6 5.6 6.4 6.4-6.4 6.4"/>',
    'chevron-left' => '<path d="m14.4 5.6-6.4 6.4 6.4 6.4"/>',
    'chevron-down' => '<path d="m5.6 9.6 6.4 6.4 6.4-6.4"/>',
    'external'  => '<path d="M14 5h5v5"/><path d="M19 5l-7.4 7.4"/><path d="M17.4 14v4.2a1.4 1.4 0 0 1-1.4 1.4H5.8a1.4 1.4 0 0 1-1.4-1.4V8a1.4 1.4 0 0 1 1.4-1.4H10"/>',
    'edit'      => '<path d="M5 19h3.2L19 8.2a1.8 1.8 0 0 0-2.6-2.6L5.6 16.4z"/><path d="M14.8 7.4 17 9.6"/>',
    'logout'    => '<path d="M14 6.4V5a1.4 1.4 0 0 0-1.4-1.4H5.8A1.4 1.4 0 0 0 4.4 5v14a1.4 1.4 0 0 0 1.4 1.4h6.8A1.4 1.4 0 0 0 14 19v-1.4"/><path d="M9.6 12h10"/><path d="m16.2 8.6 3.4 3.4-3.4 3.4"/>',
];

$body = $paths[$name] ?? $paths['leaf'];
$classes = cx(['icon', $size !== '' ? 'icon--' . $size : '', $extra]);
?>
<svg class="<?= e($classes) ?>" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
     fill="none"<?= $stroke ? ' stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"' : ' fill="currentColor"' ?>
     <?php if ($label !== ''): ?>role="img" aria-label="<?= e($label) ?>"<?php else: ?>aria-hidden="true" focusable="false"<?php endif; ?>><?= $body ?></svg>
