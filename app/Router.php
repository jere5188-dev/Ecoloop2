<?php
/**
 * EcoLoop — minimal request router.
 *
 * Maps a normalized request path + HTTP method to a callable. Deliberately tiny:
 * the platform has a fixed, flat set of pages (requirement R7.2).
 */

declare(strict_types=1);

namespace EcoLoop;

final class Router
{
    /** @var array<string, array<string, callable>> method => path => handler */
    private array $routes = ['GET' => [], 'POST' => []];

    /** @var callable|null */
    private $fallback = null;

    private static ?string $path = null;

    public function get(string $path, callable $handler): self
    {
        $this->routes['GET'][self::normalize($path)] = $handler;

        return $this;
    }

    public function post(string $path, callable $handler): self
    {
        $this->routes['POST'][self::normalize($path)] = $handler;

        return $this;
    }

    /** Handler used when no route matches (404). */
    public function fallback(callable $handler): self
    {
        $this->fallback = $handler;

        return $this;
    }

    /**
     * Normalize a path: strip the mount prefix and the query string, collapse
     * duplicate slashes, drop the trailing slash. "/pickup/" => "/pickup".
     */
    public static function normalize(string $path): string
    {
        $path = (string) parse_url($path, PHP_URL_PATH);
        $path = preg_replace('#/+#', '/', $path) ?? '/';

        $prefix = base_path_prefix();
        if ($prefix !== '' && str_starts_with($path, $prefix)) {
            $path = substr($path, strlen($prefix));
        }

        // Serving through `php -S` without a rewrite may expose /index.php.
        if (str_ends_with($path, '/index.php')) {
            $path = substr($path, 0, -strlen('index.php'));
        }

        $path = '/' . trim($path, '/');

        return $path;
    }

    /** The path currently being served — cached for the helpers. */
    public static function currentPath(): string
    {
        if (self::$path === null) {
            self::$path = self::normalize($_SERVER['REQUEST_URI'] ?? '/');
        }

        return self::$path;
    }

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /** Resolve and invoke the handler for the current request. */
    public function dispatch(): void
    {
        $path = self::currentPath();
        $method = self::method();
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null && $method === 'HEAD') {
            $handler = $this->routes['GET'][$path] ?? null;
        }

        if ($handler === null) {
            // Path exists but not for this verb -> 405, otherwise 404.
            $existsElsewhere = isset($this->routes['GET'][$path]) || isset($this->routes['POST'][$path]);
            http_response_code($existsElsewhere ? 405 : 404);
            if ($this->fallback !== null) {
                ($this->fallback)($path, $existsElsewhere ? 405 : 404);
            }

            return;
        }

        $handler();
    }
}
