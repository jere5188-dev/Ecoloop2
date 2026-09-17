<?php
/**
 * EcoLoop — front controller.
 *
 * Every request enters here. Run with:
 *   php -S localhost:8000 -t public
 */

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use EcoLoop\Controllers\PageController;
use EcoLoop\Controllers\PickupController;
use EcoLoop\Data\Nav;
use EcoLoop\Data\User;
use EcoLoop\Router;
use EcoLoop\View;

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

/* Values every layout/partial can rely on. */
View::share([
    'navItems'   => Nav::items(),
    'navBottom'  => Nav::bottom(),
    'authUser'   => User::current(),
    'appName'    => APP_NAME,
    'appTagline' => APP_TAGLINE,
]);

$pages = new PageController();
$pickup = new PickupController();

$router = new Router();

$router
    ->get('/', [$pages, 'dashboard'])
    ->get('/passbook', [$pages, 'passbook'])
    ->get('/impact', [$pages, 'impact'])
    ->get('/sorting', [$pages, 'sorting'])
    ->get('/competition', [$pages, 'gamify'])
    ->get('/profile', [$pages, 'profile'])
    ->get('/pickup', [$pickup, 'show'])
    ->post('/pickup', [$pickup, 'submit'])
    ->get('/pickup/reset', [$pickup, 'reset'])
    ->fallback(static function (string $path, int $status): void {
        View::notFound($path, $status);
    });

$router->dispatch();
