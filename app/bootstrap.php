<?php
/**
 * EcoLoop — application bootstrap.
 *
 * Defines paths, starts the session, wires a lightweight autoloader for the
 * `EcoLoop\` namespace and pulls in the global template helpers.
 */

declare(strict_types=1);

define('ECOLOOP_START', microtime(true));
define('APP_PATH', dirname(__DIR__) . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', dirname(__DIR__) . '/public');
define('APP_NAME', 'EcoLoop');
define('APP_TAGLINE', 'Sort. Collect. Reward. Repeat.');
define('APP_VERSION', '1.0.0');

/* ---------------------------------------------------------------------------
 | Development error visibility. Flip APP_DEBUG to false for production.
 --------------------------------------------------------------------------- */
define('APP_DEBUG', true);
error_reporting(APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', APP_DEBUG ? '1' : '0');

/* ---------------------------------------------------------------------------
 | Session — holds the multi-step Waste Pickup wizard state.
 --------------------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_name('ecoloop_session');
    session_start();
}

/* ---------------------------------------------------------------------------
 | Autoloader: EcoLoop\Data\Waste  ->  app/Data/Waste.php
 --------------------------------------------------------------------------- */
spl_autoload_register(static function (string $class): void {
    $prefix = 'EcoLoop\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/helpers.php';
