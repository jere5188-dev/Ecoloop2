<?php
/**
 * EcoLoop — CLI request harness.
 *
 * Simulates a single HTTP request through the front controller so routes,
 * templates and the pickup wizard state machine can be smoke-tested without
 * running a web server.
 *
 * Usage:
 *   php tools/request.php GET  /passbook
 *   php tools/request.php POST /pickup "step=1&location=dormitory&contact=0812..."
 *
 * Set ECOLOOP_SID to share a session across several invocations.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$method = strtoupper($argv[1] ?? 'GET');
$uri = $argv[2] ?? '/';
$body = $argv[3] ?? '';

$parts = parse_url($uri);
$path = $parts['path'] ?? '/';
$query = $parts['query'] ?? '';

$_SERVER['REQUEST_METHOD'] = $method;
$_SERVER['REQUEST_URI'] = $uri;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

$_GET = [];
if ($query !== '') {
    parse_str($query, $_GET);
}

$_POST = [];
if ($method === 'POST' && $body !== '') {
    parse_str($body, $_POST);
}

$sid = getenv('ECOLOOP_SID');
if (is_string($sid) && $sid !== '') {
    $_COOKIE['ecoloop_session'] = $sid;
}

/**
 * The CLI SAPI discards headers, so redirects would be invisible. Every helper
 * in app/helpers.php is declared behind a function_exists() guard, which lets
 * the harness pre-define its own observable redirect().
 */
function redirect(string $path): never
{
    fwrite(STDERR, 'REDIRECT ' . $path . "\n");
    fwrite(STDERR, "STATUS 302\n");
    exit;
}

register_shutdown_function(static function (): void {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        fwrite(STDERR, "FATAL: {$err['message']} in {$err['file']}:{$err['line']}\n");
    }
    // http_response_code() returns false under CLI until something sets it.
    $code = http_response_code();
    fwrite(STDERR, 'STATUS ' . ($code === false ? 200 : $code) . "\n");
});

require dirname(__DIR__) . '/public/index.php';
