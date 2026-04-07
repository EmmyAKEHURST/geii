<?php

// Corrige SCRIPT_NAME si Apache annonce seulement "/index.php" alors que l’URL est du type /projet/public/
// (sinon Symfony voit un pathInfo du type "/geii/public/…" et aucune route "/" ne correspond).
if (isset($_SERVER['DOCUMENT_ROOT'], $_SERVER['SCRIPT_FILENAME'])) {
    $docRoot = realpath((string) $_SERVER['DOCUMENT_ROOT']);
    $scriptFile = realpath((string) $_SERVER['SCRIPT_FILENAME']);
    if ($docRoot && $scriptFile && str_starts_with($scriptFile, $docRoot)) {
        $relative = str_replace('\\', '/', substr($scriptFile, \strlen($docRoot)));
        if (str_ends_with($relative, '/index.php')) {
            $expected = $relative;
            $current = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
            if ($current !== $expected && str_ends_with($current, 'index.php')) {
                $_SERVER['SCRIPT_NAME'] = $expected;
            }
        }
    }
}

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
