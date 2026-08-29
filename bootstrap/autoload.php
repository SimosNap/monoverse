<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {

    $prefixes = [
        'Monoverse\\' => __DIR__ . '/../app/',
        'Modules\\'   => __DIR__ . '/../modules/',
    ];

    foreach ($prefixes as $prefix => $basePath) {

        if (strpos($class, $prefix) !== 0) {
            continue;
        }

        $relative = substr($class, strlen($prefix));

        $file = $basePath . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require $file;
        }

        return;
    }
});
