<?php

namespace Lkt\Tools\Requiring;

if (!function_exists('\Lkt\Tools\Requiring\requireFiles')) {
    function requireFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $files = glob($path);
            if ($files === false) {
                throw new \RuntimeException("Failed to glob for function files");
            }
            foreach ($files as $file) {
                require_once $file;
            }
            unset($file);
            unset($files);
        }
    }
}