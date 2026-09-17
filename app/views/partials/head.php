<?php
/**
 * Document head: meta, Google Fonts, stylesheets.
 *
 * Shared vars: $title, $appName, $appTagline
 */
$pageTitle = shared('title', 'Dashboard');
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0F3D2E">
<meta name="color-scheme" content="light">
<meta name="description" content="EcoLoop — platform digital dan gamifikasi untuk konsumsi serta pengelolaan sampah berkelanjutan di kampus. Sort. Collect. Reward. Repeat.">
<meta name="format-detection" content="telephone=no">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?> — <?= e(APP_TAGLINE) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap">

<link rel="stylesheet" href="<?= e(asset('assets/css/tokens.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/layout.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/components.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/features.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/responsive.css')) ?>">

<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%230F3D2E'/><path d='M23 9c0 7-4.5 12-11 13 0-7 4.5-12 11-13Z' fill='%23A7C957'/><path d='M11 23c2-5 5-8 9-10' stroke='%230F3D2E' stroke-width='1.6' fill='none'/></svg>">
