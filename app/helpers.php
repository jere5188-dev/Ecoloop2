<?php
/**
 * EcoLoop — global template helpers.
 *
 * Deliberately procedural: these are called constantly from view files, where
 * short names keep the markup readable.
 */

declare(strict_types=1);

if (!function_exists('e')) {
    /**
     * Escape a value for HTML output. Every dynamic value in a view goes
     * through this (requirement R7.6).
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('base_path_prefix')) {
    /**
     * The URL prefix the app is mounted under. Empty when served from the
     * document root (the normal `php -S localhost:8000 -t public` case).
     */
    function base_path_prefix(): string
    {
        static $prefix = null;
        if ($prefix !== null) {
            return $prefix;
        }
        $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
        $prefix = ($dir === '' || $dir === '.') ? '' : $dir;

        return $prefix;
    }
}

if (!function_exists('url')) {
    /** Build an application URL for a route path. */
    function url(string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');
        $url = base_path_prefix() . ($path === '/' ? '/' : rtrim($path, '/'));

        return $url === '' ? '/' : $url;
    }
}

if (!function_exists('asset')) {
    /** Build a URL for a static asset, cache-busted by file mtime. */
    function asset(string $path): string
    {
        $path = ltrim($path, '/');
        $url = base_path_prefix() . '/' . $path;
        $file = PUBLIC_PATH . '/' . $path;
        if (is_file($file)) {
            $url .= '?v=' . filemtime($file);
        }

        return $url;
    }
}

if (!function_exists('current_path')) {
    /** The normalized request path, e.g. "/passbook". */
    function current_path(): string
    {
        return \EcoLoop\Router::currentPath();
    }
}

if (!function_exists('is_active')) {
    /** Is the given route path the one being viewed? */
    function is_active(string $path): bool
    {
        $current = current_path();
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return $current === '/';
        }

        return $current === $path || str_starts_with($current, $path . '/');
    }
}

if (!function_exists('component')) {
    /**
     * Render a reusable view component.
     *
     * @param string               $name  file name inside views/components (no extension)
     * @param array<string, mixed> $props made available to the component as $props
     */
    function component(string $name, array $props = []): void
    {
        \EcoLoop\View::component($name, $props);
    }
}

if (!function_exists('partial')) {
    /**
     * Render a layout partial.
     *
     * @param array<string, mixed> $props
     */
    function partial(string $name, array $props = []): void
    {
        \EcoLoop\View::partial($name, $props);
    }
}

if (!function_exists('shared')) {
    /** Read a value shared with the layout (title, heading, nav, …). */
    function shared(string $key, mixed $default = null): mixed
    {
        return \EcoLoop\View::shared($key, $default);
    }
}

if (!function_exists('fmt_kg')) {
    /** "6.5" -> "6.5 KG" with one decimal place. */
    function fmt_kg(float|int $kg, bool $unit = true): string
    {
        $n = number_format((float) $kg, 1, '.', ',');

        return $unit ? $n . ' KG' : $n;
    }
}

if (!function_exists('fmt_pts')) {
    /** 2450 -> "2,450 pts" */
    function fmt_pts(float|int $points, bool $unit = true): string
    {
        $n = number_format((float) $points, 0, '.', ',');

        return $unit ? $n . ' pts' : $n;
    }
}

if (!function_exists('fmt_pct')) {
    /** 52.36 -> "52.4%" */
    function fmt_pct(float|int $pct, int $decimals = 1): string
    {
        return number_format((float) $pct, $decimals, '.', '') . '%';
    }
}

if (!function_exists('fmt_delta')) {
    /** Signed change label, e.g. "+2.3 kg" / "-0.4 kg". */
    function fmt_delta(float $value, string $unit = 'kg'): string
    {
        $sign = $value > 0 ? '+' : ($value < 0 ? '−' : '');

        return $sign . number_format(abs($value), 1, '.', ',') . ' ' . $unit;
    }
}

if (!function_exists('clamp_pct')) {
    /** Constrain a percentage to the 0–100 range for safe CSS widths. */
    function clamp_pct(float|int $value): float
    {
        return max(0.0, min(100.0, (float) $value));
    }
}

if (!function_exists('status_tone')) {
    /** Map a verification status key to a semantic tone class suffix. */
    function status_tone(string $status): string
    {
        return match (strtolower($status)) {
            'verified', 'terverifikasi', 'completed', 'done' => 'success',
            'pending', 'processing', 'menunggu'              => 'warning',
            'rejected', 'ditolak', 'failed'                  => 'error',
            default                                          => 'info',
        };
    }
}

if (!function_exists('old')) {
    /**
     * Previously submitted form value (used by the pickup wizard when a step is
     * re-rendered after a validation failure).
     */
    function old(array $data, string $key, mixed $default = ''): mixed
    {
        return $data[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /** Per-session token used to guard the pickup wizard POSTs. */
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(16));
        }

        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    /** Hidden input carrying the CSRF token. */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_valid')) {
    /** Validate a submitted CSRF token. */
    function csrf_valid(?string $token): bool
    {
        return is_string($token) && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }
}

if (!function_exists('redirect')) {
    /** Send a 302 to an application route and stop. */
    function redirect(string $path): never
    {
        header('Location: ' . url($path), true, 302);
        exit;
    }
}

if (!function_exists('attrs')) {
    /**
     * Build an HTML attribute string from a map. Null/false values are skipped,
     * true renders the bare attribute name.
     *
     * @param array<string, mixed> $map
     */
    function attrs(array $map): string
    {
        $out = [];
        foreach ($map as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            if ($value === true) {
                $out[] = e($key);
                continue;
            }
            $out[] = e($key) . '="' . e($value) . '"';
        }

        return $out ? ' ' . implode(' ', $out) : '';
    }
}

if (!function_exists('cx')) {
    /**
     * Join class names, dropping empties.
     *
     * @param array<int, string|null|false> $classes
     */
    function cx(array $classes): string
    {
        return implode(' ', array_filter($classes, static fn ($c) => is_string($c) && $c !== ''));
    }
}
