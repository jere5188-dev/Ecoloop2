<?php
/**
 * EcoLoop — view renderer.
 *
 * Pages are plain PHP files rendered into a shared layout. Components and
 * partials are included with an explicit props array so nothing leaks from the
 * surrounding scope (requirement R7.3 / R7.4).
 */

declare(strict_types=1);

namespace EcoLoop;

final class View
{
    /** @var array<string, mixed> Values shared with the layout (title, nav, user…). */
    private static array $shared = [];

    /** @param array<string, mixed> $data */
    public static function share(array $data): void
    {
        self::$shared = array_merge(self::$shared, $data);
    }

    public static function shared(string $key, mixed $default = null): mixed
    {
        return self::$shared[$key] ?? $default;
    }

    /**
     * Render a page inside the application layout.
     *
     * @param string               $page pages/<page>.php
     * @param array<string, mixed> $data extracted into the page scope
     * @param array<string, mixed> $meta title, subtitle, bodyClass, pageScripts…
     */
    public static function render(string $page, array $data = [], array $meta = []): void
    {
        $file = VIEW_PATH . '/pages/' . $page . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View page not found: {$page}");
        }

        self::share($meta);

        // Page body is captured first so it can influence nothing but $content.
        $content = self::capture($file, $data);

        $layout = VIEW_PATH . '/layouts/app.php';
        // Layout reads $content plus everything shared.
        $shared = self::$shared;
        (static function () use ($layout, $content, $shared): void {
            extract($shared, EXTR_SKIP);
            /** @var string $content */
            require $layout;
        })();
    }

    /**
     * @param array<string, mixed> $props
     */
    public static function component(string $name, array $props = []): void
    {
        $file = VIEW_PATH . '/components/' . $name . '.php';
        if (!is_file($file)) {
            if (APP_DEBUG) {
                echo '<!-- missing component: ' . e($name) . ' -->';
            }

            return;
        }
        (static function () use ($file, $props): void {
            require $file;
        })();
    }

    /**
     * @param array<string, mixed> $props
     */
    public static function partial(string $name, array $props = []): void
    {
        $file = VIEW_PATH . '/partials/' . $name . '.php';
        if (!is_file($file)) {
            if (APP_DEBUG) {
                echo '<!-- missing partial: ' . e($name) . ' -->';
            }

            return;
        }
        $shared = self::$shared;
        (static function () use ($file, $props, $shared): void {
            extract($shared, EXTR_SKIP);
            require $file;
        })();
    }

    /**
     * Include a file with an isolated scope and return its output.
     *
     * @param array<string, mixed> $data
     */
    private static function capture(string $file, array $data): string
    {
        $shared = self::$shared;
        ob_start();
        (static function () use ($file, $data, $shared): void {
            extract($shared, EXTR_SKIP);
            extract($data, EXTR_OVERWRITE);
            require $file;
        })();

        return (string) ob_get_clean();
    }

    /** Styled 404 / 405 page (requirement R7.7). */
    public static function notFound(string $path = '', int $status = 404): void
    {
        http_response_code($status);
        self::render('404', [
            'path'   => $path,
            'status' => $status,
        ], [
            'title'     => $status === 405 ? 'Method not allowed' : 'Page not found',
            'hideAside' => false,
        ]);
    }
}
